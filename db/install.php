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
 * @package     local_sitereport
 * @category    upgrade
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Custom code to be run on installing the plugin.
 */
function xmldb_local_sitereport_install() {
    global $CFG, $DB;

    require_once($CFG->dirroot . "/local/sitereport/classes/constants.php");

    // All Default blocks.
    $defaultblocks = array(
        'activeusers' => array(
            'classname' => 'activeusersblock',
            'position' => 0,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE,
            LOCAL_SITEREPORT_BLOCK_MOBILE_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'courseprogress' => array(
            'classname' => 'courseprogressblock',
            'position' => 1,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE,
            LOCAL_SITEREPORT_BLOCK_MOBILE_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'activecourses' => array(
            'classname' => 'activecoursesblock',
            'position' => 2,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE,
            LOCAL_SITEREPORT_BLOCK_MOBILE_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'certificates' => array(
            'classname' => 'certificatesblock',
            'position' => 3,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE,
            LOCAL_SITEREPORT_BLOCK_MOBILE_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'liveusers' => array(
            'classname' => 'liveusersblock',
            'position' => 4,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE,
            LOCAL_SITEREPORT_BLOCK_MOBILE_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'siteaccess' => array(
            'classname' => 'siteaccessblock',
            'position' => 5,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE,
            LOCAL_SITEREPORT_BLOCK_MOBILE_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'todaysactivity' => array(
            'classname' => 'todaysactivityblock',
            'position' => 6,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE,
            LOCAL_SITEREPORT_BLOCK_MOBILE_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        ),
        'inactiveusers' => array(
            'classname' => 'inactiveusersblock',
            'position' => 7,
            LOCAL_SITEREPORT_BLOCK_DESKTOP_VIEW => LOCAL_SITEREPORT_BLOCK_MEDIUM,
            LOCAL_SITEREPORT_BLOCK_TABLET_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE,
            LOCAL_SITEREPORT_BLOCK_MOBILE_VIEW => LOCAL_SITEREPORT_BLOCK_LARGE
        )
    );

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

    return $DB->insert_records('sitereport_blocks', $blocks);
}
