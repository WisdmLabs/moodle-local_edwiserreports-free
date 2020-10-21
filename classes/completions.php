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
 * @package     local_edwiserreports
 * @category    admin
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all constants for use
 */

namespace local_edwiserreports;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->dirroot . '/local/edwiserreports/classes/utility.php');

use completion_info;
use core_completion\progress;
use context_course;
use stdClass;

/**
 * Added completions class to manage all completions in reports plugin
 */
class completions {
    /**
     * Completions table name for plugin
     * @var string
     */
    public $tablename = "edwreports_course_progress";


    /**
     * Update local completion table to get completions from
     * @return [type] [description]
     */
    public function update_local_completion_table() {
        global $DB;

        // Get all courses.
        $courses = get_courses();

        // Get completion for each course
        // and update in our completion tables.
        $dataobjects = array();
        foreach ($courses as $course) {
            // Return for system course.
            if ($course->id == 1) {
                continue;
            }

            // Get completion info object to get course completion.
            $completioninfo = new completion_info($course);

            // If completion is not enable then continue.
            if (!$completioninfo->is_enabled()) {
                $DB->delete_records($this->tablename, array('courseid' => $course->id));
                continue;
            }

            // Get course context.
            $coursecontext = context_course::instance($course->id);

            // Get all enrolled students
            // 'moodle/course:isincompletionreports' - this capability is allowed to only students.
            $enrolledlearners = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');

            // For each learners get completions.
            foreach ($enrolledlearners as $user) {
                // Get progress percentage from a course.
                $percentage = progress::get_course_progress_percentage($course, $user->id);

                // Prepare completions object.
                $completionobj = new stdClass();
                $completionobj->courseid = $course->id;
                $completionobj->userid = $user->id;
                $completionobj->completion = 0;
                $completionobj->timecompleted = null;
                $completionobj->grade = 0;
                $completionobj->completedactivities = 0;

                // If percentage is not null then.
                if (!is_null($percentage)) {
                    $completionobj->completion = $percentage;

                    // Get modules.
                    $modules = $completioninfo->get_activities();
                    if (!empty($modules)) {
                        $completed = 0;
                        foreach ($modules as $module) {
                            $data = $completioninfo->get_data($module, true, $user->id);
                            if ($data->completionstate != COMPLETION_INCOMPLETE) {
                                $completed += 1;
                                if ($completionobj->timecompleted < $data->timemodified) {
                                    $completionobj->timecompleted = $data->timemodified;
                                }
                            }
                        }
                        $completionobj->completedactivities = $completed;
                    }

                    // If all activities are not completed then set timecompleted to null.
                    if ($completionobj->completion != 100) {
                        $completionobj->timecompleted = null;
                    }

                    // Get Course Grades.
                    $completionobj->grade = 0;
                    $grades = \local_edwiserreports\utility::get_grades($course->id, $user->id);
                    // If course grade is set then update course grade.
                    if ($grades && $grades->finalgrade) {
                        $coursegrade = $grades->finalgrade;
                    }
                }

                // If record exist then.
                $params = array('courseid' => $course->id, 'userid' => $user->id);
                if ($prevrecord = $DB->get_record($this->tablename, $params, "id")) {
                    // If same record then dont update.
                    if ($DB->record_exists($this->tablename, (array)$completionobj)) {
                        continue;
                    }
                    // If exist then Update records.
                    $completionobj->id = $prevrecord->id;
                    $DB->update_record($this->tablename, $completionobj);
                } else {
                    // Save data to insert ar the end.
                    $dataobjects[] = $completionobj;
                }
            }
        }

        // If dataobject is not empty then insert records.
        if (!empty($dataobjects)) {
            // Insert records in database for completions.
            $DB->insert_records($this->tablename, $dataobjects);
        }
    }

    /**
     * Get course completions for each users
     * @param  [int] $courseid  Course ID
     * @return [array]          Array of completions
     */
    public function get_course_completions($courseid) {
        global $DB;
        $completions = $DB->get_records($this->tablename, array(
            'courseid' => $courseid
        ), '', "userid, progress as completion, completiontime");
        return $completions;
    }
}
