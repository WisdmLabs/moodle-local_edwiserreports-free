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
    public static function get_data() {
        $response = new stdClass();
        $response->data = self::get_todaysactivity();
        return $response;
    }

    public static function get_todaysactivity() {
        global $DB;

        $todaysactivity = array();
        $timenow = time();
        $context = context_system::instance();
        $midnighttime = strtotime(date("Ymd", $timenow));

        $enrollmentsql = "SELECT * FROM {user_enrolments} WHERE timecreated > ?";
        $enrollments = $DB->get_records_sql($enrollmentsql, array($midnighttime));
        $todaysactivity["enrollments"] = count($enrollments);

        $activitycompletionsql = "SELECT * FROM {course_modules_completion} WHERE timemodified > ?";
        $activitycompletions = $DB->get_records_sql($activitycompletionsql, array($midnighttime));
        $todaysactivity["activitycompletions"] = count($activitycompletions);

        $coursecompletionsql = "SELECT * FROM {course_completions} WHERE timecompleted > ?";
        $coursecompletions = $DB->get_records_sql($coursecompletionsql, array($midnighttime));
        $todaysactivity["coursecompletions"] = count($coursecompletions);

        $registrationssql = "SELECT * FROM {user} WHERE timecreated > ?";
        $registrations = $DB->get_records_sql($registrationssql, array($midnighttime));
        $todaysactivity["registrations"] = count($registrations);

        $registrationssql = "SELECT * FROM {user} WHERE timecreated > ?";
        $registrations = $DB->get_records_sql($registrationssql, array($midnighttime));
        $todaysactivity["registrations"] = count($registrations);

        $visitsssql = "SELECT DISTINCT userid
                        FROM {logstore_standard_log}
                        WHERE timecreated > ?
                        AND userid > 1"; // Remove guest users
        $visits = $DB->get_records_sql($visitsssql, array($midnighttime));
        $todaysactivity["visits"] = count($visits);

        $visitsssql .= " AND timecreated <= ?";
        $starttime = $midnighttime;
        $endtime = $midnighttime + 60 * 60;
        $todaysactivity["visitshour"] = array();
        do {
            $visitshour = $DB->get_records_sql($visitsssql, array($starttime, $endtime));
            $todaysactivity["visitshour"][] = count($visitshour);
            $starttime = $endtime;
            $endtime = $endtime + 60 * 60;
        } while ($starttime <= $timenow);

        $fetcher = new fetcher(null, $timenow, $midnighttime, $context);
        $activeusers = $fetcher->get_users(0);
        $todaysactivity["onlinelearners"] = $todaysactivity["onlineteachers"] = 0;

        // 'moodle/course:ignoreavailabilityrestrictions' - this capability is allowed to only teachers
        $teacherscap = "moodle/course:ignoreavailabilityrestrictions";

        // 'moodle/course:isincompletionreports' - this capability is allowed to only students
        $learnerscap = "moodle/course:isincompletionreports";
        foreach ($activeusers as $user) {
            $isteacher = $islearner = false;
            $courses = enrol_get_users_courses($user->id);
            foreach ($courses as $course) {
                $coursecontext = context_course::instance($course->id);
                $isteacher = has_capability($teacherscap, $coursecontext, $user->id);
                $islearner = has_capability($learnerscap, $coursecontext, $user->id);
            }

            if ($isteacher) {
                $todaysactivity["onlineteachers"]++;
            }

            if ($islearner) {
                $todaysactivity["onlinelearners"]++;
            }
        }
        return $todaysactivity;
    }
}
/*
array(
            "onlinelearners" => 20,
            "onlineteachers" => 1,
            "enrollments" => 2,
            "activitycompletions" => 3,
            "coursecompletions" => 1,
            "registrations" => 2,
            "visits" => 21,
            "sessions" => 14,
            "totalusers" => 21,
            "visitshour" => array(1, 0, 2, 5)
        );
        */