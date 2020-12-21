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

require_once($CFG->dirroot . '/local/edwiserreports/lib.php');

/**
 * Custom code to be run on installing the plugin.
 */
function xmldb_local_edwiserreports_install() {
    global $CFG, $DB;

    set_config('edwiserreportsinstallation', true, 'local_edwiserreports');

    // All Default blocks.
    $defaultblocks = local_edwiserreports_get_default_block_settings();

    // Create each block.
    $blocks = array();
    foreach ($defaultblocks as $key => $block) {
        $blockdata = new stdClass();
        $blockdata->blockname = $key;
        $blockdata->classname = $block['classname'];
        $blockdata->blocktype = LOCAL_SITEREPORT_BLOCK_TYPE_DEFAULT;
        $blockdata->blockdata = json_encode((object) array(
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => $block[LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW],
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => $block[LOCAL_SITEREPORT_BLOCK_TABLET_VIEW],
            LOCAL_SITEREPORT_BLOCK_MOBILE_VIEW => $block[LOCAL_SITEREPORT_BLOCK_MOBILE_VIEW],
            'position' => $block['position']
        ));
        $blockdata->timecreated = time();
        $blocks[] = $blockdata;
    }

    // Database controller.
    $dbcontroller = new local_edwiserreports\db_controller();

    // Sync all users in installations process.
    $completionupgrade = $dbcontroller->sync_old_users_with_course_progress();
    $reportpluginupgrade = $DB->insert_records('edwreports_blocks', $blocks);

    return $completionupgrade && $reportpluginupgrade;
}
