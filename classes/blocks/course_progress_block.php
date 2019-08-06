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

/**
 * Class Course Progress Block
 * To get the data related to active users block
 */
class course_progress_block extends utility {
    public static $constcompleted = array(
                                        "incompleted" => 0,
                                        "completed20" => 1,
                                        "completed40" => 2,
                                        "completed60" => 3,
                                        "completed80" => 4,
                                        "completed" => 5,
                                      );

    public static function get_data($courseid) {
        $course = get_course($courseid);
        $coursecontext = context_course::instance($courseid);
        // Get only students
        $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');

        $response = new stdClass();
        $completed = array(
            self::$constcompleted["incompleted"] => 0,
            self::$constcompleted["completed20"] => 0,
            self::$constcompleted["completed40"] => 0,
            self::$constcompleted["completed60"] => 0,
            self::$constcompleted["completed80"] => 0,
            self::$constcompleted["completed"] => 0,
        );

        foreach ($enrolledstudents as $user) {
            $completion = self::get_course_completion_info($course, $user->id);
            switch (true) {
                case $completion["progresspercentage"] < 20:
                    $completed[self::$constcompleted["incompleted"]]++;
                    break;
                case $completion["progresspercentage"] < 40:
                    $completed[self::$constcompleted["completed20"]]++;
                    break;
                case $completion["progresspercentage"] < 60:
                    $completed[self::$constcompleted["completed40"]]++;
                    break;
                case $completion["progresspercentage"] < 80:
                    $completed[self::$constcompleted["completed60"]]++;
                    break;
                case $completion["progresspercentage"] < 100:
                    $completed[self::$constcompleted["completed80"]]++;
                    break;
                case $completion["progresspercentage"] = 100:
                    $completed[self::$constcompleted["completed"]]++;
                    break;
                default:
                    $completed[self::$constcompleted["incompleted"]]++;
                    break;
            }
        }
        $response->data = array_values($completed);
        return $response;
    }

    public static function get_header() {
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

    public static function get_courselist() {
        $courses = \report_elucidsitereport\utility::get_courses(true);

        $response = array();
        foreach ($courses as $course) {
            $coursedata = self::get_data($course->id);
            foreach ($coursedata->data as $key => $data) {
                $reskey = array_search($key, self::$constcompleted);
                $course->$reskey = $data;
            }

            $coursecontext = context_course::instance($course->id);
            $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');
            $course->enrolments = count($enrolledstudents);
            $response[] = $course;
        }
        return $response;
    }

    public static function get_userslist_table($courseid, $action) {

        $table = new html_table();
        $table->head = array(
            get_string("fullname", "report_elucidsitereport"),
            get_string("email", "report_elucidsitereport")
        );
        $table->attributes["class"] = "generaltable modal-table";
        $table->data = self::get_userslist($courseid, $action);
        return html_writer::table($table);
    }

    public static function get_userslist($courseid, $action) {
        $course = get_course($courseid);
        $coursecontext = context_course::instance($courseid);
        $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');

        $data = array();
        foreach ($enrolledstudents as $enrolleduser) {
            $completion = self::get_course_completion_info($course, $enrolleduser->id);
            $progressper = $completion["progresspercentage"];

            switch ($action) {
                case "incompleted":
                    if ($progressper < 20) {
                        $user = core_user::get_user($enrolleduser->id);
                        $data[] = array(
                            fullname($user),
                            $user->email
                        );
                    }
                    break;
                case "completed20":
                    if ($progressper >= 20 && $progressper < 40) {
                        $user = core_user::get_user($enrolleduser->id);
                        $data[] = array(
                            fullname($user),
                            $user->email
                        );
                    }
                    break;
                case "completed40":
                    if ($progressper >= 40 && $progressper < 60) {
                        $user = core_user::get_user($enrolleduser->id);
                        $data[] = array(
                            fullname($user),
                            $user->email
                        );
                    }
                    break;
                case "completed60":
                    if ($progressper >= 60 && $progressper < 80) {
                        $user = core_user::get_user($enrolleduser->id);
                        $data[] = array(
                            fullname($user),
                            $user->email
                        );
                    }
                    break;
                case "completed80":
                    if ($progressper >= 80 && $progressper < 100) {
                        $user = core_user::get_user($enrolleduser->id);
                        $data[] = array(
                            fullname($user),
                            $user->email
                        );
                    }
                    break;
                case "completed":
                    if ($progressper == 100) {
                        $user = core_user::get_user($enrolleduser->id);
                        $data[] = array(
                            fullname($user),
                            $user->email
                        );
                    }
                    break;
                default:
                    $user = core_user::get_user($enrolleduser->id);
                    $data[] = array(
                        fullname($user),
                        $user->email
                    );
                    break;
            }
        }
        return $data;
    }
}
