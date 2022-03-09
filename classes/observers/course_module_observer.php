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
 * Local Course Progress Manager Plugin Events Onserver.
 *
 * @package     local_edwiserreports
 * @category    admin
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Local Course Progress Manager Namespace
 */
namespace local_edwiserreports\observers;

defined('MOODLE_INTERNAL') || die();

use stdClass;

// Require files.
require_once($CFG->dirroot . '/local/edwiserreports/classes/db_controller.php');
require_once($CFG->dirroot . '/local/edwiserreports/classes/constants.php');

trait course_module_observer {
    /**
     * Get event data
     * @param  object $event Event data
     * @return object        Course module event data
     */
    protected static function get_course_module_eventdata($event) {
        // Get event related data.
        $eventdata = (object) $event->get_data();

        // Prepare course completion module data.
        $data = new stdClass();
        $data->courseid = (int) $eventdata->courseid;
        $data->moduleid = (int) $eventdata->objectid;

        // If userid is set then get userid.
        if (isset($eventdata->relateduserid)) {
            $data->userid = (int) $eventdata->relateduserid;
        }

        // Return event data which is required.
        return $data;
    }

    /**
     * Course module create event
     * @param \core\event\course_module_created $event Event Data
     */
    public static function course_module_created(\core\event\course_module_created $event) {
        // If course module event data is present then
        // update course progress records.
        if ($data = self::get_course_module_eventdata($event)) {
            // Get datatbase controller.
            $dbc = new \local_edwiserreports\db_controller();

            // This is not deleting event.
            $data->isdeleting = false;

            // Create course module completion.
            $dbc->update_course_progress_table($data);
        }
    }

    /**
     * Course module create event
     * @param \core\event\course_module_deleted $event Event Data
     */
    public static function course_module_deleted(\core\event\course_module_deleted $event) {
        // If course module event data is present then
        // update course progress records.
        if ($data = self::get_course_module_eventdata($event)) {
            // Get datatbase controller.
            $dbc = new \local_edwiserreports\db_controller();

            // If deleting the module then remove data from
            // completed module as well.
            $data->isdeleting = true;

            // Create course module completion.
            $dbc->update_course_progress_table($data);
        }
    }

    /**
     * Course module updated event
     * @param \core\event\course_module_updated $event Event Data
     */
    public static function course_module_updated(\core\event\course_module_updated $event) {
        // If course module event data is present then
        // update course progress records.
        if ($data = self::get_course_module_eventdata($event)) {
            // Get datatbase controller.
            $dbc = new \local_edwiserreports\db_controller();

            // Create course module completion.
            $dbc->course_data_changed(array('courseid' => $data->courseid));
        }
    }

    /**
     * Course module completion update
     * @param  \core\event\course_module_completion_updated $event Event Data
     */
    public static function course_module_completion_updated(\core\event\course_module_completion_updated $event) {

        // If course module event data is present.
        if ($data = self::get_course_module_eventdata($event)) {
            // If data contain userid.
            if (isset($data->userid)) {
                // Get datatbase controller.
                $dbc = new \local_edwiserreports\db_controller();

                // Update course module completion.
                $dbc->course_data_changed(array(
                    'courseid' => $data->courseid,
                    'userid' => $data->userid
                ));
            }
        }
    }
}
