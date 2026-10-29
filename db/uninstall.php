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
 * Code that is executed before the tables and data are dropped during the plugin uninstallation.
 *
 * @package     local_edwiserreports
 * @category    upgrade
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/edwiserreports/lib.php');

/**
 * Custom uninstallation procedure.
 */
function xmldb_local_edwiserreports_uninstall() {
    global $DB, $CFG;

    require_once($CFG->dirroot . '/local/edwiserreports/lib.php');

    $blocks = local_edwiserreports_get_default_block_settings();

    foreach (array_keys($blocks) as $blockid) {
        $DB->delete_records('user_preferences', array('name' => 'pref_' . $blockid . 'block'));
    }

    // Delete Dashboard url from custommenuitems on uninstall.
    $links = explode("\n", get_config('core', 'custommenuitems'));
    foreach ($links as $key => $link) {
        if (strpos($link, "/local/edwiserreports/index.php") !== false) {
            unset($links[$key]);
            break;
        }
    }
    set_config('custommenuitems', implode("\n", $links));
    return true;
}
