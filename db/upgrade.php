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
 * Code to be executed after the plugin's database scheme has been installed is defined here.
 *
 * @package     local_edwiserreports
 * @category    upgrade
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Custom code to be run on upgrading the plugin.
 * @param int $oldversion Plugin's old version
 * @return bool True if upgrade successful
 */
function xmldb_local_edwiserreports_upgrade($oldversion) {
    global $DB;

    // Check the old version.
    if (2020030400 <= $oldversion) {
        // Table name to be removed.
        $tablename = 'edwiserReport_completion';

        // Get all tables.
        $tables = $DB->get_tables();

        // If table exist.
        if (isset($tables[$tablename])) {
            $DB->execute('DROP table {' . $tablename . '}');
        }
    }

    // Return true.
    return true;
}
