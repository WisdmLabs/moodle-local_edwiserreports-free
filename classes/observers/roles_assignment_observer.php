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

/**
 * Roles Assignment observer
 */
trait roles_assignment_observer {
    /**
     * Get event data
     * @param  object $event Event object
     * @return object        Role assignment event data
     */
    protected static function get_roles_assignment_eventdata($event) {
        // Get event related data.
        $eventdata = (object) $event->get_data();

        // Get role assignment data.
        $roledata = $event->get_record_snapshot(
            $eventdata->objecttable,
            $eventdata->objectid
        );

        // If role is not assigned as student then
        // don't save the records in database.
        if ($roledata->archetype !== CPM_STUDENTS_ARCHETYPE) {
            return false;
        }

        // Prepare course completion module data.
        $data = new stdClass();
        $data->userid = (int) $eventdata->relateduserid;
        $data->courseid = (int) $eventdata->courseid;

        // Return event data which is required.
        return $data;
    }

    /**
     * Role assigned event
     * @param \core\event\role_assigned $event Event Data
     */
    public static function role_assigned(\core\event\role_assigned $event) {
        // If event data is there then create progress data.
        if ($data = self::get_roles_assignment_eventdata($event)) {
            // Get datatbase controller.
            $dbc = new \local_edwiserreports\db_controller();

            // Create course module completion.
            $dbc->update_course_completion($data);
        }
    }

    /**
     * Role unassigned event
     * @param \core\event\role_unassigned $event Event Data
     */
    public static function role_unassigned(\core\event\role_unassigned $event) {
        // If event data is there then delet progress data.
        if ($data = self::get_roles_assignment_eventdata($event)) {
            // Get datatbase controller.
            $dbc = new \local_edwiserreports\db_controller();

            // Create course module completion.
            $dbc->delete_course_completion($data);
        }
    }
}
