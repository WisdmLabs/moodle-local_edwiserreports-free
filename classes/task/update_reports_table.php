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

namespace report_elucidsitereport\task;

defined('MOODLE_INTERNAL') || die();

use stdClass;
use context_course;
 
/**
 * Scheduled Task to Update Report Plugin Table.
 */
class update_reports_table extends \core\task\scheduled_task {
 
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('updatetables', 'report_elucidsitereport');
    }
 
    /**
     * Execute the task.
     */
    public function execute() {
        global $DB;

        // Report Completion Table
        $tablename = "elucidsitereport_completion";

        // Get all courses to get completion
        $courses = get_courses();

        mtrace(get_string('updatingrecordstarted', 'report_elucidsitereport'));
        foreach ($courses as $course) {
            // Get Course Context
            $coursecontext = context_course::instance($course->id);

            // Get all students from course
            $students = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');

            // For all students calculate completions
            foreach ($students as $user) {
                mtrace(get_string('updatinguserrecord', 'report_elucidsitereport',
                    array('userid' => $user->id, 'courseid' => $course->id)));
                // Get Completion for Course
                $progressper = 0;
                $completion = (object) \report_elucidsitereport\utility::get_course_completion_info($course, $user->id);
                if ($completion) {
                    $progressper = $completion->progresspercentage;
                }
                
                // Get Course Grades
                $coursegrade = 0;
                $grades = \report_elucidsitereport\utility::get_grades($course->id, $user->id);
                if ($grades && $grades->finalgrade) {
                    $coursegrade = $grades->finalgrade;
                }

                // Get Course Comletion Time
                $timecompleted = \report_elucidsitereport\utility::get_time_completion($course->id, $user->id);

                // Create data object
                $dataobject = (object) array(
                    "courseid" => $course->id,
                    "userid" => $user->id,
                    "completion" => $progressper,
                    "grade" => $coursegrade,
                    "timecompleted" => $timecompleted
                );

                // Params to get records
                $params = array(
                    "courseid" => $course->id,
                    "userid" => $user->id
                );

                // Update/Insert Records in Reports Table
                if ($record = $DB->get_record($tablename, $params, "id")) {
                    $dataobject->id = $record->id;
                    // If exist then Update records
                    $DB->update_record($tablename, $dataobject);
                } else {
                    // If not exist then insert records
                    $DB->insert_record($tablename, $dataobject);
                }
            }
        }
        mtrace(get_string('updatingrecordended', 'report_elucidsitereport'));
    }
}