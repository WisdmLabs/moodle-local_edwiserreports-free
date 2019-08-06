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
 * Get Export Link to export data from blocks and individual page
 * @param  [string] $prefix Url prifix to get export link
 * @return [string] moodle link detail
 */
function get_exportlinks($prefix) {
    $links = new stdClass();
    $links->blockactiveusers = new stdClass();
    $links->blockactivecourses = new stdClass();
    $links->blockactiveusers->export   = get_exportlinks_block_activeusers($prefix);
    $links->blockactivecourses->export = get_exportlinks_block_activecourses($prefix);
    return $links;
}

/**
 * Get Export Link to export data for active users block
 * @param  [string] $prefix Url prifix to get export link
 * @return [string] moodle link detail
 */
function get_exportlinks_block_activeusers($prefix) {
    $url = $prefix."download.php";
    $params = array(
        "region" => "block",
        "action" => "activeusers",
        "filter" => "weekly"
    );

    return get_exportlink_array($url, $params);
}

/**
 * Get Export Link to export data for active users report
 * @param  [string] $prefix Url prifix to get export link
 * @return [string] moodle link detail
 */
function get_exportlinks_report_activeusers($prefix) {
    $url = $prefix."download.php";
    $params = array(
        "region" => "report",
        "action" => "activeusers",
        "filter" => "all"
    );

    return get_exportlink_array($url, $params);
}

/**
 * Get Export Link to export data for active Courses block
 * @param  [string] $prefix Url prifix to get export link
 * @return [string] moodle link detail
 */
function get_exportlinks_block_activecourses($prefix) {
    $url = $prefix."download.php";
    $params = array(
        "region" => "block",
        "action" => "activecourses"
    );

    return get_exportlink_array($url, $params);
}

/**
 * Get Export Link to export link array
 * @param  [string] $url Url for export link
 * @param  [array] $params Prameters for link
 * @return [array] Array of export link
 */
function get_exportlink_array($url, $params) {
    return array(
        array(
            "name" => get_string("csv", "report_elucidsitereport"),
            "icon" => "file-o",
            "link" => new moodle_url($url, array_merge(array("type" => "csv"), $params)),
        ),
        array(
            "name" => get_string("excel", "report_elucidsitereport"),
            "icon" => "file-excel-o",
            "link" => new moodle_url($url, array_merge(array("type" => "excel"), $params)),
        ),
        array(
            "name" => get_string("pdf", "report_elucidsitereport"),
            "icon" => "file-pdf-o",
            "link" => new moodle_url($url, array_merge(array("type" => "pdf"), $params)),
        ),
        array(
            "name" => get_string("email", "report_elucidsitereport"),
            "icon" => "envelope-o",
            "link" => new moodle_url($url, array_merge(array("type" => "copy"), $params)),
        ),
        array(
            "name" => get_string("copy", "report_elucidsitereport"),
            "icon" => "copy",
            "link" => new moodle_url($url, array_merge(array("type" => "copy"), $params)),
        )
    );
}