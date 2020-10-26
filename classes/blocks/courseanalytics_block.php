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
 * Plugin administration pages are defined here.
 *
 * @package     local_edwiserreports
 * @category    admin
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports;
use stdClass;
use context_course;
use html_writer;

/**
 * Class Course Access Block. To get the data related to active users block.
 */
class courseanalytics_block extends utility {
    /**
     * Get Data for Course Access
     * @param  int    $courseid Course id
     * @param  int    $cohortid Cohort id
     * @return object           Response for Course Access
     */
    public static function get_data($courseid, $cohortid) {
        $response = new stdClass();
        $response->data = self::get_courseanalytics($courseid, $cohortid);
        return $response;
    }

    /**
     * Get Course Access data
     * @param  int   $courseid Course id
     * @param  int   $cohortid Cohort id
     * @return array           Array of users with course Access
     */
    public static function get_courseanalytics($courseid, $cohortid) {

        $coursecontext = context_course::instance($courseid);
        // Get only enrolled students.
        $enrolledstudents = \local_edwiserreports\utility::get_enrolled_students($courseid, $coursecontext);

        $courseanalytics = new stdClass();
        $courseanalytics->recentvisits = self::get_recentvisits($courseid, $enrolledstudents, $cohortid);
        $courseanalytics->recentenrolments = self::get_recentenrolments($courseid, $enrolledstudents, $cohortid);
        $courseanalytics->recentcompletions = self::get_recentcompletions($courseid, $enrolledstudents, $cohortid);
        return $courseanalytics;
    }

    /**
     * Get Recent visits on a course
     * @param  int   $courseid Course ID
     * @param  array $users    Array of enrolled users
     * @param  int   $cohortid  Cohort Id
     * @return array           Array of Recent visits
     */
    public static function get_recentvisits($courseid, $users, $cohortid) {
        $timenow = time();

        $visits = array();
        foreach ($users as $user) {
            if ($cohortid) {
                $cohorts = cohort_get_user_cohorts($user->id);
                if (!array_key_exists($cohortid, $cohorts)) {
                    continue;
                }
            }

            $uservisits = self::get_visits_by_users($courseid, $user->id);

            $userinfo = array();
            $userinfo[] = fullname($user);
            if (empty($uservisits)) {
                $userinfo[] = get_string("never");
            } else {
                $timecreated = array_values((array) $uservisits)[0]->timecreated;
                $userinfo[] = html_writer::span("", $timecreated) . format_time($timenow - $timecreated);
            }
            $visits[] = $userinfo;
        }
        return $visits;
    }

    /**
     * Get Recent enrolements on a course
     * @param  int   $courseid Course ID
     * @param  array $users    Array of Enrolled Users
     * @param  int   $cohortid Cohort id
     * @return array           Array of Recent Enrolments
     */
    public static function get_recentenrolments($courseid, $users, $cohortid) {
        global $DB;

        $enrolsql = "SELECT *
            FROM {user_enrolments} ue
            JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = :courseid)
            JOIN {user} u ON u.id = ue.userid
            WHERE ue.userid = :userid AND u.deleted = 0";

        $enrolments = array();
        foreach ($users as $user) {
            if ($cohortid) {
                $cohorts = cohort_get_user_cohorts($user->id);
                if (!array_key_exists($cohortid, $cohorts)) {
                    continue;
                }
            }

            $params = array('courseid' => $courseid, 'userid' => $user->id);
            $enrolinfo = $DB->get_record_sql($enrolsql, $params, IGNORE_MULTIPLE);

            $userinfo = array();
            $userinfo[] = fullname($user);
            $userinfo[] = html_writer::span("", $enrolinfo->timemodified) . date("d M Y", $enrolinfo->timemodified);
            $enrolments[] = $userinfo;
        }
        return $enrolments;
    }

    /**
     * Get Recent eCompletions on a course
     * @param  int   $courseid Course ID
     * @param  array $users    Array of Enrolled Users
     * @param  int   $cohortid Cohort id
     * @return array           Array of Recent Completions
     */
    public static function get_recentcompletions($courseid, $users, $cohortid) {
        global $DB;

        $recentcompletions = array();
        foreach ($users as $user) {
            if ($cohortid) {
                $cohorts = cohort_get_user_cohorts($user->id);
                if (!array_key_exists($cohortid, $cohorts)) {
                    continue;
                }
            }

            $completion = $DB->get_record("course_completions" , array(
                "userid" => $user->id,
                "course" => $courseid
            ));

            $userinfo = array();
            $userinfo[] = fullname($user);
            if (!empty($completion) && $completion->timecompleted) {
                $userinfo[] = html_writer::span("", $completion->timecompleted) . date("d M Y", $completion->timecompleted);
            } else {
                $userinfo[] = get_string("notyet", "local_edwiserreports");
            }
            $recentcompletions[] = $userinfo;
        }
        return $recentcompletions;
    }

    /**
     * Get Export Header
     * @param  string $action header to get
     * @return array          Array of headers
     */
    public static function get_header_report($action) {
        switch($action) {
            case "visits" :
                $header = array(
                    get_string("name", "local_edwiserreports"),
                    get_string("lastvisit", "local_edwiserreports")
                );
                break;
            case "enrolment" :
                $header = array(
                    get_string("name", "local_edwiserreports"),
                    get_string("enrolledon", "local_edwiserreports")
                );
                break;
            case "completion" :
                $header = array(
                    get_string("name", "local_edwiserreports"),
                    get_string("completedon", "local_edwiserreports")
                );
                break;
            default :
                $header = false;
                break;
        }

        return $header;
    }
    /**
     * Get Exportable data Course Anaytics Report
     * @param  int   $courseid Course ID
     * @return array           Export array
     */
    public static function get_exportable_data_report($courseid) {
        $cohortid = optional_param("cohortid", 0, PARAM_INT);
        $action = required_param("action", PARAM_TEXT);

        $coursecontext = context_course::instance($courseid);
        $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');

        $export = array();
        $header = self::get_header_report($action);
        switch($action) {
            case "visits" :
                $response = self::get_recentvisits($courseid, $enrolledstudents, $cohortid);
                break;
            case "enrolment" :
                $response = self::get_recentenrolments($courseid, $enrolledstudents, $cohortid);
                break;
            case "completion" :
                $response = self::get_recentcompletions($courseid, $enrolledstudents, $cohortid);
                break;
            default :
                $response = false;
                break;
        }

        foreach ($response as $r => $val) {
            foreach ($val as $c => $v) {
                $response[$r][$c] = strip_tags($v);
            }
        }
        $export = array_merge(array($header), $response);
        return $export;
    }
}
