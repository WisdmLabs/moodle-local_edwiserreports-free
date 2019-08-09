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

defined('MOODLE_INTERNAL') || die();

use stdClass;
use context_course;

/**
 * Class Course Engagement Block
 * To get the data related to course engagement block
 */
class courseengage_block extends utility {
    public static function get_data() {
        $response = new stdClass();
        $response->data = self::get_courseengage($lpid);

        return $response;
    }

    /**
     * @return [array] Array of course engagement
     */
    public static function get_courseengage() {
        $engagedata = array();
        $courses = self::get_courses(true);
        foreach($courses as $course) {
            $engagedata[] = self::get_engagement($course);
        }
        return $engagedata;
    }

    /**
     * Get Course Engagement for a course
     * @param [int] $courseid Courese ID to get course engagement
     * @return [object] 
     */
    public static function get_engagement($course) {
        global $DB;
        $coursecontext = context_course::instance($course->id);
        // Get only students
        $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');

        $engagement = new stdClass();
        $engagement->coursename = $course->fullname;
        $engagement->enrolment = count($enrolledstudents);
        $engagement->visited = count(self::get_course_visites($course->id));
        $engagement->activitystart = count(self::users_completed_a_module($course->id, $enrolledstudents));
        $engagement->completedhalf = count(self::users_completed_half_module($course->id, $enrolledstudents));
        $engagement->coursecompleted = count(self::users_completed_all_module($course->id, $enrolledstudents));

        return $engagement;
    }
}