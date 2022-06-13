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
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports;

defined('MOODLE_INTERNAL') || die;

use stdClass;
use moodle_url;
use cache;
use context_course;
use html_writer;
use html_table;
use core_user;
use context;

require_once($CFG->dirroot . '/local/edwiserreports/classes/block_base.php');

/**
 * Course progress block.
 */
class courseprogressblock extends block_base {
    /**
     * Get reports data for Course Progress block
     * @param  object $params Parameters
     * @return object         Response object
     */
    public function get_data($params = false) {
        $courseid = isset($params->courseid) ? $params->courseid : false;
        $cohortid = isset($params->cohortid) ? $params->cohortid : false;

        if (isset($params->tabledata) && $params->tabledata == true) {
            $tabledata = true;
        } else {
            $tabledata = false;
        }

        // Make cache for courseprogress block.
        $cache = cache::make("local_edwiserreports", "courseprogress");
        $cachekey = $this->generate_cache_key('courseprogress', $courseid, $cohortid);

        // If cache not set for course progress.
        if ((!$response = $cache->get($cachekey)) || $tabledata) {

            // Get all courses for dropdown.
            $course = get_course($courseid);
            $coursecontext = context_course::instance($courseid);

            // Get only students.
            $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');

            // Get response.
            $response = new stdClass();
            list($progress, $average) = $this->get_completion_with_percentage(
                $course,
                $enrolledstudents,
                $tabledata
            );
            $response->data = $progress;
            $response->average = $average;
            $response->tooltip = [
                'single' => get_string('student', 'core_grades'),
                'plural' => get_string('students')
            ];

            // Set cache to get data for course progress.
            $cache->set($cachekey, $response);
        }

        $upgradelink = '';
        if (is_siteadmin($this->get_current_user())) {
            $upgradelink = UPGRADE_URL;
        }

        // Insight.
        $response->insight = [
            'insight' => [
                'value' => '??',
                'title' => 'averagecourseprogress'
            ],
            'pro' => $this->image_icon('lock'),
            'upgradelink' => $upgradelink
        ];

        // Return response.
        return $response;
    }

    /**
     * Preapre layout for each block
     * @return object Response object
     */
    public function get_layout() {
        global $CFG;

        // Layout related data.
        $this->layout->id = 'courseprogressblock';
        $this->layout->name = get_string('courseprogress', 'local_edwiserreports');
        $this->layout->info = get_string('courseprogressblockhelp', 'local_edwiserreports');
        $this->layout->morelink = new moodle_url($CFG->wwwroot . "/local/edwiserreports/coursereport.php");
        $this->layout->downloadlinks = $this->get_block_download_links();
        $this->layout->filters = $this->get_courseprogress_filter();

        // Add block view in layout.
        $this->layout->blockview = $this->render_block('courseprogressblock', $this->block);

        // Set block edit capabilities.
        $this->set_block_edit_capabilities($this->layout->id);

        // Return blocks layout.
        return $this->layout;
    }

    /**
     * Prepare active users block filters
     * @return array filters array
     */
    public function get_courseprogress_filter() {
        global $OUTPUT, $USER, $COURSE, $USER, $DB;

        $courses = $this->get_courses_of_user($USER->id);

        unset($courses[$COURSE->id]);

        $this->block->hascourses = count($courses) > 0;

        if (!$this->block->hascourses) {
            return '';
        }

        return $OUTPUT->render_from_template('local_edwiserreports/courseprogressblockfilters', [
            'courses' => array_values($courses)
        ]);
    }

    /**
     * Get completion with percentage
     * (0%, 20%, 40%, 60%, 80%, 100%)
     * @param  object $course   Course Object
     * @param  array  $users    Users Object
     * @return array            Array of completion with percentage
     */
    public function get_completion_with_percentage($course, $users, $tabledata = false) {
        $completions = \local_edwiserreports\utility::get_course_completion($course->id);

        // Default grade scores.
        $completedusers = [
            '0% - 20%' => 0,
            '21% - 40%' => 0,
            '41% - 60%' => 0,
            '61% - 80%' => 0,
            '81% - 100%' => 0
        ];

        $completed = 0;
        $total = 0;
        $count = 0;
        foreach ($users as $user) {
            $count++;
            // If not set the completion then this user is not completed.
            if (!isset($completions[$user->id])) {
                $completedusers['0% - 20%']++;
            } else {
                $progress = $completions[$user->id]->completion;
                $total += $progress;
                switch(true) {
                    case $progress <= 20:
                        $completedusers['0% - 20%']++;
                        break;
                    case $progress <= 40:
                        $completedusers['21% - 40%']++;
                        break;
                    case $progress <= 60:
                        $completedusers['41% - 60%']++;
                        break;
                    case $progress <= 80;
                        $completedusers['61% - 80%']++;
                        break;
                    default:
                        $completedusers['81% - 100%']++;
                        break;
                }
                if ($progress == 100) {
                    $completed++;
                }
            }
        }
        if ($tabledata) {
            $completedusers['completed'] = $completed;
        }
        return [array_values($completedusers), $total == 0 ? 0 : $total / $count];
    }

    /**
     * Get headers for exportable data for blocks
     * @return [array] Array of header
     */
    public static function get_header() {
        $header = array(
            get_string("name", "local_edwiserreports"),
            get_string("email", "local_edwiserreports"),
            get_string("coursename", "local_edwiserreports"),
            get_string("completedactivity", "local_edwiserreports"),
            get_string("completions", "local_edwiserreports")
        );
        return $header;
    }

    /**
     * Get header for report page for course progress
     * @return [array] Array of headers in report
     */
    public static function get_header_report() {
        $header = array(
            get_string("coursename", "local_edwiserreports"),
            get_string("enrolled", "local_edwiserreports"),
            get_string("completed", "local_edwiserreports"),
            '81% - 100%',
            '61% - 80%',
            '41% - 60%',
            '21% - 40%',
            '0% - 20%'
        );
        return $header;
    }

    /**
     * Get Course List
     * @param  int   $cohortid Cohort ID
     * @return array           Object of course list
     */
    public function get_courselist($cohortid) {
        global $COURSE, $CFG;

        $courses = $this->get_courses_of_user();
        unset($courses[$COURSE->id]);

        $response = array();
        foreach ($courses as $course) {
            // Generate response object for a course.
            $completedusers = [
                '0% - 20%' => 0,
                '21% - 40%' => 0,
                '41% - 60%' => 0,
                '61% - 80%' => 0,
                '81% - 100%' => 0,
                'completed' => 0
            ];
            $enrolments = 0;

            $res = new stdClass();

            // Assign response data.
            $res->id = $course->id;
            $res->coursename = html_writer::link(
                new moodle_url(
                    "/local/edwiserreports/completion.php",
                    array(
                        "courseid" => $course->id,
                        'backurl' => new moodle_url('/local/edwiserreports/coursereport.php#progress')
                    )
                ),
                $course->fullname
            );

            // Get only enrolled student.
            $enrolledstudents = \local_edwiserreports\utility::get_enrolled_students(
                $course->id,
                false,
                $cohortid
            );
            if (!count($enrolledstudents) && !is_siteadmin()) {
                continue;
            }
            // Get completions.
            $compobj = new \local_edwiserreports\completions();
            $completions = $compobj->get_course_completions($course->id);

            // For each enrolled student get completions.
            foreach ($enrolledstudents as $user) {
                // Generate $key to save completion in an array.
                if (!isset($completions[$user->id])) {
                    // Completed 0% of course.
                    $completedusers['0% - 20%']++;
                } else {
                    // Calculated progress percantage.
                    $progress = $completions[$user->id]->completion;

                    switch(true) {
                        case $progress <= 20:
                            $completedusers['0% - 20%']++;
                            break;
                        case $progress <= 40:
                            $completedusers['21% - 40%']++;
                            break;
                        case $progress <= 60:
                            $completedusers['41% - 60%']++;
                            break;
                        case $progress <= 80;
                            $completedusers['61% - 80%']++;
                            break;
                        case $progress < 100;
                            $completedusers['81% - 100%']++;
                            break;
                        default:
                            $completedusers['81% - 100%']++;
                            $completedusers['completed']++;
                            break;
                    }
                }

                // Increament enrolment count.
                $enrolments++;
            }

            $courseid = $course->id;
            $coursename = $course->fullname;
            $res->completed0to20 = self::get_userlist_popup_link(
                $courseid,
                $coursename,
                $completedusers['0% - 20%'],
                'completed0to20',
                '0',
                '20'
            );
            $res->completed21to40 = self::get_userlist_popup_link(
                $courseid,
                $coursename,
                $completedusers['21% - 40%'],
                'completed21to40',
                '21',
                '40'
            );
            $res->completed41to60 = self::get_userlist_popup_link(
                $courseid,
                $coursename,
                $completedusers['41% - 60%'],
                'completed41to60',
                '41',
                '60'
            );
            $res->completed61to80 = self::get_userlist_popup_link(
                $courseid,
                $coursename,
                $completedusers['61% - 80%'],
                'completed61to80',
                '61',
                '80'
            );
            $res->completed81to100 = self::get_userlist_popup_link(
                $courseid,
                $coursename,
                $completedusers['81% - 100%'],
                'completed81to100',
                '81',
                '100'
            );
            $res->completed = self::get_userlist_popup_link(
                $courseid,
                $coursename,
                $completedusers['completed'],
                'completed',
                '100',
                '100'
            );
            $res->enrolments = self::get_userlist_popup_link(
                $courseid,
                $coursename,
                $enrolments,
                'enrolments',
                '-1',
                '100'
            );

            // Added response object in response array.
            $response[] = $res;
        }
        // Return response.
        return $response;
    }

    /**
     * Prepare userslistpopup link
     *
     * @param  int    $courseid   Course id
     * @param  string $coursename Course name
     * @param  string $value      Value for link
     * @param  string $action     Action for link
     * @param  int    $minval     Minimum value for popup
     * @param  int    $maxval     Maximum value for popup
     * @return string             HTML link content
     */
    public static function get_userlist_popup_link($courseid, $coursename, $value, $action, $minval, $maxval) {
        $url = new moodle_url('javascript:void(0)');
        $class = 'modal-trigger text-decoration-none';
        return html_writer::link(
            $url,
            $value,
            array(
                'class' => $class,
                'data-action' => $action,
                'data-minvalue' => $minval,
                'data-maxvalue' => $maxval,
                'data-courseid' => $courseid,
                'data-coursename' => $coursename
            )
        );
    }

    /**
     * Get Users List Table
     * @param  int    $courseid Course ID
     * @param  int    $minval   Minimum Progress Value
     * @param  int    $maxval   Maximum Progress Value
     * @param  int    $cohortid Cohort id
     * @return string           HTML content
     */
    public static function get_userslist_table($courseid, $minval, $maxval, $cohortid) {
        global $OUTPUT;
        $context = new stdClass;
        $context->searchicon = \local_edwiserreports\utility::image_icon('actions/search');
        $context->placeholder = get_string('searchuser', 'local_edwiserreports');
        $context->length = [10, 25, 50, 100];
        $filter = $OUTPUT->render_from_template('local_edwiserreports/common-table-search-filter', $context);

        $table = new html_table();
        $table->head = array(
            get_string("fullname", "local_edwiserreports"),
            get_string("email", "local_edwiserreports")
        );
        $table->attributes["class"] = "modal-table table";
        $table->attributes["style"] = "min-width: 100%;";

        $data = self::get_userslist($courseid, $minval, $maxval, $cohortid);
        if (!empty($data)) {
            $table->data = $data;
        }
        return $filter . html_writer::table($table);
    }

    /**
     * Get Users list
     * @param  int   $courseid Course ID
     * @param  int   $minval   Minimum Progress Value
     * @param  int   $maxval   Maximum Progress Value
     * @param  int   $cohortid Cohort id
     * @return array           Users Data Array
     */
    public static function get_userslist($courseid, $minval, $maxval, $cohortid) {
        global $DB;

        $enrolledstudents = \local_edwiserreports\utility::get_enrolled_students(
            $courseid,
            false,
            $cohortid
        );

        // Get completions.
        $completions = $DB->get_records_sql(
            'SELECT userid, progress as completion, completiontime
               FROM {edwreports_course_progress}
              WHERE courseid = :courseid
                AND progress >= :minval
                AND progress <= :maxval',
            array(
                'courseid' => $courseid,
                'minval' => $minval,
                'maxval' => $maxval
            )
        );

        $usersdata = array();
        foreach ($enrolledstudents as $enrolleduser) {

            if (isset($completions[$enrolleduser->id])) {
                $user = core_user::get_user($enrolleduser->id);
                $usersdata[] = array(
                    fullname($user),
                    $user->email
                );
            }
        }

        return $usersdata;
    }

    /**
     * Get Exportable data for Course Progress Block
     * @param  string $filter Filter to get data from specific range
     * @return array          Array of exportable data
     */
    public function get_exportable_data_block($filter) {
        $export = array();
        $export[] = self::get_header();
        $course = get_course($filter);
        $enrolledstudents = \local_edwiserreports\utility::get_enrolled_students($course->id, false);
        foreach ($enrolledstudents as $student) {
            $completion = \local_edwiserreports\utility::get_course_completion_info($course, $student->id);
            $completed = $completion["completedactivities"] . "/" . $completion["totalactivities"];
            $export[] = array(
                fullname($student),
                $student->email,
                $course->fullname,
                $completed,
                $completion["progresspercentage"] . "%"
            );
        }

        return $export;
    }

    /**
     * Get Exportable data for Active Users Page
     * @return array          Array of exportable data
     */
    public static function get_exportable_data_report() {
        global $COURSE;

        $cohortid = optional_param("cohortid", 0, PARAM_INT);
        $export = array();
        $export[] = self::get_header_report();

        $blockobj = new self();
        $courses = $blockobj->get_courses_of_user();
        unset($courses[$COURSE->id]);
        $params = (object) array(
            'cohortid' => $cohortid,
            'tabledata' => true
        );
        foreach ($courses as $course) {
            $blockobj = new self();
            $params->courseid = $course->id;
            $courseprogress = $blockobj->get_data($params);
            $enrolledstudents = \local_edwiserreports\utility::get_enrolled_students($course->id, false, $cohortid);

            $export[] = array_merge(
                array(
                    $course->fullname,
                    count($enrolledstudents)
                ),
                array_reverse($courseprogress->data)
            );
        }

        return $export;
    }
    /**
     * Returns list of users enrolled into course.
     *
     * @param context $context
     * @param string $withcapability
     * @param int $groupid 0 means ignore groups, USERSWITHOUTGROUP without any group and any other value limits
     * the result by group id
     * @param string $userfields requested user record fields
     * @param string $orderby
     * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @param bool $onlyactive consider only active enrolments in enabled plugins and time restrictions
     * @return array of user records
     */
    public static function rep_get_enrolled_users(
        context $context,
        $withcapability = '',
        $groupid = 0,
        $userfields = 'u.*',
        $orderby = null,
        $limitfrom = 0,
        $limitnum = 0,
        $onlyactive = false
    ) {
        global $DB;
        list($esql, $params) = get_enrolled_sql($context, $withcapability, $groupid, $onlyactive);
        $sql = "SELECT $userfields
                  FROM {user} u
                  JOIN ($esql) je ON je.id = u.id
                 WHERE u.deleted = 0";
        if ($orderby) {
            $sql = "$sql ORDER BY $orderby";
        } else {
            list($sort, $sortparams) = users_order_by_sql('u');
            $sql = "$sql ORDER BY $sort";
            $params = array_merge($params, $sortparams);
        }
        return $DB->get_records_sql($sql, $params, $limitfrom, $limitnum);
    }
}
