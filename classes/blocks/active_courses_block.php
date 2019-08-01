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

/**
 * Class Acive Users Block
 * To get the data related to active users block
 */
class active_courses_block extends utility {
    public static function get_data() {
        $response = new stdClass();
        $response->data = self::get_course_data();
        return $response;
    }

    public static function get_header() {
        $header = array(
            get_string("rank", "report_elucidsitereport"),
            get_string("coursename", "report_elucidsitereport"),
            get_string("enrolments", "report_elucidsitereport"),
            get_string("visits", "report_elucidsitereport"),
            get_string("completions", "report_elucidsitereport"),
        );

        return $header;
    }

    public static function get_course_data() {
        global $DB;

        $table = "course";
        $records = $DB->get_records($table, array());

        $courses = array();
        $count = 0;
        foreach ($records as $record) {
            if ($record->id == 1) {
                continue;
            }

            $count++;
            $sqlcourseview = "SELECT DISTINCT userid
                FROM {logstore_standard_log}
                WHERE action = ? AND courseid = ?";
            $course = get_course($record->id);
            $coursecontext = \context_course::instance($record->id);
            $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');

            if (!empty($enrolledstudents)) {
                $extsql = " AND userid IN (" . implode(",", array_keys($enrolledstudents)) . ")";
                $sqlcourseview .= $extsql;
            }

            $completedusers = 0;
            foreach ($enrolledstudents as $user) {
                $completion = self::get_course_completion_info($course, $user->id);
                if ($completion["progresspercentage"] == 100) {
                    $completedusers++;
                }
            }

            $courses[] = array(
                $count,
                $record->fullname,
                count($enrolledstudents),
                count($DB->get_records_sql($sqlcourseview, array(
                    'viewed',
                    $record->id
                ))),
                $completedusers
            );
        }
        return $courses;
    }
}