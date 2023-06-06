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

namespace local_edwiserreports\task;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . "/local/edwiserreports/classes/constants.php");

use local_edwiserreports\controller\progress;
use local_edwiserreports\utility;
use context_course;

/**
 * Scheduled Task to Update Report Plugin Table.
 */
class active_courses_data extends \core\task\scheduled_task {

    /**
     * Object to show progress of task
     * @var \local_edwiserreports\task\progress
     */
    private $progress;

    /**
     * Can run cron task.
     *
     * @return boolean
     */
    public function can_run(): bool {
        return true;
    }

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('activecoursestask', 'local_edwiserreports');
    }

    /**
     * Construct
     */
    public function __construct() {

        $this->progress = new progress('activecourses');

    }

    /**
     * Execute the task.
     */
    public function execute() {
        global $DB;

        $courses = get_courses();
        unset($courses[1]);

        // Start progress.
        $this->progress->start_progress();

        $count = 1;
        $activecourses = array();
        // Calculate Completion Count for All Course.
        $sql = "SELECT courseid, COUNT(userid) AS users
            FROM {edwreports_course_progress}
            WHERE progress = :progress
            GROUP BY courseid";
        $params = array("progress" => 100);
        // Get records with 100% completions.
        $coursecompletion = $DB->get_records_sql($sql, $params);
        $progress = 0;
        $increament = 100 / count($courses);
        foreach ($courses as $course) {
            $progress += $increament;
            // Get Course Context.
            $coursecontext = context_course::instance($course->id);

            // Get Enrolled users
            // 'moodle/course:isincompletionreports' - this capability is allowed to only students.
            $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');
            if (empty($enrolledstudents)) {
                continue;
            }

            // Create a record for responce.
            $res = array(
                $count++,
                format_string($course->fullname, true, ['context' => \context_system::instance()])
            );

            $res[] = count($enrolledstudents);

            // Get Completion count.
            if (!isset($coursecompletion[$course->id])) {
                $completedusers = 0;
            } else {
                $completedusers = $coursecompletion[$course->id]->users;
            }

            $res[] = $this->get_courseview_count($course->id, array_keys($enrolledstudents));
            $res[] = $completedusers;
            $activecourses[$course->id] = $res;

            // Update progress.
            $this->progress->update_progress($progress);
        }

        // End progress.
        $this->progress->end_progress();
        set_config('activecoursesdata', json_encode($activecourses), 'local_edwiserreports');
        return true;
    }

    /**
     * Get Course View Count by users
     * @param  int   $courseid         Course Id
     * @param  array $studentsids      Array of enrolled uesers id
     * @return int                     Number of course views by users
     */
    public function get_courseview_count($courseid, $studentsids) {
        global $DB;

        // Temporary course table.
        $userstable = utility::create_temp_table('tmp_ac_s', $studentsids);
        $params = array();

        // Students join.
        $join = " INNER JOIN {" . $userstable  . "} u ON logs.userid = u.tempid";

        $sqlcourseview = "SELECT COUNT(DISTINCT userid) as usercount
            FROM {logstore_standard_log} logs
            $join
            WHERE logs.action = :action
            AND logs.courseid = :courseid";

        $params['courseid'] = $courseid;
        $params['action'] = 'viewed';
        $views = $DB->get_record_sql($sqlcourseview, $params);

        // Drop temporary table.
        utility::drop_temp_table($userstable);
        return $views->usercount;
    }
}
