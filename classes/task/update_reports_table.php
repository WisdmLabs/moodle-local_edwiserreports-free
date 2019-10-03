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

require_once($CFG->libdir . "/enrollib.php");

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

        // Completions table report plugin
        $tablename = "elucidsitereport_completion";

        // Get all courses to get completion
        $courses = get_courses();

        // Get report plugin completion data
        $completions = $DB->get_records($tablename);

        $data = array();
        // Get completions for each courses
        foreach($courses as $course) {
            $coursecontext = context_course::instance($course->id);
            // Get all student users
            $users = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');
            foreach($users as $user) {
                // Get completions for courses
                $completion = \report_elucidsitereport\utility::get_course_completion_info($course, $user->id);

                // If completion is not empty then update progress percentage
                $progressper = $completedactivities = 0;
                if (!empty($completion)) {
                    $completion = (object) $completion;
                    $progressper = $completion->progresspercentage;
                    $completedactivities = $completion->completedactivities;
                }

                // Get Course Grades
                $coursegrade = 0;
                $grades = \report_elucidsitereport\utility::get_grades($course->id, $user->id);
                // If course grade is set then update course grade
                if ($grades && $grades->finalgrade) {
                    $coursegrade = $grades->finalgrade;
                }

                // Get Course Comletion Time
                $timecompleted = \report_elucidsitereport\utility::get_time_completion($course->id, $user->id);

                // Created data oabject
                $dataobject = array(
                    "courseid" => $course->id,
                    "userid" => $user->id,
                    "completion" => $progressper,
                    "completedactivities" => $completedactivities,
                    "grade" => $coursegrade,
                    "timecompleted" => $timecompleted
                );

                // Completion param to get time completion
                $completionparam = array(
                    "courseid" => $course->id,
                    "userid" => $user->id
                );
            
                // Get previous completion recordid
                $prevcompletion = $DB->get_record($tablename, $completionparam, "id");
                if ($prevcompletion) {
                    unset($completions[$prevcompletion->id]);

                    // If same record then dont update
                    if ($DB->record_exists($tablename, $dataobject)) {
                        continue;
                    }

                    mtrace(get_string('updatinguserrecord', 'report_elucidsitereport', $completionparam));
                    // If exist then Update records
                    $dataobject["id"] = $prevcompletion->id;
                    $DB->update_record($tablename, $dataobject);
                } else {
                    // Save data to inseart ar the end
                    mtrace(get_string('gettinguserrecord', 'report_elucidsitereport', $completionparam));
                    $data[] = $dataobject;
                }
            }
        }

        // Delete records if enrolment is over
        foreach($completions as $completion) {
            // Delete record id no longer enrolments
            mtrace(get_string('deletingguserrecord',
                'report_elucidsitereport',
                array(
                    "courseid" => $completion->courseid,
                    "userid" => $completion->userid
                )
            ));
            $DB->delete_records_select($tablename, "id = $completion->id");
        }

        // If not exist then insert records
        mtrace(get_string('creatinguserrecord', 'report_elucidsitereport'));
        $DB->insert_records($tablename, $data);
        mtrace(get_string('updatingrecordended', 'report_elucidsitereport'));
    }
}