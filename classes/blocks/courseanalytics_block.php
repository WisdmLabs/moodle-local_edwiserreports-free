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
 * @package     report_elucidsitereport
 * @category    admin
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_elucidsitereport;
use stdClass;
use context_course;
use html_writer;

/**
 * Class Course Access Block
 * To get the data related to active users block
 */
class courseanalytics_block extends utility {
    /**
     * Get Data for Course Access
     * @return [object] Response for Course Access
     */
    public static function get_data($courseid) {
        $response = new stdClass();
        $response->data = self::get_courseanalytics_data($courseid);
        return $response;
    }

    /**
     * Get Course Access data
     * @return [array] Array of users with course Access 
     */
    public static function get_courseanalytics_data($courseid) {
        global $DB;

        $coursecontext = context_course::instance($courseid);
        $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');
        $course = get_course($courseid);
        $timenow = time();

        $courseanalytics = new stdClass();
        $courseanalytics->recentvisits = self::get_recentvisits($courseid, $enrolledstudents);
        $courseanalytics->recentenrolments = self::get_recentenrolments($courseid, $enrolledstudents);
        $courseanalytics->recentcompletions = self::get_recentcompletions($courseid, $enrolledstudents);
        return $courseanalytics;
    }

    /**
     * Get Recent visits on a course
     * @param [int] $courseid Course ID
     * @param [array] $users Array of Enrolled Users
     * @return [array] Array of Recent visits
     */
    public static function get_recentvisits($courseid, $users) {
        $cohortid = 0; //TODO: Remove for cohort filter
        $allvisits = self::get_course_visites($courseid, $cohortid);
        $timenow = time();

        $visits = array();
        foreach($users as $user) {
            $uservisits = self::get_visits_by_users($courseid, $user->id);

            $userinfo = array();
            $userinfo[] = fullname($user);
            if (empty($uservisits)) {
                $userinfo[] = get_string("never");
            } else {
                $timecreated = array_values($uservisits)[0]->timecreated;
                $userinfo[] = html_writer::span($timecreated, "d-none") . format_time($timenow - $timecreated);
            }
            $visits[] = $userinfo;
        }
        return $visits;
    }

    /**
     * Get Recent enrolements on a course
     * @param [int] $courseid Course ID
     * @param [array] $users Array of Enrolled Users
     * @return [array] Array of Recent Enrolments
     */
    public static function get_recentenrolments($courseid, $users) {
        global $DB;

        $enrolsql = "SELECT *
            FROM {user_enrolments} ue
            JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = :courseid)
            JOIN {user} u ON u.id = ue.userid
            WHERE ue.userid = :userid AND u.deleted = 0";

        $params = array('courseid'=>$courseid, 'userid' => $user->id);
            $enrolinfo = $DB->get_record_sql($enrolsql, $params);

        $enrolments = array();
        foreach($users as $user) {
            $params = array('courseid'=>$courseid, 'userid' => $user->id);
            $enrolinfo = $DB->get_record_sql($enrolsql, $params);

            $userinfo = array();
            $userinfo[] = fullname($user);
            $userinfo[] = html_writer::span($enrolinfo->timemodified, "d-none") . date("d M Y", $enrolinfo->timemodified);
            $enrolments[] = $userinfo;
        }
        return $enrolments;
    }

    /**
     * Get Recent eCompletions on a course
     * @param [int] $courseid Course ID
     * @param [array] $users Array of Enrolled Users
     * @return [array] Array of Recent Completions
     */
    public static function get_recentcompletions($courseid, $users) {
        global $DB;
        $course = get_course($courseid);

        $recentcompletions = array();
        foreach($users as $user) {
            $completion = $DB->get_record("course_completions" , array(
                "userid" => $user->id,
                "course" => $courseid
            ));

            $userinfo = array();
            $userinfo[] = fullname($user);
            if (!empty($completion) && $completion->timecompleted) {
                $userinfo[] = html_writer::span($completion->timecompleted, "d-none") . date("d M Y", $completion->timecompleted);
            } else {
                $userinfo[] = get_string("notyet", "report_elucidsitereport");
            }
            $recentcompletions[] = $userinfo;
        }
        return $recentcompletions;
    }
}