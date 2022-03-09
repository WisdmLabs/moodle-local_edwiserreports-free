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
use local_edwiserreports\visitsonsiteblock;
use local_edwiserreports\timespentonsiteblock;
use local_edwiserreports\timespentoncourseblock;
use local_edwiserreports\courseactivitystatusblock;

require_once($CFG->dirroot . '/local/edwiserreports/classes/block_base.php');

/**
 * Active users block.
 */
class studentengagement extends block_base {

    /**
     * Prepare active users block filters
     * @return array filters array
     */
    public function get_studentengagement_filter() {
        global $USER, $COURSE, $USER, $DB;

        $courses = $this->get_courses_of_user($USER->id);

        unset($courses[$COURSE->id]);

        array_unshift($courses, (object)[
            'id' => 0,
            'fullname' => get_string('fulllistofcourses')
        ]);

        return $courses;
    }

    /**
     * Get users for more details table.
     *
     * @param int       $cohort         Cohort id
     * @param array     $coursetable    Courses table name
     * @param string    $search         Search query
     * @param int       $start          Starting row index of page
     * @param int       $length         Number of roows per page
     *
     * @return array
     */
    private function get_table_users($cohort, $coursetable, $search, $start, $length) {
        global $DB;

        $params = [
            'contextlevel' => CONTEXT_COURSE,
            'archetype' => 'student'
        ];

        $searchquery = '';
        $fullname = $DB->sql_fullname("u.firstname", "u.lastname");
        if (trim($search) !== '') {
            $params['search'] = "%$search%";
            $searchquery = 'AND ' . $DB->sql_like($fullname, ':search');
        }

        // If cohort ID is there then add cohort filter in sqlquery.
        $sqlcohort = "";
        if ($cohort) {
            $sqlcohort .= "JOIN {cohort_members} cm ON cm.userid = u.id AND cm.cohortid = :cohortid";
            $params["cohortid"] = $cohort;
        }

        $sql = "SELECT DISTINCT u.id, $fullname student
                  FROM {{$coursetable}} c
                  JOIN {context} ctx ON c.tempid = ctx.instanceid
                  JOIN {role_assignments} ra ON ctx.id = ra.contextid
                  JOIN {role} r ON ra.roleid = r.id
                  JOIN {user} u ON ra.userid = u.id
                  $sqlcohort
                 WHERE ctx.contextlevel = :contextlevel
                   AND r.archetype = :archetype
                   $searchquery";
        $users = $DB->get_records_sql($sql, $params, $start, $length);

        $countsql = "SELECT count(DISTINCT u.id)
                       FROM {{$coursetable}} c
                       JOIN {context} ctx ON c.tempid = ctx.instanceid
                       JOIN {role_assignments} ra ON ctx.id = ra.contextid
                       JOIN {role} r ON ra.roleid = r.id
                       JOIN {user} u ON ra.userid = u.id
                       $sqlcohort
                      WHERE ctx.contextlevel = :contextlevel
                        AND r.archetype = :archetype
                        $searchquery";
        $count = $DB->count_records_sql($countsql, $params);
        return [$users, $count];
    }

    /**
     * Get total timespent on lms data for table.
     *
     * @param string $userstable User table name
     *
     * @return array
     */
    private function get_table_timespentonmls($userstable) {
        global $DB;
        $sql = "SELECT al.userid id, sum(" . $DB->sql_cast_char2int("al.timespent") . ") timespent
                  FROM {{$userstable}} u
                  JOIN {edwreports_activity_log} al ON u.tempid = al.userid
                 GROUP BY al.userid";
        return $DB->get_records_sql($sql);
    }

    /**
     * Get total timespent on course data for table.
     *
     * @param string $userstable    User table name
     * @param string $coursetable   Course table name
     *
     * @return array
     */
    private function get_table_timespentoncourse($userstable, $coursetable) {
        global $DB;
        $sql = "SELECT al.userid id, sum(" . $DB->sql_cast_char2int("al.timespent") . ") timespent
                  FROM {{$userstable}} u
                  JOIN {edwreports_activity_log} al ON u.tempid = al.userid
                  JOIN {{$coursetable}} c ON c.tempid = al.course
                 GROUP BY al.userid";
        return $DB->get_records_sql($sql);
    }

    /**
     * Get total assingment submmited in course data for table.
     *
     * @param int    $userid        Current user id
     * @param string $userstable    User table name
     * @param string $coursetable   Course table name
     *
     * @return array
     */
    private function get_table_assignmentsubmitted($userid, $usertable, $coursetable) {
        global $DB;
        $sql = "SELECT u.tempid id, count(sub.id) submitted
                FROM {{$usertable}} u
                JOIN {assign_submission} sub ON u.tempid = sub.userid
                JOIN {assign} a ON sub.assignment = a.id ";
        if (!is_siteadmin($userid) && has_capability('moodle/site:configview', context_system::instance(), $userid)) {
            $sql .= "JOIN {{$coursetable}} c ON c.tempid = a.course ";
        }
        $sql .= "GROUP BY u.tempid";
        return $DB->get_records_sql($sql);
    }

    /**
     * Get total number of activities completed in course data for table.
     *
     * @param int    $userid        Current user id
     * @param string $userstable    User table name
     * @param string $coursetable   Course table name
     *
     * @return array
     */
    private function get_table_activitiescompleted($userid, $usertable, $coursetable) {
        global $DB;
        $sql = "SELECT u.tempid id, count(cmc.id) completed
                FROM {{$usertable}} u
                JOIN {course_modules_completion} cmc ON u.tempid = cmc.userid
                JOIN {course_modules} cm ON cmc.coursemoduleid = cm.id ";
        if (!is_siteadmin($userid) && has_capability('moodle/site:configview', context_system::instance(), $userid)) {
            $sql .= "JOIN {{$coursetable}} c ON c.tempid = cm.course ";
        }
        $sql .= "WHERE cmc.completionstate <> 0
                 GROUP BY u.tempid";
        return $DB->get_records_sql($sql);
    }

    /**
     * Get visits count on course data for table.
     *
     * @param string $userstable    User table name
     * @param string $coursetable   Course table name
     *
     * @return array
     */
    private function get_table_visitsoncourse($usertable, $coursetable) {
        global $DB;
        $sql = "SELECT u.tempid id, count(al.activity) visits
                  FROM {{$usertable}} u
                  JOIN {edwreports_activity_log} al ON u.tempid = al.userid
                  JOIN {{$coursetable}} c ON c.tempid = al.course
                 GROUP BY u.tempid";
        return $DB->get_records_sql($sql);
    }

    /**
     * Get student engagement table data based on filters
     *
     * @param object $filter Table filters.
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

        list($users, $count) = $this->get_table_users(
            $cohort,
            $coursetable,
            $search,
            $start,
            $length
        );

        // Temporary user table.
        $usertable = 'tmp_stengage_users';
        // Creating temporary table.
        utility::create_temp_table($usertable, array_keys($users));

        $timespentonlms = $this->get_table_timespentonmls($usertable);
        $timespentoncourse = $this->get_table_timespentoncourse($usertable, $coursetable);
        $assignmentsubmitted = $this->get_table_assignmentsubmitted($userid, $usertable, $coursetable);
        $activitiescompleted = $this->get_table_activitiescompleted($userid, $usertable, $coursetable);
        $visitsoncourse = $this->get_table_visitsoncourse($usertable, $coursetable);

        foreach (array_keys($users) as $key) {
            unset($users[$key]->id);
            $users[$key]->timespentoncourse = isset($timespentoncourse[$key]) ? $timespentoncourse[$key]->timespent : 0;
            $users[$key]->timespentonlms = isset($timespentonlms[$key]) ? $timespentonlms[$key]->timespent : 0;
            $users[$key]->assignmentsubmitted = isset($assignmentsubmitted[$key]) ? $assignmentsubmitted[$key]->submitted : 0;
            $users[$key]->activitiescompleted = isset($activitiescompleted[$key]) ? $activitiescompleted[$key]->completed : 0;
            $users[$key]->visitsoncourse = isset($visitsoncourse[$key]) ? $visitsoncourse[$key]->visits : 0;
        }

        // Droppping course table.
        utility::drop_temp_table($coursetable);

        // Droppping user table.
        utility::drop_temp_table($usertable);

        return [
            "data" => empty($users) ? [] : array_values($users),
            "recordsTotal" => $count,
            "recordsFiltered" => $count
        ];
    }

    /**
     * Get exportable data for report.
     *
     * @param string $filter Filter parameter
     *
     * @return array
     */
    public static function get_exportable_data_report($filter) {
        global $COURSE, $USER;

        $filter = explode('-', $filter);
        $cohortid = (int)$filter[0];
        $course = (int)$filter[1];
        $obj = new self();

        if ($course === 0) {
            $courses = $obj->get_courses_of_user($USER->id);
            unset($courses[$COURSE->id]);
            $courses = array_keys($courses);
        } else {
            $courses = [$course];
        }

        // Temporary course table.
        $coursetable = 'tmp_stengage_courses';
        // Creating temporary table.
        utility::create_temp_table($coursetable, $courses);

        list($users) = $obj->get_table_users(
            $cohortid,
            $coursetable,
            '',
            0,
            0
        );

        // Temporary user table.
        $usertable = 'tmp_stengage_users';
        // Creating temporary table.
        utility::create_temp_table($usertable, array_keys($users));

        $timespentonlms = $obj->get_table_timespentonmls($usertable);
        $timespentoncourse = $obj->get_table_timespentoncourse($usertable, $coursetable);
        $assignmentsubmitted = $obj->get_table_assignmentsubmitted($USER->id, $usertable, $coursetable);
        $activitiescompleted = $obj->get_table_activitiescompleted($USER->id, $usertable, $coursetable);
        $visitsoncourse = $obj->get_table_visitsoncourse($usertable, $coursetable);

        $export = [];
        $export[] = [
            get_string('student', 'core_grades'),
            get_string('timespentonlms', 'local_edwiserreports'),
            get_string('timespentoncourse', 'local_edwiserreports'),
            get_string('assignmentsubmitted', 'local_edwiserreports'),
            get_string('activitiescompleted', 'local_edwiserreports'),
            get_string('visitsoncourse', 'local_edwiserreports')
        ];
        foreach (array_keys($users) as $key) {
            $export[] = [
                $users[$key]->student,
                isset($timespentoncourse[$key]) ? date('H:i:s', mktime(0, 0, $timespentoncourse[$key]->timespent)) : 0,
                isset($timespentonlms[$key]) ? date('H:i:s', mktime(0, 0, $timespentonlms[$key]->timespent)) : 0,
                isset($assignmentsubmitted[$key]) ? $assignmentsubmitted[$key]->submitted : 0,
                isset($activitiescompleted[$key]) ? $activitiescompleted[$key]->completed : 0,
                isset($visitsoncourse[$key]) ? $visitsoncourse[$key]->visits : 0
            ];
        }

        // Droppping course table.
        utility::drop_temp_table($coursetable);

        // Droppping user table.
        utility::drop_temp_table($usertable);

        return $export;
    }
}
