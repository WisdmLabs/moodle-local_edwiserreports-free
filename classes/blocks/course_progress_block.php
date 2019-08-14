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
use completion_info;
use html_table;
use html_writer;
use html_table_cell;
use html_table_row;
use core_user;

require_once($CFG->dirroot . "/cohort/lib.php");

/**
 * Class Course Progress Block
 * To get the data related to active users block
 */
class course_progress_block extends utility {
    /**
     * Constant for completions
     */
    public static $completed = array(
        "00" => array(
            "index" => 0,
            "value" => 0,
            "count" => 0
        ),
        "20" => array(
            "index" => 1,
            "value" => 20,
            "count" => 0
        ),
        "40" => array(
            "index" => 2,
            "value" => 40,
            "count" => 0
        ),
        "60" => array(
            "index" => 3,
            "value" => 60,
            "count" => 0
        ),
        "80" => array(
            "index" => 4,
            "value" => 80,
            "count" => 0
        ),
        "100" => array(
            "index" => 5,
            "value" => 100,
            "count" => 0
        )
    );

    /**
     * Get data for course progress block
     * @param [int] $courseid Course Id
     * @return [array] Array of course completion info
     */
    public static function get_data($courseid, $cohortid = 0) {
        $course = get_course($courseid);
        $coursecontext = context_course::instance($courseid);
        // Get only students
        $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');

        $response = new stdClass();
        $response->data = self::get_completion_with_percentage($course, $enrolledstudents, $cohortid);
        return $response;
    }

    /**
     * Get completion with percentage
     * (0%, 20%, 40%, 60%, 80%, 100%)
     * @param [object] $course Course Object
     * @param [object] $users Users Object
     * @return [array] Array of completion with percentage
     */
    public static function get_completion_with_percentage($course, $users, $cohortid) {
        foreach ($users as $user) {

            /* If cohort filter is there then get only users from cohort */
            if ($cohortid) {
                $cohorts = cohort_get_user_cohorts($user->id);
                if (!array_key_exists($cohortid, $cohorts)) {
                    continue;
                }
            }

            $completion = self::get_course_completion_info($course, $user->id);
            $progressper = $completion["progresspercentage"];
            switch (true) {
                case $progressper == self::$completed["100"]["value"]:
                    self::$completed["100"]["count"]++;
                case $progressper >= self::$completed["80"]["value"]:
                    self::$completed["80"]["count"]++;
                case $progressper >= self::$completed["60"]["value"]:
                    self::$completed["60"]["count"]++;
                case $progressper >= self::$completed["40"]["value"]:
                    self::$completed["40"]["count"]++;
                case $progressper >= self::$completed["20"]["value"]:
                    self::$completed["20"]["count"]++;
                    break;
                default:
                    self::$completed["00"]["count"]++;
                    break;
            }
        }

        $completed = array();
        foreach(self::$completed as $key => $complete) {
            $completed[$complete["index"]] = $complete["count"];
            // Flush after getting the value
            self::$completed[$key]["count"] = 0;
        }

        return $completed;
    }

    public static function get_header() {
        $header = array(
            get_string("name", "report_elucidsitereport"),
            get_string("email", "report_elucidsitereport"),
            get_string("coursename", "report_elucidsitereport"),
            get_string("completedactivity", "report_elucidsitereport"),
            get_string("completions", "report_elucidsitereport")
        );
        return $header;
    }

    public static function get_header_report() {
        $header = array(
            get_string("coursename", "report_elucidsitereport"),
            get_string("noofenrolled", "report_elucidsitereport"),
            get_string("noofincompleted", "report_elucidsitereport"),
            get_string("noofcompleted20", "report_elucidsitereport"),
            get_string("noofcompleted40", "report_elucidsitereport"),
            get_string("noofcompleted60", "report_elucidsitereport"),
            get_string("noofcompleted80", "report_elucidsitereport"),
            get_string("noofcompleted", "report_elucidsitereport"),
        );
        return $header;
    }

    /**
     * Get Course List
     * @param [int] $cohort ID
     * @return [object] Object of course list
     */
    public static function get_courselist($cohortid) {
        $courses = \report_elucidsitereport\utility::get_courses(true);

        $response = array();
        foreach ($courses as $course) {
            $coursedata = self::get_data($course->id, $cohortid);
            foreach ($coursedata->data as $key => $data) {
                foreach(self::$completed as $compkey => $complete) {
                    if ($key == $complete["index"]) {
                        $attrkey = "completed".$compkey;
                    }
                }
                $course->$attrkey = $data;
            }

            $coursecontext = context_course::instance($course->id);
            $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');

            if($cohortid) {
                foreach($enrolledstudents as $key => $user) {
                    $cohorts = cohort_get_user_cohorts($user->id);
                    if (!array_key_exists($cohortid, $cohorts)) {
                        unset($enrolledstudents[$key]);
                    }
                }
            }

            $course->enrolments = count($enrolledstudents);
            $response[] = $course;
        }
        return $response;
    }

    /**
     * Get Users List Table
     * @param [int] $courseid Course ID
     * @param [int] $minval Minimum Progress Value
     * @param [int] $maxval Maximum Progress Value
     */
    public static function get_userslist_table($courseid, $minval, $maxval) {

        $table = new html_table();
        $table->head = array(
            get_string("fullname", "report_elucidsitereport"),
            get_string("email", "report_elucidsitereport")
        );
        $table->attributes["class"] = "generaltable modal-table";

        $data = self::get_userslist($courseid, $minval, $maxval);
        if (empty($data)) {
            $notavail = get_string("nousersavailable", "report_elucidsitereport");
            $emptycell = new html_table_cell($notavail);
            $row = new html_table_row();
            $emptycell->colspan = count($table->head);
            $emptycell->attributes = array(
                "class" => "text-center"
            );
            $row->cells = array($emptycell);
            $table->data = array($row);
        } else {
            $table->data = $data;
        }
        return html_writer::table($table);
    }

    /**
     * Get Users list
     * @param [int] $courseid Course ID
     * @param [int] $minval Minimum Progress Value
     * @param [int] $maxval Maximum Progress Value
     * @return [array] Users Data Array
     */
    public static function get_userslist($courseid, $minval, $maxval) {
        $course = get_course($courseid);
        $coursecontext = context_course::instance($courseid);
        $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');

        $usersdata = array();
        foreach ($enrolledstudents as $enrolleduser) {
            $completion = self::get_course_completion_info($course, $enrolleduser->id);
            $progressper = $completion["progresspercentage"];
            if ($progressper > $minval && $progressper <= $maxval) {
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
     * @param $filter [string] Filter to get data from specific range
     * @return [array] Array of exportable data
     */
    public static function get_exportable_data_block($filter) {
        $export = array();
        $export[] = course_progress_block::get_header();
        $coursecontext = context_course::instance($filter);
        $course = get_course($filter);
        $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');
        foreach($enrolledstudents as $key => $student) {
            $completion = course_progress_block::get_course_completion_info($course, $student->id);
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
     * @param $filter [string] Filter to get data from specific range
     * @return [array] Array of exportable data
     */
    public static function get_exportable_data_report($filter) {
        $export = array();
        $export[] = course_progress_block::get_header_report();
        $courses = \report_elucidsitereport\utility::get_courses();
        foreach ($courses as $key => $course) {
            $courseprogress = course_progress_block::get_data($course->id);
            $coursecontext = context_course::instance($course->id);
            $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');
            $export[] = array_merge(
                array(
                    $course->fullname,
                    count($enrolledstudents)
                ),
                $courseprogress->data
            );
        }

        return $export;
    }
}
