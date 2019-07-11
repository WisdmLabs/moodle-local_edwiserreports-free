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
use MoodleQuickForm;
use context_course;
use completion_info;
use progress;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/completion/classes/progress.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/active_users_block.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/active_courses_block.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/course_progress_block.php");
require_once($CFG->dirroot . "/report/elucidsitereport/classes/blocks/f2fsession_block.php");

/**
 * Utilty class to add all utility function
 * to perform in the eLucid report plugin
 */
class utility {
    public static function get_active_users_data($data) {
        if (isset($data->filter)) {
            $filter = $data->filter;
        } else {
            $filter = 'weekly'; // Default filter
        }
        return \report_elucidsitereport\active_users_block::get_data($filter);
    }

    public static function get_course_progress_data($data) {
        return \report_elucidsitereport\course_progress_block::get_data($data->courseid);
    }

    public static function get_active_courses_data() {
        return \report_elucidsitereport\active_courses_block::get_data();
    }

    public static function get_f2fsessiondata_data() {
        return \report_elucidsitereport\f2fsession_block::get_data();
    }

    public static function generate_course_filter() {
        global $DB;
        $fields = "id, fullname, shortname";
        $form = new MoodleQuickForm('test', 'post', '#');
        $courses = $DB->get_records('course', array(), '', $fields);

        $select = array();
        foreach ($courses as $course) {
            if ($course->id == 1) {
                continue;
            }
            $coursecontext = context_course::instance($course->id);
            // Get only students
            $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');
            if (count($enrolledstudents) == 0) {
                continue;
            }
            $select[$course->id] = $course->fullname;
        }

        $options = array(
           'multiple' => false,
           'placeholder' => 'Search and Select Courses',
           'class' => 'ml-0 mr-5 mb-10'
        );
        $form->addElement('autocomplete', 'courses', '', $select, $options);

        ob_start();
        $form->display();
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    public static function get_course_completion_info($course = false, $userid = false) {
        global $COURSE, $USER;
        if (!$course) {
            $course = $COURSE;
        }

        if (!$userid) {
            $userid = $USER->id;
        }

        $completioninfo = array();
        $coursecontext = context_course::instance($course->id);
        if (is_enrolled($coursecontext, $userid)) {
            $completion = new completion_info($course);
            if ($completion->is_enabled()) {
                $percentage = \core_completion\progress::get_course_progress_percentage($course, $userid);
                $modules = $completion->get_activities();
                $completioninfo['totalactivities'] = count($modules);
                $completioninfo['completedactivities'] = 0;
                if (!is_null($percentage)) {
                    $percentage = floor($percentage);
                    if ($percentage == 100) {
                        $completioninfo['progresspercentage'] = 100;
                        $completioninfo['completedactivities'] = count($modules);
                    } else if ($percentage > 0 && $percentage < 100) {
                        $completioninfo['progresspercentage'] = $percentage;
                        foreach ($modules as $module) {
                            $data = $completion->get_data($module, false, $userid);
                            if ($data->completionstate) {
                                $completioninfo['completedactivities']++;
                            }
                        }
                    } else {
                        $completioninfo['progresspercentage'] = 0;
                    }
                } else {
                    $completioninfo['progresspercentage'] = 0;
                }
            }
        }
        return $completioninfo;
    }
}