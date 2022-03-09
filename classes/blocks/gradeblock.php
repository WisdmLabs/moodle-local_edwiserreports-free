<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Reports abstract block will define here to which will extend for each repoers blocks
 *
 * @package     local_edwiserreports
 * @copyright   2021 wisdmlabs <support@wisdmlabs.com>
 * @author      Yogesh Shirsath
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports;

defined('MOODLE_INTERNAL') or die;

use local_edwiserreports\controller\authentication;
use moodle_url;
use cache;
use html_writer;
use core_text;

require_once($CFG->dirroot . '/local/edwiserreports/classes/block_base.php');

/**
 * Active users block.
 */
class gradeblock extends block_base {
    /**
     * Get the first site access data.
     *
     * @var null
     */
    public $firstsiteaccess;

    /**
     * Current time
     *
     * @var int
     */
    public $enddate;

    /**
     * Active users block labels
     *
     * @var array
     */
    public $labels;

    /**
     * No. of labels for active users.
     *
     * @var int
     */
    public $xlabelcount;

    /**
     * Cache
     *
     * @var object
     */
    public $cache;

    /**
     * Dates main array.
     *
     * @var array
     */
    public $dates = [];

    /**
     * Instantiate object
     *
     * @param int $blockid Block id
     */
    public function __construct($blockid = false) {
        parent::__construct($blockid);
        // Set cache for block.
        $this->sessioncache = cache::make('local_edwiserreports', 'grade_session');
    }

    /**
     * Preapre layout for each block
     * @return object Layout
     */
    public function get_layout() {
        global $CFG;

        // Layout related data.
        $this->layout->id = 'gradeblock';
        $this->layout->name = get_string('gradeheader', 'local_edwiserreports');
        $this->layout->info = get_string('gradeblockhelp', 'local_edwiserreports');
        $this->layout->morelink = new moodle_url($CFG->wwwroot . "/local/edwiserreports/grade.php");
        $this->layout->filters = $this->get_grade_filter();
        $this->layout->downloadlinks = $this->get_block_download_links();
        $this->layout->filter = '0-0';
        $this->layout->cohortid = 0;

        // Add block view in layout.
        $this->layout->blockview = $this->render_block('gradeblock', $this->block);

        // Set block edit capabilities.
        $this->set_block_edit_capabilities($this->layout->id);

        // Return blocks layout.
        return $this->layout;
    }

    /**
     * Prepare active users block filters
     * @param  $onlycourses Return only courses dropdown for current user.
     * @return array filters array
     */
    public function get_grade_filter($onlycourses = false) {
        global $OUTPUT, $USER, $COURSE, $USER, $DB;

        $courses = $this->get_courses_of_user($USER->id);

        unset($courses[$COURSE->id]);

        $users = $this->get_users_of_courses($USER->id, $courses);

        array_unshift($users, (object)[
            'id' => 0,
            'fullname' => get_string('allusers', 'search')
        ]);

        array_unshift($courses, (object)[
            'id' => 0,
            'fullname' => get_string('fulllistofcourses')
        ]);

        // Return only courses array if $onlycourses is true.
        if ($onlycourses == true) {
            return $courses;
        }
        return $OUTPUT->render_from_template('local_edwiserreports/gradeblockfilters', [
            'courses' => $courses,
            'students' => $users
        ]);
    }

    /**
     * Generate cache key for blocks
     * @param  string $blockname Block name
     * @param  string $filter    Filter
     * @param  int    $cohortid  Cohort id
     * @return string            Cache key
     */
    public function generate_cache_key($blockname, $filter, $cohortid = 0) {
        $cachekey = $blockname . "-" . $filter . "-";

        if ($cohortid) {
            $cachekey .= $cohortid;
        } else {
            $cachekey .= "all";
        }

        return $cachekey;
    }

    /**
     * Get grade scores for pie chart based on query.
     *
     * @param array  $gradescores   Default grade scores
     * @param string $sql           SQL query
     * @param array  $params        Parameters for SQL query
     *
     * @return array
     */
    private function get_grade_scores($gradescores, $sql, $params) {
        global $DB;
        $grades = $DB->get_recordset_sql($sql, $params);
        if (!$grades->valid()) {
            return [$gradescores, 0];
        }
        $total = 0;
        $count = 0;
        foreach ($grades as $grade) {
            $count++;
            $total += $grade->grade;
            switch(true) {
                case $grade->grade === NULL || $grade->grade <= 20:
                    $index = '0% - 20%';
                    break;
                case $grade->grade <= 40:
                    $index = '21% - 40%';
                    break;
                case $grade->grade <= 60:
                    $index = '41% - 60%';
                    break;
                case $grade->grade <= 80;
                    $index = '61% - 80%';
                    break;
                default:
                    $index = '81% - 100%';
                    break;
            }
            $gradescores[$index]++;
        }
        return [$gradescores, $total == 0 ? 0 : $total / $count];
    }

    /**
     * Get pie chart data
     *
     * @param object $filter Block filters
     *
     * @return array
     */
    public function get_graph_data($filter) {

        $userid = $filter->student;
        $course = $filter->course;

        $cachekey = $this->generate_cache_key('grade', $course . '-' . $userid);

        if (!$response = $this->sessioncache->get($cachekey)) {
            if ($course == 0) {
                $courses = $this->get_courses_of_user($this->get_current_user());
            } else {
                $courses = [$course => 'Dummy'];
            }

            // Default grade scores.
            $gradescores = [
                '0% - 20%' => 0,
                '21% - 40%' => 0,
                '41% - 60%' => 0,
                '61% - 80%' => 0,
                '81% - 100%' => 0
            ];

            if ($course == 0) {
                // Temporary course table.
                $coursetable = 'tmp_stengage_courses';
                // Creating temporary table.
                utility::create_temp_table($coursetable, array_keys($courses));
            }
            switch (true) {
                case $course == 0 && $userid == 0:
                    // Students grade categories.
                    $sql = "SELECT (gg.finalgrade / gg.rawgrademax * 100) grade
                              FROM {{$coursetable}} ct
                              JOIN {grade_items} gi ON ct.tempid = gi.iteminstance
                              JOIN {grade_grades} gg ON gi.id = gg.itemid
                             WHERE gi.itemtype = :itemtype";
                    $params = ['itemtype' => 'course'];
                    $header = get_string('studentgrades', 'local_edwiserreports');
                    break;
                case $course != 0 && $userid == 0:
                    // Students grade categories.
                    $sql = "SELECT (gg.finalgrade / gg.rawgrademax * 100) grade
                              FROM {grade_items} gi
                              JOIN {grade_grades} gg ON gi.id = gg.itemid
                             WHERE gi.itemtype = :itemtype
                               AND gi.courseid = :course";
                    $params = ['itemtype' => 'course', 'course' => $course];
                    $header = get_string('studentgrades', 'local_edwiserreports');
                    break;
                case $course == 0 && $userid != 0:
                    // Courses grade categories.
                    $sql = "SELECT (gg.finalgrade / gg.rawgrademax * 100) grade
                              FROM {{$coursetable}} ct
                              JOIN {grade_items} gi ON ct.tempid = gi.iteminstance
                              JOIN {grade_grades} gg ON gi.id = gg.itemid
                             WHERE gi.itemtype = :itemtype
                               AND gg.userid = :userid";
                    $params = ['itemtype' => 'course', 'userid' => $userid];
                    $header = get_string('coursegrades', 'local_edwiserreports');
                    break;
                case $course != 0 && $userid != 0:
                    // Activity grade categories.
                    $sql = "SELECT (gg.finalgrade / gg.rawgrademax * 100) grade
                              FROM {grade_items} gi
                              JOIN {grade_grades} gg ON gi.id = gg.itemid
                             WHERE gi.itemtype = :itemtype
                               AND gg.userid = :userid
                               AND gi.courseid = :course";
                    $params = ['itemtype' => 'mod', 'userid' => $userid, 'course' => $course];
                    $header = get_string('activitygrades', 'local_edwiserreports');
                    break;
            }
            [$gradescores, $average] = $this->get_grade_scores($gradescores, $sql, $params);
            $labels = array_keys($gradescores);
            $grades = array_values($gradescores);
            if (array_sum($grades) == 0) {
                $grades = [];
                $labels = [];
            }
            if ($course == 0) {
                // Drop temporary table.
                utility::drop_temp_table($coursetable);
            }
            $response = [
                'labels' => $labels,
                'grades' => $grades,
                'header' => $header,
                'average' => $average
            ];
            $this->sessioncache->set($cachekey, $response);
        }
        return $response;
    }

    /**
     * Prepare rows for data export
     *
     * @param recordset $records    SQL Record set
     * @param string    $header     Export data header
     * @param array     $values     Values to fetch from recordset
     * @param array     $rows       Array of rows
     *
     * @return array Array of rows
     */
    private function prepare_rows($records, $header, $values, $rows) {
        $rows[] = $header;
        if (!$records->valid()) {
            return $rows;
        }

        foreach ($records as $record) {
            $row = [];
            foreach ($values as $value) {
                $row[] = $value == 'grade' ? round($record->$value, 2) . '%' : $record->$value;
            }
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * Get additional filename content for export
     *
     * @param string $filter Filter object
     *
     * @return string
     */
    public function get_exportable_data_block_file_postfix($filter) {
        global $DB;
        $filename = '';
        list($courseid, $userid) = explode('-', $filter);
        if ($courseid != 0 && $course = $DB->get_record('course', array('id' => $courseid))) {
            $filename .= '-' . clean_param($course->fullname, PARAM_FILE);
        }
        if ($userid != 0 && $user = $DB->get_record('user', array('id' => $userid))) {
            $filename .= '-' . clean_param(fullname($user), PARAM_FILE);
        }
        return $filename;
    }

    /**
     * Get exportable data for block
     *
     * @param string $filter Filter string
     *
     * @return array
     */
    public function get_exportable_data_block($filter) {
        global $DB;

        list($course, $userid) = explode('-', $filter);

        if ($course == 0) {
            $courses = $this->get_courses_of_user($this->get_current_user());
        } else {
            $courses = [$course => 'Dummy'];
        }

        if ($course == 0) {
            // Temporary course table.
            $coursetable = 'tmp_stengage_courses';
            // Creating temporary table.
            utility::create_temp_table($coursetable, array_keys($courses));
        }

        $fullname = $DB->sql_fullname("u.firstname", "u.lastname");

        $rows = [];
        switch (true) {
            case $course == 0 && $userid == 0:
                // Students grade categories.
                $values = ['fullname', 'course', 'grade'];
                $header = [
                    get_string('name'),
                    get_string('course'),
                    get_string('grade', 'core_grades')
                ];
                $sql = "SELECT $fullname fullname, c.fullname course, (gg.finalgrade / gg.rawgrademax * 100) grade
                          FROM {{$coursetable}} ct
                          JOIN {course} c ON ct.tempid = c.id
                          JOIN {grade_items} gi ON c.id = gi.iteminstance
                          JOIN {grade_grades} gg ON gi.id = gg.itemid
                          JOIN {user} u ON gg.userid = u.id
                         WHERE gi.itemtype = :itemtype";
                $params = ['itemtype' => 'course'];
                break;
            case $course != 0 && $userid == 0:
                // Students grade categories.
                $values = ['fullname', 'grade'];
                $header = [
                    get_string('name'),
                    get_string('grade', 'core_grades')
                ];
                $sql = "SELECT $fullname fullname, (gg.finalgrade / gg.rawgrademax * 100) grade
                          FROM {grade_items} gi
                          JOIN {course} c ON gi.courseid = c.id
                          JOIN {grade_grades} gg ON gi.id = gg.itemid
                          JOIN {user} u ON gg.userid = u.id
                         WHERE gi.itemtype = :itemtype
                           AND gi.courseid = :course";
                $params = ['itemtype' => 'course', 'course' => $course];
                break;
            case $course == 0 && $userid != 0:
                // Courses grade categories.
                $values = ['course', 'grade'];
                $header = [
                    get_string('course'),
                    get_string('grade', 'core_grades')
                ];
                $sql = "SELECT c.fullname course, (gg.finalgrade / gg.rawgrademax * 100) grade
                          FROM {{$coursetable}} ct
                          JOIN {course} c ON ct.tempid = c.id
                          JOIN {grade_items} gi ON c.id = gi.iteminstance
                          JOIN {grade_grades} gg ON gi.id = gg.itemid
                         WHERE gi.itemtype = :itemtype
                           AND gg.userid = :userid";
                $params = ['itemtype' => 'course', 'userid' => $userid];
                break;
            case $course != 0 && $userid != 0:
                // Activity grade categories.
                $sql = "SELECT cm.id, (gg.finalgrade / gg.rawgrademax * 100) grade
                          FROM {grade_items} gi
                          JOIN {grade_grades} gg ON gi.id = gg.itemid
                          JOIN {course_modules} cm ON gi.iteminstance = cm.instance
                          JOIN {modules} m ON cm.module = m.id AND gi.itemmodule = m.name
                         WHERE gi.itemtype = :itemtype
                           AND gg.userid = :userid
                           AND gi.courseid = :course
                           AND cm.course = :course1";
                $params = [
                    'itemtype' => 'mod',
                    'userid' => $userid,
                    'course' => $course,
                    'course1' => $course
                ];
                $rows[] = [
                    get_string('activity'),
                    get_string('grade', 'core_grades')
                ];
                $records = $DB->get_recordset_sql($sql, $params);
                $cms = get_fast_modinfo($course)->get_cms();
                if (!$records->valid()) {
                    return $rows;
                }
                foreach ($records as $record) {
                    if (isset($cms[$record->id])) {
                        $rows[] = [
                            $cms[$record->id]->get_name(),
                            round($record->grade, 2) . '%'
                        ];
                    }
                }
                return $rows;
        }

        $records = $DB->get_recordset_sql($sql, $params);

        if ($course == 0) {
            // Drop temporary table.
            utility::drop_temp_table($coursetable);
        }

        $rows = $this->prepare_rows($records, $header, $values, $rows);

        return $rows;
    }

    /**
     * Get users for more details table.
     *
     * @param int       $cohort         Cohort id
     * @param array     $coursetable    Courses table name
     * @param string    $search         Search query
     * @param int       $start          Starting row index of page
     * @param int       $length         Number of rows per page
     * @param object    $ordering       Order by column
     *
     * @return array
     */
    private function get_grade_table_data($cohort, $coursetable, $search, $start, $length, $ordering) {
        global $DB;

        $params = [
            'contextlevel' => CONTEXT_COURSE,
            'archetype' => 'student',
            'itemtype' => 'mod'
        ];

        $searchquery = '';
        $fullname = $DB->sql_fullname("u.firstname", "u.lastname");
        $ordercolumn = [$fullname, 'c.fullname', '', 'gg.finalgrade'][$ordering->column];
        $order = $ordering->dir;
        if (trim($search) !== '') {
            $params['search'] = "%$search%";
            $searchquery = 'AND ' . $DB->sql_like($fullname, ':search');
        }

        // If cohort ID is there then add cohort filter in sqlquery.
        $sqlcohort = "";
        if ($cohort) {
            $sqlcohort .= "JOIN {cohort_members} cohortm ON cohortm.userid = u.id AND cohortm.cohortid = :cohortid";
            $params["cohortid"] = $cohort;
        }

        $sql = "SELECT u.id, $fullname student, c.fullname coursename, c.id courseid, cm.id cmid, gg.finalgrade grade
                  FROM {{$coursetable}} ct
                  JOIN {context} ctx ON ct.tempid = ctx.instanceid
                  JOIN {role_assignments} ra ON ctx.id = ra.contextid
                  JOIN {role} r ON ra.roleid = r.id
                  JOIN {user} u ON ra.userid = u.id
                  $sqlcohort
                  JOIN {course} c ON ctx.instanceid = c.id
                  JOIN {grade_items} gi ON c.id = gi.courseid AND gi.itemtype = :itemtype
                  JOIN {grade_grades} gg ON gi.id = gg.itemid AND u.id = gg.userid
                  JOIN {course_modules} cm ON gi.iteminstance = cm.instance AND gi.courseid = cm.course
                  JOIN {modules} m ON cm.module = m.id AND gi.itemmodule = m.name
                 WHERE ctx.contextlevel = :contextlevel
                   AND r.archetype = :archetype
                   $searchquery
                 ORDER BY $ordercolumn $order";
        $users = $DB->get_recordset_sql($sql, $params, $start, $length);

        $countsql = "SELECT count(u.id)
                       FROM {{$coursetable}} ct
                       JOIN {context} ctx ON ct.tempid = ctx.instanceid
                       JOIN {role_assignments} ra ON ctx.id = ra.contextid
                       JOIN {role} r ON ra.roleid = r.id
                       JOIN {user} u ON ra.userid = u.id
                       $sqlcohort
                       JOIN {course} c ON ctx.instanceid = c.id
                       JOIN {grade_items} gi ON c.id = gi.courseid AND gi.itemtype = :itemtype
                       JOIN {grade_grades} gg ON gi.id = gg.itemid AND u.id = gg.userid
                       JOIN {course_modules} cm ON gi.iteminstance = cm.instance AND gi.courseid = cm.course
                       JOIN {modules} m ON cm.module = m.id AND gi.itemmodule = m.name
                      WHERE ctx.contextlevel = :contextlevel
                        AND r.archetype = :archetype
                        $searchquery";
        $count = $DB->count_records_sql($countsql, $params);
        return [$users, $count];
    }

    /**
     * Get grades for more details table.
     *
     * @param object $filter Filter object
     *
     * @return array
     */
    public function get_table_data($filter) {
        global $COURSE;

        $cohort = (int)$filter->cohort;
        $course = (int)$filter->course;
        $search = $filter->search;
        $start = (int)$filter->start;
        $length = (int)$filter->length;
        $ordering = $filter->ordering;
        $secret = optional_param('secret', null, PARAM_TEXT);

        $authentication = new authentication();
        $userid = $authentication->get_user($secret);

        if ($course === 0) {
            $courses = $this->get_courses_of_user($userid);
            unset($courses[$COURSE->id]);
            $courses = array_keys($courses);
        } else {
            $courses = [$course];
        }

        // Temporary course table.
        $coursetable = 'tmp_stengage_courses';
        // Creating temporary table.
        utility::create_temp_table($coursetable, $courses);

        list($users, $count) = $this->get_grade_table_data(
            $cohort,
            $coursetable,
            $search,
            $start,
            $length,
            $ordering
        );

        $records = [];

        if ($users->valid()) {
            foreach ($users as $user) {
                $subcoursename = $user->coursename > 30 ? core_text::substr($user->coursename, 0, 30) . '...' : $user->coursename;
                $records[] = [
                    'student' => $user->student,
                    'course' => html_writer::tag('a',  $subcoursename, [
                        'href' => new moodle_url('/course/view.php', ['id' => $user->courseid]),
                        'title' => $user->coursename,
                        'target' => '_blank'
                    ]),
                    'activity' => get_fast_modinfo($user->courseid)->cms[$user->cmid]->get_name(),
                    'grade' => round($user->grade, 2) . '%'
                ];
            }
        }

        // Droppping course table.
        utility::drop_temp_table($coursetable);

        return [
            "data" => $records,
            "recordsTotal" => $count,
            "recordsFiltered" => $count
        ];
    }

    /**
     * Get exportable data for more details page.
     *
     * @param string $filter Filter object
     *
     * @return array
     */
    public static function get_exportable_data_report($filter) {
        global $USER, $COURSE;

        list($course, $cohort, $search, $column, $dir) = explode('-', $filter);

        $userid = $USER->id;
        $course = (int)$course;
        $cohort = (int)$cohort;
        $ordering = (object)[
            'column' => $column,
            'dir' => $dir
        ];

        $obj = new self();

        if ($course === 0) {
            $courses = $obj->get_courses_of_user($userid);
            unset($courses[$COURSE->id]);
            $courses = array_keys($courses);
        } else {
            $courses = [$course];
        }

        // Temporary course table.
        $coursetable = 'tmp_stengage_courses';
        // Creating temporary table.
        utility::create_temp_table($coursetable, $courses);

        // Get
        list($users) = $obj->get_grade_table_data(
            $cohort,
            $coursetable,
            $search,
            0,
            0,
            $ordering
        );

        $records = [[
            get_string('student', 'core_grades'),
            get_string('course'),
            get_string('activity'),
            get_string('grade', 'core_grades')
        ]];

        if ($users->valid()) {
            foreach ($users as $user) {
                $records[] = [
                    $user->student,
                    $user->coursename,
                    get_fast_modinfo($user->courseid)->cms[$user->cmid]->get_name(),
                    round($user->grade, 2) . '%'
                ];
            }
        }

        // Droppping course table.
        utility::drop_temp_table($coursetable);

        return $records;
    }

}
