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

// Require files.
require_once($CFG->dirroot . '/local/edwiserreports/classes/db_controller.php');
require_once($CFG->dirroot . '/local/edwiserreports/classes/constants.php');

trait authentication {
    /**
     * User logged in event
     * Delete previous secret key(If any).
     * Then create new secret key.
     * @param \core\event\user_loggedin $event Event Data
     */
    public static function user_loggedin(\core\event\user_loggedin $event) {
        global $DB;

        $userid = $event->get_data()['userid'];

        $authentication = new \local_edwiserreports\controller\authentication();
        $authentication->create_secret_key($userid);
    }

    /**
     * User logged in event.
     * Delete user's secret key.
     * @param \core\event\user_loggedout $event Event Data
     */
    public static function user_loggedout(\core\event\user_loggedout $event) {
        global $DB;

        $userid = $event->get_data()['userid'];
        $authentication = new \local_edwiserreports\controller\authentication();
        $authentication->delete_secret_key($userid);
    }
}
