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
 * Plugin administration pages are defined here.
 *
 * @package     report_elucidsitereport
 * @category    admin
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Check whether the plugin is available or not
 * this will return true is plugin is available
 * @param  [string] $plugintype Plugin type to check
 * @param  [string] $puginname Plugin Name
 * @return boolean Return boolean
 */

require_once $CFG->dirroot."/report/elucidsitereport/classes/blocks/active_users_block.php";

/**
 * Get Users List Fragments for diffrent pages
 * @param [array] $args Array of arguments
 * @return [string] HTML table
 */
function report_elucidsitereport_output_fragment_userslist($args) {
    $response = null;
    $page = clean_param($args["page"], PARAM_TEXT);
    $cohortid = clean_param($args["cohortid"], PARAM_TEXT);

    switch ($page) {
        case "activeusers":
            $filter = clean_param($args['filter'], PARAM_TEXT);
            $action = clean_param($args['action'], PARAM_TEXT);

            $response = \report_elucidsitereport\active_users_block::get_userslist_table($filter, $action, $cohortid);
            break;

        case "courseprogress":
            $courseid = clean_param($args['courseid'], PARAM_TEXT);
            $minval = clean_param($args['minval'], PARAM_TEXT);
            $maxval = clean_param($args['maxval'], PARAM_TEXT);

            $response = \report_elucidsitereport\course_progress_block::get_userslist_table($courseid, $minval, $maxval, $cohortid);
            break;
        case "courseengage":
        	$courseid = clean_param($args['courseid'], PARAM_TEXT);
            $action   = clean_param($args['action'], PARAM_TEXT);

            $response = \report_elucidsitereport\courseengage_block::get_userslist_table($courseid, $action, $cohortid);
            break;
    }

    return $response;
}

/**
 * Get Learning Program stats fragment
 * @param [array] $args Array of arguments
 * @return [string] HTML table
 */
function report_elucidsitereport_output_fragment_lpstats($args) {
    global $DB;
    $lpid = clean_param($args["lpid"], PARAM_TEXT);

    return json_encode(\report_elucidsitereport\lpstats_block::get_lpstats_usersdata($lpid));
}
