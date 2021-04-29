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
 * Local Course Progress Manager Plugin Database Comtroller.
 *
 * @package     local_edwiserreports
 * @category    admin
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* Local course progress manager namespace */
namespace local_edwiserreports;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/completionlib.php');

use moodle_exception;
use completion_info;
use context_course;
use stdClass;

/**
 * Course Progress Database Controller
 */
class db_controller {
    /**
     * Course Progress Table Name
     * @var string | boolean
     */
    public $progresstable = false;

    /**
     * Custructor for course progress database
     */
    public function __construct() {
        // Set Progress Table Name.
        $this->progresstable = 'edwreports_course_progress';
    }

    /**
     * Set course module completions
     * @param  stdClass $params Data to be inserted
     * @return bool|int       Status
     */
    public function update_course_completion($params) {
        global $DB;

        // Get progress if already inserted in table
        // Check if course progress is available or not.
        $progress = $DB->get_record($this->progresstable, array(
            'courseid' => $params->courseid,
            'userid' => $params->userid
        ));

        // If progress data is available then update record.
        if ($progress) {
            // Update records in database.
            $params->id = $progress->id;
            return $DB->update_record($this->progresstable, $params);
        } else {
            // Insert records in database.
            return $DB->insert_record($this->progresstable, $params);
        }
    }

    /**
     * Get course module completions
     * @param  stdClass $params Data to be updated
     * @return bool|int            Status
     */
    public function get_course_completion($params) {
        global $DB;

        // Get records which has course completion criteria.
        $params->criteria = 1;

        // Update records in database.
        return $DB->get_record($this->progresstable, (array) $params);
    }

    /**
     * Delete course module completions
     * @param  stdClass $params Data to be updated
     * @return bool|int            Status
     */
    public function delete_course_completion($params) {
        global $DB;

        // If params is null the return moodle exception.
        if (!$params || empty((array) $params)) {
            new moodle_exception(get_string('invalidparam', 'local_edwiserreports'));
        }

        // If paramter i object.
        if (is_object($params)) {
            $params = (array) $params;
        }

        // Delete records from database.
        return $DB->delete_records($this->progresstable, $params);
    }

    /**
     * Get total course activities
     * @param  stdClass $course Course Object
     * @return array            Array of activities
     */
    public function get_completable_activities($course) {
        // Get course completion object.
        $completion = new completion_info($course);

        // If completion is not enable then return false.
        if (!$completion->is_enabled()) {
            // If course completions enable then return
            // empty array of activities.
            return array();
        }

        // Get the number of modules that support completion.
        return $completion->get_activities();
    }

    /**
     * Sync all old users to course progress
     * @return bool Sync Process Status
     */
    public function sync_old_users_with_course_progress() {
        global $DB;

        // Get all courses.
        $courses = get_courses();

        // Parse all courses.
        $progressdata = array();
        foreach ($courses as $cid => $course) {
            // Skip system course (Moodle Default Course)
            // which is always a courseid equal to '1'.
            if ($course->id == 1) {
                continue;
            }

            // Get course context.
            $coursecontext = context_course::instance($course->id);

            // Get all enrolled users with role students
            // 'moodle/course:isincompletionreports' - this capability is allowed to only students
            // This will return all learners in a course.
            $users = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');

            // For each users create entry in database.
            foreach ($users as $uid => $user) {
                // Prepare data to insert in array
                // Add this data in course progress data in
                // progress data array.
                $progressdata[] = (object) array (
                    'courseid' => $course->id,
                    'userid' => $user->id
                );
            }
        }

        // Insert records in database.
        return $DB->insert_records($this->progresstable, $progressdata);
    }

    /**
     * Get all changable records
     * @return array All changeble records
     */
    public function get_course_progress_changeble_records() {
        global $DB;

        // Return changeble records.
        return $DB->get_records($this->progresstable, array(
            'pchange' => 1
        ));
    }

    /**
     * Get course course completion information
     * @param  int $course Course Object
     * @param  int $userid User ID
     * @return stdClass    Completions information
     */
    public function get_course_completion_info($course = false, $userid = false) {
        global $COURSE, $USER;

        // If course dosn't pass then select current course.
        if (!$course) {
            $course = $COURSE;
        }

        // If user id not passed then select current users id.
        if (!$userid) {
            $userid = $USER->id;
        }

        // Set completion info default false.
        $completioninfo = false;

        // Get course context.
        $coursecontext = context_course::instance($course->id);

        // Check if user is enrolled in course.
        if (is_enrolled($coursecontext, $userid)) {
            // Get completion object.
            $completion = new \completion_info($course);

            // If completion enable then only set all completion values.
            if ($completion->is_enabled()) {
                // Prepare completion information object.
                $completioninfo = new stdClass();

                // Get progress percantage.
                $percentage = \core_completion\progress::get_course_progress_percentage($course, $userid);

                // Get all available modules.
                $modules = $completion->get_activities();

                // Set default values for completion info object.
                $completioninfo->totalmodules = 0;
                $completioninfo->completedmodules = null;
                $completioninfo->completedmodulescount = 0;
                $completioninfo->progress = 0;
                $completioninfo->timecompleted = null;

                // If percentage is not null then calculate percentage.
                if (!is_null($percentage)) {
                    // Get floor value of percentage.
                    $percentage = floor($percentage);

                    // Else calculate the completted activities.
                    $completioninfo->progress = $percentage;

                    // Get completed and incompleted modules.
                    $completedmodules = array();
                    foreach ($modules as $module) {
                        // If activity is label then ignore.
                        if ($module->modname == "label") {
                            continue;
                        }

                        // Get course module data.
                        $data = $completion->get_data($module, false, $userid);
                        // If completion status is set then increase
                        // Completion count.
                        if ($data->completionstate) {
                            // Total modules.
                            $completioninfo->totalmodules++;

                            $completedmodules[] = $module->id;
                            $completioninfo->completedmodulescount++;

                            // Get the last time completed activity.
                            if ($completioninfo->timecompleted < $data->timemodified) {
                                $completioninfo->timecompleted = $data->timemodified;
                            }
                        }
                    }

                    // If completed module is not empty.
                    if (!empty($completedmodules)) {
                        // Get completed modules.
                        $completioninfo->completedmodules = implode(',', $completedmodules);
                    }

                    // If completion is not 100% then remove completion time.
                    if ($completioninfo->progress != 100) {
                        $completioninfo->timecompleted = null;
                    }
                }
            }
        }
        // Return completion information about course and user.
        return $completioninfo;
    }

    /**
     * Update course completion module count
     * @param  stdClass $params Data to be inserted
     * @return bool|int       Status
     */
    public function update_course_progress_table($params) {
        global $DB;

        if (isset($params->isdeleting) && $params->isdeleting) {
            // If we update the pchange value then cron will automatically update the value.
            return $this->course_data_changed(array('courseid' => $params->courseid));
        }

        // Get course.
        $course = get_course($params->courseid);

        // Get total completable activity count.
        $totalmodules = count($this->get_completable_activities($course));

        // If we update the pchange value then cron will automatically update the value.
        return $DB->set_field($this->progresstable, 'totalmodules', $totalmodules, array('courseid' => $params->courseid));
    }

    /**
     * Course completions changed
     * @param  stdClass $params Data to be inserted
     * @return bool|int       Status
     */
    public function course_data_changed($params) {
        global $DB;

        // If we update the pchange value then cron will automatically update the value.
        return $DB->set_field($this->progresstable, 'pchange', '1', (array) $params);
    }

    /**
     * Check if completion is updated in course
     * @param  int     $courseid Course Id
     * @return boolean           Completion Updated Status
     */
    public function is_completion_updated($courseid) {
        global $DB;

        // Get course.
        $course = $DB->get_record('course', array('id' => $courseid));

        // Get completion object.
        $completion = new \completion_info($course);

        // Get completion detail from course progress table.
        $sql = 'SELECT courseid, criteria
                FROM {' . $this->progresstable . '}
                WHERE courseid = :courseid
                GROUP BY courseid, criteria';
        $progress = $DB->get_record_sql($sql, array('courseid' => $courseid));

        // If there is no data in table then.
        // Return false.
        if (!$progress) {
            return false;
        }

        // If progress criteria is diffrent then the
        // current completion return true.
        if ($progress->criteria != $completion->is_enabled()) {
            return true;
        }
        return false;
    }
}
