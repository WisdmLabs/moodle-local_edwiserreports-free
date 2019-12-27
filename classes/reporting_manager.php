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
 * Reportng manager operations are defined here.
 *
 * @package     report_elucidsitereport
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_elucidsitereport;

defined('MOODLE_INTERNAL') || die();

/**
 * Class to make reporting manager related operations.
 */
class reporting_manager
{
	public $userid;
	/**
     * Constructor to create reporting_manager object
     */
	public function __construct()
	{
		global $USER;
		$this->userid = $USER->id;
	}
	/**
	 * Check user is reporting manager or not
	 * @return [boolean] Reporting manager
	 */
	public function check_user_is_reporting_manager() {
		$roles = get_user_roles(\context_system::instance(), $this->userid);
        $rpm = false;
        if (!empty($roles)) {
            foreach ($roles as $role) {
                if ($role->shortname == 'reportingmanager') {
                    $rpm = true;
                }
            }
        }
        return $rpm;
	}
	/**
	 * Get reporting manager students
	 * @return [array] [reporting manager students]
	 */
	public function get_repoting_manager_students() {
		global $DB;
		// Query to get users of reporting manager
        $sql = "SELECT userid FROM {user_info_data} WHERE data = ? OR data IN (SELECT userid FROM {user_info_data} WHERE data = ?)";

        // Get all users who are inactive
        $users = $DB->get_records_sql($sql, array($this->userid, $this->userid));
        $users = array_keys($users);
        return $users;
	}
}