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

/**
 * Class Course Completion Block
 * To get the data related to active users block
 */
class completion_block extends utility {
    /**
     * Get Data for Course Completion
     * @return [object] Response for Course Completion
     */
    public static function get_data($courseid) {
        $response = new stdClass();
        $response->data = self::get_completion_data($courseid);
        return $response;
    }

    /**
     * Get headers for Course Completion Block
     * @return [array] Array of header of course block
     */
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

    /**
     * Get Course Completion data
     * @return [array] Array of users with course Completion 
     */
    public static function get_completion_data($courseid) {
        $coursecontext = context_course::instance($courseid);
        $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');
        $course = get_course($courseid);

        $userscompletion = array();
        foreach ($enrolledstudents as $user) {
            $completion = self::get_course_completion_info($course, $user->id);

            if (empty($completion)) {
                $activitycompletion = "NA";
                $progressper = "NA";
            } else {
                $activitycompletion = get_string(
                    'activitycompleted',
                    'report_elucidsitereport',
                    array(
                        "completed" => $completion["completedactivities"],
                        "total" => $completion["totalactivities"]
                    )
                );
                $progressper = $completion["progresspercentage"] . "%";
            }

            $completioninfo = new stdClass();
            $completioninfo->username = fullname($user);
            $completioninfo->useremail = $user->email;
            $completioninfo->visits = count(self::get_visits_by_users($courseid, $user->id));
            $completioninfo->activitycompleted = $activitycompletion;
            $completioninfo->completion = $progressper;
            $userscompletion[] = $completioninfo;
        }
        return $userscompletion;
    }
}