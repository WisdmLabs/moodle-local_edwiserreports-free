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

require_once($CFG->dirroot . "/cohort/lib.php");

/**
 * Get Export Link to export data from blocks and individual page
 * @param  [string] $url Url prifix to get export link
 * @param  [stdClass] $data Object for additional data
 * @return [string] moodle link detail
 */
function get_block_exportlinks($url, $data) {
    $links = new stdClass();

    /* Course Progress Filter Id*/
    if (isset($data->firstcourseid)) {
        $cpfilter = $data->firstcourseid;
    } else {
        $cpfilter = false;
    }

    if (isset($data->firstlpid)) {
        $lpfilter = $data->firstlpid;
    } else {
        $lpfilter = false;
    }

    $links->blockactiveusers = get_exportlinks($url, "block", "activeusers", "weekly");
    $links->blockactivecourses = get_exportlinks($url, "block", "activecourses");
    $links->blockcourseprogress = get_exportlinks($url, "block", "courseprogress", $cpfilter);
    $links->blockcertificates = get_exportlinks($url, "block", "certificates");
    $links->blockf2fsessions = get_exportlinks($url, "block", "f2fsession");
    $links->blocklpstats = get_exportlinks($url, "block", "lpstats", $lpfilter);
    return $links;
}

/**
 * Get Export Link to export link array
 * @param  [string] $prifix Url url for export link
 * @param  [string] $region Region for export
 * @param  [string] $blockname Block to export
 * @param  [string] $filter Filter for data to export
 * @param  [string] $action Action of a page report
 * @return [array] Array of export link
 */
function get_exportlinks($url, $region, $blockname, $filter = false, $cohortid = false, $action = false) {
    $out = new stdClass();

    $params = array(
        "region" => $region,
        "blockname" => $blockname
    );

    if ($action !== false) {
        $params["action"] = $action;
    }

    if ($filter !== false) {
        $params["filter"] = $filter;
    }

    if ($cohortid !== false) {
        $params["cohortid"] = $cohortid;
    }

    $out->export = get_exportlink_array($url, $blockname, $params);
    return $out;
}

/**
 * Get Export Link to export link array
 * @param  [string] $url Url for export link
 * @param  [string] $blockname Block to export
 * @param  [array] $params Prameters for link
 * @return [array] Array of export link
 */
function get_exportlink_array($url, $blockname, $params) {
    $context = context_system::instance();
    return array(
        array(
            "name" => get_string("csv", "report_elucidsitereport"),
            "icon" => "file-o",
            "link" => (new moodle_url($url, array_merge(array("format" => "csv"), $params)))->out(),
            "action" => 'csv',
            "blockname" => $blockname,
            "contextid" => $context->id
        ),
        array(
            "name" => get_string("excel", "report_elucidsitereport"),
            "icon" => "file-excel-o",
            "link" => (new moodle_url($url, array_merge(array("format" => "excel"), $params)))->out(),
            "action" => 'excel',
            "blockname" => $blockname,
            "contextid" => $context->id
        ),
        array(
            "name" => get_string("pdf", "report_elucidsitereport"),
            "icon" => "file-pdf-o",
            "link" => (new moodle_url($url, array_merge(array("format" => "pdf"), $params)))->out(),
            "action" => 'pdf',
            "blockname" => $blockname,
            "contextid" => $context->id
        ),
        array(
            "name" => get_string("email", "report_elucidsitereport"),
            "icon" => "envelope-o",
            "link" => (new moodle_url($url, array_merge(array("format" => "email"), $params)))->out(),
            "action" => 'email',
            "blockname" => $blockname,
            "contextid" => $context->id
        )/*,
        array(
            "name" => get_string("copy", "report_elucidsitereport"),
            "icon" => "copy",
            "link" => new moodle_url($url, array_merge(array("format" => "copy"), $params)),
            "action" => 'copy'
        )*/
    );
}

/**
 * Get Users Filter for filer the data
 * @param [boolean] $customfields Custom Fields
 * @param [boolean] $cohortfilter Cohort Filters
 * @param [boolean] $rangeselector Range Selector
 * @return [array] Array of filters
 */
function get_userfilters($customfields, $cohortfilter, $rangeselector) {
    $userfilters = new stdClass();

    if ($cohortfilter) {
        $userfilters->cohortfilter = get_cohort_filter();
    }

    if ($rangeselector) {
        $userfilters->rangeselector = true;
    }

    return $userfilters;
}

/**
 * Get Cohort filter Filter for filer the data
 * @return [array] Array of Cohort filters
 */
function get_cohort_filter() {
    global $DB;

    $syscontext = context_system::instance();
    $cohorts = cohort_get_cohorts($syscontext->id)["cohorts"];
    $categories = $DB->get_records_select('course_categories', 'id');

    foreach($categories as $category) {
        $catcontext = context_coursecat::instance($category->id);
        $cohorts = array_merge($cohorts, cohort_get_cohorts($catcontext->id)["cohorts"]);
    }

    if (empty($cohorts)) {
        return false;
    }
    
    $cohortfilter = new stdClass();
    $cohortfilter->text = "Cohort";
    $cohortfilter->values = $cohorts;
    return $cohortfilter;
}

/**
 * Create back button for each individual page
 * @param [object] $backurl Moodle Url Object
 * @return [string] Html string for back button
 */
function create_back_button($backurl) {
    $html = html_writer::div(
        html_writer::link(
            $backurl,
            html_writer::tag(
                "i", "", array(
                    "class" => "icon fa fa-arrow-left",
                    "aria-hidden" => "true"
                )
            ),
            array(
                "class" => "btn btn-sm btn-default",
                "data-toggle" => "tooltip",
                "data-original-title" => get_string("back"),
                "data-placement" => "bottom"
            )
        ),
        "mb-10", array ("id" => "wdm_reportback_button")
    );
    return $html;
}

/**
 * If the moodle has plugin then return true
 * @param [string] $plugintype Plugin Type
 * @param [string] $pluginname Plugin Name
 * @return [boolean] True|False based on plugin exist
 */
function has_plugin($plugintype, $puginname) {
    $plugins = core_plugin_manager::instance()->get_plugins_of_type($plugintype);

    if (array_key_exists($puginname, $plugins)) {
        return true;
    }

    return false;
}