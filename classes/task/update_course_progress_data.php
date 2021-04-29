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
 * Local Course Progress Manager Plugin Events Observer.
 *
 * @package     local_edwiserreports
 * @category    admin
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports\task;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/local/edwiserreports/classes/db_controller.php');

use context_course;

/**
 * Update course progress data
 */
class update_course_progress_data extends \core\task\scheduled_task {
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('update_course_progress_data', 'local_edwiserreports');
    }

    /**
     * Execute the sheduled task
     */
    public function execute() {
        // Database controller.
        $dbc = new \local_edwiserreports\db_controller();

        // Get updatable record from database.
        $progressdata = $dbc->get_course_progress_changeble_records();

        // Parse all progress data.
        foreach ($progressdata as $data) {
            // Get course.
            $course = get_course($data->courseid);

            // Get course completion.
            $completion = $dbc->get_course_completion_info($course, $data->userid);

            // Get course context.
            $coursecontext = context_course::instance($data->courseid);

            // Get old progress to calculate if course progress is changed.
            $oldprogress = $data->progress;

            // If no completion data then return from here.
            if (!$completion) {
                // Criteria is not set.
                $data->criteria = 0;
            } else {
                // Criteria is not set.
                $data->criteria = 1;

                // Get total modules.
                $data->totalmodules = $completion->totalmodules;

                // Default completed modules.
                $data->completedmodules = null;

                // Default dataprogress.
                $data->progress = 0;

                // Get completed modules.
                if ($completion->completedmodules) {
                    $data->completedmodules = $completion->completedmodules;

                    // Get progress data.
                    $data->progress = $completion->progress;

                    // Get completion time.
                    $data->completiontime = $completion->timecompleted;
                }

                // If criteria is set and course progress in 100%
                // then trigger edw_course_completion event.
                if (
                    $data->criteria == 1 && // If completion criteria is set.
                    $data->progress == 100 && // If progress in 100%.
                    $oldprogress != $data->progress // If progress is changed.
                ) {
                    // Create a course completion event.
                    $event = \local_edwiserreports\event\edw_course_completed::create(array(
                        'context' => $coursecontext,
                        'objectid' => $data->id
                    ));

                    // Trigger completion event.
                    $event->trigger();
                }
            }

            // Update the pchange value to marked as changed.
            $data->pchange = 0;

            // Set course module records.
            if ($dbc->update_course_completion($data)) {
                // Trigger course progress updated event.
                $event = \local_edwiserreports\event\course_progress_updated::create(array(
                    'context' => $coursecontext,
                    'objectid' => $data->id,
                    'relateduserid' => $data->userid
                ));

                // Trigger completion event.
                $event->trigger();
            }
        }
    }
}
