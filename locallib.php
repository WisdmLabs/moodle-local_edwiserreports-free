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
 * @param  [string] $url Url prifix to get export link
 * @param  [stdClass] $data Object for additional data
 * @return [string] moodle link detail
 */
function get_block_exportlinks($url, $data) {
    $links = new stdClass();

    if (isset($data->firstcourseid)) {
        $cpfilter = $data->firstcourseid;
    } else {
        $cpfilter = false;
    }

    $links->blockactiveusers = get_exportlinks($url, "block", "activeusers", "weekly");
    $links->blockactivecourses = get_exportlinks($url, "block", "activecourses");
    $links->blockcourseprogress = get_exportlinks($url, "block", "courseprogress", $cpfilter);

    $links->blockcertificates = get_exportlinks($url, "block", "certificates");
    $links->blockf2fsessions = get_exportlinks($url, "block", "f2fsession");
    return $links;
}

/**
 * Get Export Link to export link array
 * @param  [string] $prifix Url url for export link
 * @param  [string] $region Region for export
 * @param  [string] $blockname Block to export
 * @param  [string] $filter Filter for data to export
 * @return [array] Array of export link
 */
function get_exportlinks($url, $region, $blockname, $filter = false) {
    $out = new stdClass();

    $params = array(
        "region" => $region,
        "blockname" => $blockname
    );

    if ($filter) {
        $params["filter"] = $filter;
    }

    $out->export = get_exportlink_array($url, $params);
    return $out;
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