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
 * @package     report_elucidsitereport
 * @category    upgrade
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Custom code to be run on installing the plugin.
 */
function xmldb_report_elucidsitereport_install() {
    global $CFG, $DB;

    require_once($CFG->dirroot . "/report/elucidsitereport/classes/constants.php");

    // All Default blocks
    $defaultblocks = array(
        'activeusers' => array(
            'classname' => 'activeusersblock',
            'position' => 0,
            BLOCK_DESKTOP_VIEW => BLOCK_LARGE,
            BLOCK_TABLET_VIEW => BLOCK_LARGE,
            BLOCK_MOBILEVIEW => BLOCK_LARGE
        ),
        'courseprogress' => array(
            'classname' => 'courseprogressblock',
            'position' => 1,
            BLOCK_DESKTOP_VIEW => BLOCK_MEDIUM,
            BLOCK_TABLET_VIEW => BLOCK_LARGE,
            BLOCK_MOBILEVIEW => BLOCK_LARGE
        ),
        'activecourses' => array(
            'classname' => 'activecoursesblock',
            'position' => 2,
            BLOCK_DESKTOP_VIEW => BLOCK_MEDIUM,
            BLOCK_TABLET_VIEW => BLOCK_LARGE,
            BLOCK_MOBILEVIEW => BLOCK_LARGE
        ),
        'certificates' => array(
            'classname' => 'certificatesblock',
            'position' => 3,
            BLOCK_DESKTOP_VIEW => BLOCK_MEDIUM,
            BLOCK_TABLET_VIEW => BLOCK_LARGE,
            BLOCK_MOBILEVIEW => BLOCK_LARGE
        ),
        'liveusers' => array(
            'classname' => 'liveusersblock',
            'position' => 4,
            BLOCK_DESKTOP_VIEW => BLOCK_MEDIUM,
            BLOCK_TABLET_VIEW => BLOCK_LARGE,
            BLOCK_MOBILEVIEW => BLOCK_LARGE
        ),
        'siteaccess' => array(
            'classname' => 'siteaccessblock',
            'position' => 5,
            BLOCK_DESKTOP_VIEW => BLOCK_MEDIUM,
            BLOCK_TABLET_VIEW => BLOCK_LARGE,
            BLOCK_MOBILEVIEW => BLOCK_LARGE
        ),
        'todaysactivity' => array(
            'classname' => 'todaysactivityblock',
            'position' => 6,
            BLOCK_DESKTOP_VIEW => BLOCK_MEDIUM,
            BLOCK_TABLET_VIEW => BLOCK_LARGE,
            BLOCK_MOBILEVIEW => BLOCK_LARGE
        ),
        'inactiveusers' => array(
            'classname' => 'inactiveusersblock',
            'position' => 7,
            BLOCK_DESKTOP_VIEW => BLOCK_MEDIUM,
            BLOCK_TABLET_VIEW => BLOCK_LARGE,
            BLOCK_MOBILEVIEW => BLOCK_LARGE
        )
    );

    // Create each block
    $blocks = array();
    foreach ($defaultblocks as $key => $block) {
        $blockdata = new stdClass();
        $blockdata->blockname = $key;
        $blockdata->classname = $block['classname'];
        $blockdata->blocktype = BLOCK_TYPE_DEFAULT;
        $blockdata->blockdata = json_encode((object) array(
            BLOCK_DESKTOP_VIEW => $block[BLOCK_DESKTOP_VIEW],
            BLOCK_TABLET_VIEW => $block[BLOCK_TABLET_VIEW],
            BLOCK_MOBILEVIEW => $block[BLOCK_MOBILEVIEW],
            'position' => $block['position']
        ));
        $blockdata->timecreated = time();
        $blocks[] = $blockdata;
    }

    return $DB->insert_records('sitereport_blocks', $blocks);
}
