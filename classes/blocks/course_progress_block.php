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

/**
 * Class Course Progress Block
 * To get the data related to active users block
 */
class course_progress_block extends utility {
    public static function get_course_progress_graph_data($courseid) {
        $course = get_course($courseid);
        $coursecontext = context_course::instance($courseid);
        // Get only students
        $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');

        $response = new stdClass();
        $completed = array(
            0 => 0,
            20 => 0,
            40 => 0,
            60 => 0,
            80 => 0,
            100 => 0,
        );

        foreach ($enrolledstudents as $user) {
            $completion = self::get_course_completion_info($course, $user->id);
            switch (true) {
                case $completion["progresspercentage"] < 20:
                    $completed[0]++;
                    break;
                case $completion["progresspercentage"] < 40:
                    $completed[20]++;
                    break;
                case $completion["progresspercentage"] < 60:
                    $completed[40]++;
                    break;
                case $completion["progresspercentage"] < 80:
                    $completed[60]++;
                    break;
                case $completion["progresspercentage"] < 100:
                    $completed[80]++;
                    break;
                case $completion["progresspercentage"] = 100:
                    $completed[100]++;
                    break;
                default:
                    $completed[0]++;
                    break;
            }
        }
        $response->data = array_values($completed);
        return $response;
    }
}
