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
 * Reports block external apis
 *
 * @package     local_edwiserreports
 * @copyright   2021 wisdmlabs <support@wisdmlabs.com>
 * @author      Yogesh Shirsath
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports\external;

defined('MOODLE_INTERNAL') || die();

use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use local_edwiserreports\block_base;

require_once($CFG->libdir.'/externallib.php');
/**
 * Trait impleme56nting the external function local_edwiserreports_get_students_of_course.
 */
trait get_students_of_course {

    /**
     * Describes the structure of parameters for the function.
     *
     * @return external_function_parameters
     */
    public static function get_students_of_course_parameters() {
        return new external_function_parameters(
            array (
                'courseid' => new external_value(PARAM_INT, 'Course id')
            )
        );
    }

    /**
     * Get enrolled students from course.
     *
     * @param  int    $courseid     Course id
     * @param  bool   $owncourses   List users from courses in which current user in teacher.
     * @return bool                 Students list
     */
    public static function get_students_of_course($courseid) {
        global $USER, $COURSE;

        $blockbase = new block_base();

        if ($courseid == 0) {
            $courses = $blockbase->get_courses_of_user($USER->id);

            unset($courses[$COURSE->id]);
            $users = $blockbase->get_users_of_courses($USER->id, $courses);
        } else {
            $users = $blockbase->get_users_of_course($courseid);
        }


        array_unshift($users, (object)[
            'id' => 0,
            'fullname' => get_string('allusers', 'search')
        ]);

        return $users;
    }
    /**
     * Describes the structure of the function return value.
     *
     * @return external_multiple_structure
     */
    public static function get_students_of_course_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'Student id'),
                    'fullname' => new external_value(PARAM_TEXT, 'Student name')
                )
            )
        );
    }
}
