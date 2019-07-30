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

use completion_info;
use context_course;
use MoodleQuickForm;
use progress;

defined('MOODLE_INTERNAL') || die();

require_once $CFG->dirroot . "/completion/classes/progress.php";
require_once $CFG->dirroot . "/report/elucidsitereport/classes/blocks/active_users_block.php";
require_once $CFG->dirroot . "/report/elucidsitereport/classes/blocks/active_courses_block.php";
require_once $CFG->dirroot . "/report/elucidsitereport/classes/blocks/course_progress_block.php";
require_once $CFG->dirroot . "/report/elucidsitereport/classes/blocks/f2fsession_block.php";
require_once $CFG->dirroot . "/report/elucidsitereport/classes/blocks/certificates_block.php";
require_once $CFG->dirroot . "/report/elucidsitereport/classes/blocks/liveusers_block.php";
require_once $CFG->dirroot . "/report/elucidsitereport/classes/blocks/siteaccess_block.php";
require_once $CFG->dirroot . "/report/elucidsitereport/classes/blocks/todaysactivity_block.php";
require_once $CFG->dirroot . "/report/elucidsitereport/classes/blocks/lpstats_block.php";
require_once $CFG->dirroot . "/report/elucidsitereport/classes/blocks/inactiveusers_block.php";

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
        if ($data->courseid == "all") {
            return \report_elucidsitereport\course_progress_block::get_courselist();
        }
        return \report_elucidsitereport\course_progress_block::get_data($data->courseid);
    }

    public static function get_active_courses_data() {
        return \report_elucidsitereport\active_courses_block::get_data();
    }

    public static function get_f2fsessiondata_data() {
        return \report_elucidsitereport\f2fsession_block::get_data();
    }

    public static function get_certificates_data($data) {
        if (isset($data->certificateid)) {
            return \report_elucidsitereport\certificates_block::get_certificate($data->certificateid);
        }
        return \report_elucidsitereport\certificates_block::get_data();
    }

    public static function get_liveusers_data() {
        return \report_elucidsitereport\liveusers_block::get_data();
    }

    public static function get_siteaccess_data() {
        return \report_elucidsitereport\siteaccess_block::get_data();
    }

    public static function get_todaysactivity_data() {
        return \report_elucidsitereport\todaysactivity_block::get_data();
    }

    public static function get_lpstats_data($data) {
        return \report_elucidsitereport\lpstats_block::get_data($data->lpid);
    }

    public static function get_inactiveusers_data($data) {
        if (isset($data->filter)) {
            $filter = $data->filter;
        } else {
            $filter = 'never'; // Default filter
        }
        return \report_elucidsitereport\inactiveusers_block::get_data($filter);
    }

    /* Generate Course Filter for course progress block
     * @return String HTML form with select and search box
     */
    public static function get_courses($all = false) {
        global $DB;
        $fields = "id, fullname, shortname";
        $form = new MoodleQuickForm('course', 'post', '#');
        $records = $DB->get_records('course', array(), '', $fields);

        $courses = array();
        foreach ($records as $course) {
            if ($course->id == 1) {
                continue;
            }
            $coursecontext = context_course::instance($course->id);
            // Get only students
            $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');
            if (!$all && count($enrolledstudents) == 0) {
                continue;
            }
            $courses[] = $course;
        }
        return $courses;
    }

    /* Generate Learning Program Filter for course progress block
     * @return String HTML form with select and search box
     */
    public static function get_lps() {
        global $DB;
        $fields = "id, name, shortname, courses";
        $form = new MoodleQuickForm('learningprogram', 'post', '#');
        $records = $DB->get_records('wdm_learning_program', array(), '', $fields);

        $lps = array();
        foreach ($records as $lp) {
            /* If there in no courses available */
            if (empty(json_decode($lp->courses))) {
                continue;
            }

            /* If there in no userss available */
            $lpenrolment = $DB->get_records("wdm_learning_program_enrol", array("learningprogramid" => $lp->id), "userid");
            if (empty($lpenrolment)) {
                continue;
            }

            $lps[] = array(
                "id" => $lp->id,
                "fullname" => $lp->name
            );
        }

        return $lps;
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