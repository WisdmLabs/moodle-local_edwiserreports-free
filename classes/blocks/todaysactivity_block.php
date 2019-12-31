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
use context_system;
use context_course;
use block_online_users\fetcher;
use theme_remui\utility;

/**
 * Class Acive Users Block
 * To get the data related to active users block
 */
class todaysactivity_block extends utility {
    public static function get_data($date = false) {
        $response = new stdClass();
        $response->data = self::get_todaysactivity($date);
        return $response;
    }

    /**
     * Get Todays Activity information
     * @param [string] $date Date filter in proprtdat format
     * @return [array] Array of todays activities information
     */
    public static function get_todaysactivity($date) {
        global $DB;

        // Set time according to the filter
        if ($date) {
            $starttime = strtotime($date);
            $endtime = $starttime + 24 * 60 * 60;
        } else {
            $endtime = time();
            $starttime = strtotime(date("Ymd", $endtime));
        }

        $todaysactivity = array();
        $total = 0;
        $context = context_system::instance();
        // Create reporting manager instance
        $rpm = reporting_manager::get_instance();
        // Enrolments
        $enrollmentsql = "SELECT * FROM {user_enrolments}
            WHERE timecreated >= :starttime
            AND timecreated < :endtime
            AND userid ".$rpm->insql."";
        $params['starttime'] = $starttime;
        $params['endtime'] = $endtime;
        $params = array_merge($params, $rpm->inparams);
        $enrollments = $DB->get_records_sql($enrollmentsql, $params);
        $todaysactivity["enrollments"] = count($enrollments);
        $total += count($enrollments);

        // Activity Completion
        $activitycompletionsql = "SELECT * FROM {course_modules_completion}
            WHERE timemodified >= :starttime
            AND timemodified < :endtime
            AND userid ".$rpm->insql."";
        $activitycompletions = $DB->get_records_sql($activitycompletionsql, $params);
        $todaysactivity["activitycompletions"] = count($activitycompletions);
        $total += count($activitycompletions);

        // Course Completion
        $coursecompletionsql = "SELECT * FROM {course_completions}
            WHERE timecompleted >= :starttime
            AND timecompleted < :endtime
            AND userid ".$rpm->insql."";
        $coursecompletions = $DB->get_records_sql($coursecompletionsql, $params);
        $todaysactivity["coursecompletions"] = count($coursecompletions);
        $total += count($coursecompletions);

        // Registration
        $registrationssql = "SELECT * FROM {user}
            WHERE timecreated >= :starttime
            AND timecreated < :endtime
            AND id ".$rpm->insql."";
        $registrations = $DB->get_records_sql($registrationssql, $params);
        $todaysactivity["registrations"] = count($registrations);
        $total += count($registrations);

        // Visits
        $visitsssql = "SELECT DISTINCT userid
            FROM {logstore_standard_log}
            WHERE timecreated >= :starttime
            AND timecreated < :endtime
            AND userid ".$rpm->insql.""; // Remove guest users
        $visits = $DB->get_records_sql($visitsssql, $params);
        $todaysactivity["visits"] = count($visits);
        $total += count($visits);

        $starttimehour = $starttime;
        $endtimehour = $starttime + 60 * 60;
        $todaysactivity["visitshour"] = array();
        $params = array();
        $params['starttime'] = $starttimehour;
        $params['endtime'] = $endtimehour;
        $params = array_merge($params, $rpm->inparams);
        do {
            $visitshour = $DB->get_records_sql($visitsssql, $params);
            $todaysactivity["visitshour"][] = count($visitshour);
            $starttimehour = $endtimehour;
            $endtimehour = $endtimehour + 60 * 60;
            $params = array();
            $params['starttime'] = $starttimehour;
            $params['endtime'] = $endtimehour;
            $params = array_merge($params, $rpm->inparams);
        } while ($starttimehour < $endtime);

        /*$fetcher = new fetcher(null, $endtime, $starttime, $context);
        $activeusers = $fetcher->get_users(0);*/
        $todaysactivity["onlinelearners"] = $todaysactivity["onlineteachers"] = 0;

        // 'moodle/course:ignoreavailabilityrestrictions' - this capability is allowed to only teachers
        $teacherscap = "moodle/course:ignoreavailabilityrestrictions";

        // 'moodle/course:isincompletionreports' - this capability is allowed to only students
        $learnerscap = "moodle/course:isincompletionreports";
        foreach ($visits as $user) {
            $isteacher = $islearner = false;
            $courses = enrol_get_users_courses($user->userid);
            foreach ($courses as $course) {
                $coursecontext = context_course::instance($course->id);
                $isteacher = has_capability($teacherscap, $coursecontext, $user->userid);
                $islearner = has_capability($learnerscap, $coursecontext, $user->userid);
            }

            if ($isteacher) {
                $todaysactivity["onlineteachers"]++;
            }

            if ($islearner) {
                $todaysactivity["onlinelearners"]++;
            }
        }
        $todaysactivity["totalusers"] = $total;
        return $todaysactivity;
    }
}
