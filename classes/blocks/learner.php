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

defined('MOODLE_INTERNAL') or die;

use local_edwiserreports\controller\authentication;
use context_system;
use moodle_url;

require_once($CFG->dirroot . '/local/edwiserreports/classes/block_base.php');

/**
 * Active users block.
 */
class learner extends block_base {

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
     * Dates main array.
     *
     * @var array
     */
    public $dates = [];

    /**
     * Get user using secret key or global $USER
     *
     * @return int
     */
    private function get_user() {
        global $USER;
        $secret = optional_param('secret', null, PARAM_TEXT);
        if ($secret !== null) {
            $authentication = new \local_edwiserreports\controller\authentication();
            $userid = $authentication->get_user($secret);
        } else {
            $userid = $USER->id;
        }
        return $userid;
    }

   /**
     * Get total timespent on course data for table.
     *
     * @param string $userid        User id
     * @param string $coursetable   Course table name
     *
     * @return array
     */
    private function get_table_timespentoncourse($userid, $coursetable) {
        global $DB;
        $sql = "SELECT al.course id, SUM(al.timespent) timespent
                  FROM {edwreports_activity_log} al
                  JOIN {{$coursetable}} c ON c.tempid = al.course
                  WHERE al.userid = :userid
                 GROUP BY al.course";
        return $DB->get_records_sql($sql, ['userid' => $userid]);
    }

    /**
     * Get activities completion count and course progress for courses.
     *
     * @param int       $userid         User id
     * @param string    $coursetable    Course table
     *
     * @return array
     */
    private function get_table_activitiescompleted($userid, $coursetable) {
        global $DB;
        $sql = "SELECT c.tempid, cp.progress, cp.totalmodules activitiescompleted
                  FROM {{$coursetable}} c
                  JOIN {edwreports_course_progress} cp ON c.tempid = cp.courseid
                 WHERE cp.userid = :userid";
        return $DB->get_records_sql($sql, ['userid' => $userid]);
    }

    /**
     * Get grade of courses.
     *
     * @param int       $userid         User id
     * @param string    $coursetable    Course table
     *
     * @return array
     */
    private function get_table_grade($userid, $coursetable) {
        global $DB;
        $sql = "SELECT c.tempid, gg.finalgrade grades
                  FROM {{$coursetable}} c
                  JOIN {grade_items} gi ON c.tempid = gi.iteminstance
                  JOIN {grade_grades} gg ON gi.id = gg.itemid
                 WHERE " . $DB->sql_compare_text('gi.itemtype') . " = " . $DB->sql_compare_text(':itemtype') .
                  "AND  gg.userid = :userid";
        $params = [
            'itemtype' => 'course',
            'userid' => $userid
        ];
        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get student engagement table data based on filters
     *
     * @param object $filter Table filters.
     *
     * @return array
     */
    public function get_table_data($filter) {
        global $COURSE, $DB;

        $search = $filter->search;
        $start = (int)$filter->start;
        $length = (int)$filter->length;

        $secret = optional_param('secret', null, PARAM_TEXT);

        $authentication = new authentication();
        $userid = $authentication->get_user($secret);

        if (is_siteadmin($userid) || has_capability('moodle/site:configview', context_system::instance(), $userid)) {
            $usercourses = get_courses();
        } else {
            $usercourses = enrol_get_users_courses($userid);
        }
        unset($usercourses[$COURSE->id]);
        $usercourses = array_keys($usercourses);
        $count = count($usercourses);
        // Temporary course table.
        $coursetable = 'tmp_sql_courses';
        // Creating temporary table.
        utility::create_temp_table($coursetable, $usercourses);

        $sql = "SELECT c.id, c.fullname
                  FROM {{$coursetable}} uc
                  JOIN {course} c ON uc.tempid = c.id
                 WHERE " . $DB->sql_like('c.fullname', ':fullname');
        $params = [
            'userid' => $userid,
            'fullname' => "%$search%"
        ];
        $courses = $DB->get_records_sql($sql, $params, $start, $length);
        // Droppping course table.
        utility::drop_temp_table($coursetable);

        // Temporary course table.
        $coursetable = 'tmp_learner_courses';
        // Creating temporary table.
        utility::create_temp_table($coursetable, array_keys($courses));

        $activitiescompleted = $this->get_table_activitiescompleted($userid, $coursetable);
        $timespentoncourse = $this->get_table_timespentoncourse($userid, $coursetable);
        $grades = $this->get_table_grade($userid, $coursetable);

        foreach (array_keys($courses) as $key) {
            unset($courses[$key]->id);
            $courses[$key]->progress = isset($activitiescompleted[$key]) ? $activitiescompleted[$key]->progress . '%' : 0;
            $courses[$key]->activitiescompleted = isset($activitiescompleted[$key]) ?
                                                $activitiescompleted[$key]->activitiescompleted : 0;
            $courses[$key]->timespentoncourse = isset($timespentoncourse[$key]) ? $timespentoncourse[$key]->timespent : 0;
            $courses[$key]->grades = isset($grades[$key]) ? round($grades[$key]->grades, 2) . '%' : '-';
        }

        // Droppping course table.
        utility::drop_temp_table($coursetable);

        return [
            "data" => empty($courses) ? [] : array_values($courses),
            "recordsTotal" => $count,
            "recordsFiltered" => $count
        ];
    }
}
