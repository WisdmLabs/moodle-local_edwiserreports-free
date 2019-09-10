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
require_once($CFG->dirroot . "/report/elucidsitereport/classes/constants.php");

/**
 * Class Course Progress Block
 * To get the data related to active users block
 */
class course_progress_block extends utility {
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
        $completions = parent::get_course_completion($course->id);
        $completedusers = array(
            PERCENTAGE_00 => 0,
            PERCENTAGE_20 => 0,
            PERCENTAGE_40 => 0,
            PERCENTAGE_60 => 0,
            PERCENTAGE_80 => 0,
            PERCENTAGE_100 => 0,
        );
        foreach ($users as $user) {

            /* If cohort filter is there then get only users from cohort */
            if ($cohortid) {
                $cohorts = cohort_get_user_cohorts($user->id);
                if (!array_key_exists($cohortid, $cohorts)) {
                    continue;
                }
            }

            // If not set the completion then this user is not completed
            if (!isset($completions[$user->id])) {
                $completedusers["0%"]++;
            } else {
                $progress = $completions[$user->id]->progress;
                switch (true) {
                    case $progress == COURSE_COMPLETE_100PER:
                        // Completed 100% of course
                        $completedusers[PERCENTAGE_100]++;
                        break;
                    case $progress >= COURSE_COMPLETE_80PER && $progress < COURSE_COMPLETE_100PER:
                        // Completed 80% of course
                        $completedusers[PERCENTAGE_80]++;
                        break;
                    case $progress >= COURSE_COMPLETE_60PER && $progress < COURSE_COMPLETE_80PER:
                        // Completed 60% of course
                        $completedusers[PERCENTAGE_60]++;
                        break;
                    case $progress >= COURSE_COMPLETE_40PER && $progress < COURSE_COMPLETE_60PER:
                        // Completed 40% of course
                        $completedusers[PERCENTAGE_40]++;
                        break;
                    case $progress >= COURSE_COMPLETE_20PER && $progress < COURSE_COMPLETE_40PER:
                        // Completed 20% of course
                        $completedusers[PERCENTAGE_20]++;
                        break;
                    default:
                        // Completed 0% of course
                        $completedusers[PERCENTAGE_00]++;
                }
            }
        }
        return array_values($completedusers);
    }

    /**
     * Get headers for exportable data for blocks
     * @return [array] Array of header
     */
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

    /**
     * Get header for report page for course progress
     * @return [array] Array of headers in report
     */
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

        $completions = parent::get_course_completions();
        $response = array();
        foreach ($courses as $course) {
            $res = (object) array(
                "completed100" => 0,
                "completed80" => 0,
                "completed60" => 0,
                "completed40" => 0,
                "completed20" => 0,
                "completed00" => 0,
                "enrolments" => 0
            );

            $res->id = $course->id;
            $res->fullname = $course->fullname;
            $coursecontext = context_course::instance($course->id);
            $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');
            foreach ($enrolledstudents as $user) {
                // If cohort filter in there then remove the users
                // who is not belongs to the cohort
                if($cohortid) {
                    $cohorts = cohort_get_user_cohorts($user->id);
                    if (!array_key_exists($cohortid, $cohorts)) {
                        unset($enrolledstudents[$key]);
                        continue;
                    }
                }

                $key = $user->id."-".$course->id;
                if (!isset($completions[$key])) {
                    // Completed 0% of course
                    $res->completed00++;
                } else {
                    $progress = $completions[$key]->progress;
                    switch (true) {
                        case $progress == COURSE_COMPLETE_100PER:
                            // Completed 100% of course
                            $res->completed100++;
                            break;
                        case $progress >= COURSE_COMPLETE_80PER && $progress < COURSE_COMPLETE_100PER:
                            // Completed 80% of course
                            $res->completed80++;
                            break;
                        case $progress >= COURSE_COMPLETE_60PER && $progress < COURSE_COMPLETE_80PER:
                            // Completed 60% of course
                            $res->completed60++;
                            break;
                        case $progress >= COURSE_COMPLETE_40PER && $progress < COURSE_COMPLETE_60PER:
                            // Completed 40% of course
                            $res->completed40++;
                            break;
                        case $progress >= COURSE_COMPLETE_20PER && $progress < COURSE_COMPLETE_40PER:
                            // Completed 20% of course
                            $res->completed20++;
                            break;
                        default:
                            // Completed 0% of course
                            $res->completed00++;
                    }
                }
                $res->enrolments++;
            }
            $response[] = $res;
        }
        return $response;
    }

    /**
     * Get Users List Table
     * @param [int] $courseid Course ID
     * @param [int] $minval Minimum Progress Value
     * @param [int] $maxval Maximum Progress Value
     */
    public static function get_userslist_table($courseid, $minval, $maxval, $cohortid) {

        $table = new html_table();
        $table->head = array(
            get_string("fullname", "report_elucidsitereport"),
            get_string("email", "report_elucidsitereport")
        );
        $table->attributes["class"] = "generaltable modal-table";

        $data = self::get_userslist($courseid, $minval, $maxval, $cohortid);
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
    public static function get_userslist($courseid, $minval, $maxval, $cohortid) {
        $course = get_course($courseid);
        $coursecontext = context_course::instance($courseid);
        $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');
        $completions = parent::get_course_completion($courseid);

        $usersdata = array();
        foreach ($enrolledstudents as $enrolleduser) {
            /* If cohort filter is there then get only users from cohort */
            if ($cohortid) {
                $cohorts = cohort_get_user_cohorts($enrolleduser->id);
                if (!array_key_exists($cohortid, $cohorts)) {
                    continue;
                }
            }

            // If Completion table dont have entries then
            // set progress as zero
            if (!isset($completions[$enrolleduser->id])) {
                $progress = 0;
            } else {
                $progress = $completions[$enrolleduser->id]->progress * 100;
            }

            // If progress between the min and max value
            if ($progress > $minval && $progress <= $maxval) {
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
        $cohortid = optional_param("cohortid", 0, PARAM_INT);
        $export = array();
        $export[] = course_progress_block::get_header_report();
        $courses = \report_elucidsitereport\utility::get_courses();
        foreach ($courses as $key => $course) {
            $courseprogress = course_progress_block::get_data($course->id, $cohortid);
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
