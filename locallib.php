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
        ),
        array(
            "name" => get_string("emailscheduled", "report_elucidsitereport"),
            "icon" => "envelope-o",
            "link" => (new moodle_url($url, array_merge(array("format" => "emailscheduled"), $params)))->out(),
            "action" => 'emailscheduled',
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

/**
 * Get Schedule Email form
 * @param [string] Url for submiting form
 * @return [string] HTML String of schedule form
 */
function get_schedule_emailform($formaction) {
    // Create count dropdown 
    $coutdropdown = html_writer::tag("button", "1", array(
        "type" => "button",
        "class" => "btn btn-default btn-sm dropdown-toggle",
        "id" => "durationcount",
        "data-toggle" => "dropdown",
        "aria-expanded" => "false"
    ));

    $coutdropdown .= html_writer::start_div("dropdown-menu", array(
        "aria-labelledby" => "durationcount",
        "role" => "menu"
    ));

    for ($i = 1; $i < 5; $i++) {
        $coutdropdown .= '<a class="dropdown-item" href="javascript:void(0)"
        data-value="'. $i .'" role="menuitem">'. $i .'</a>';
    }

    $coutdropdown .= html_writer::end_div();

    // Create weeks dropdown 
    $weeksdropdown = html_writer::tag("button", get_string("week_1", "report_elucidsitereport"), array(
        "type" => "button",
        "class" => "btn btn-default btn-sm dropdown-toggle",
        "id" => "weeksdropdown",
        "data-toggle" => "dropdown",
        "aria-expanded" => "false"
    ));

    $weeksdropdown .= html_writer::start_div("dropdown-menu", array(
        "aria-labelledby" => "weeksdropdown",
        "role" => "menu"
    ));

    // Get all 7 weeks
    for ($i = 1; $i <= 7; $i++) {
        $str = get_string("week_". $i, "report_elucidsitereport");
        $weeksdropdown .= '<a class="dropdown-item" href="javascript:void(0)"
        data-value="'. $i .'" role="menuitem">'. $str .'</a>';
    }

    $weeksdropdown .= html_writer::end_div();

    /*
     * Generate Modal for email schedule
     */
    // Form Start
    $out = html_writer::start_tag("form", array(
        "action" => $formaction
    ));

    // Start Main Div
    $out .= html_writer::start_div();

    // Start Header Div
    $out .= html_writer::start_div("d-flex");

    // Duration Count Dropdown Start
    $out .= html_writer::tag("i", "", array(
        "class" => "icon fa fa-calendar my-auto",
        "aria-hidden" => "true"
    ));
    // Header Duration Count
    $out .= html_writer::span(get_string("sendevery", "report_elucidsitereport"), "my-auto mr-10");
    // Duration Count Dropdown
    $out .= html_writer::span($coutdropdown, "dropdown");
    // Duration Count Dropdown End

    // Toggle Switch For Enable and Disable Start
    $out .= html_writer::start_div("my-auto px-5");
    $out .= html_writer::label(
        html_writer::tag("input", "", array(
            "id" => "test",
            "type" => "checkbox",
            "value" => "",
            "name" => "esr-switch-input",
            "checked" => "checked"
        )).
        html_writer::div(
            html_writer::div("", "switch-background bg-primary").
            html_writer::div("", "switch-lever bg-primary"),
            "switch-container esr-enable-disable-form"
        ), "test", true,
        array(
            "class" => "esr-switch",
            "title" => "Enable/Disable email"
        )
    );
    $out .= html_writer::end_div();
    // Toggle Switch For Enable and Disable End

    // Header Weeks Dropdown
    $out .= html_writer::span(get_string("weeks_on", "report_elucidsitereport"), "ml-auto my-auto px-10");

    // Weeks Dropdown
    $out .= html_writer::span($weeksdropdown, "dropdown");
    
    // End Header Div 
    $out .= html_writer::end_div();

    // Start Body Div
    $out .= html_writer::start_div("w-full py-10 mt-20");

    $out .= html_writer::start_div("py-10");
    $out .= html_writer::tag("i", "", array(
        "class" => "icon fa fa-user my-auto",
        "aria-hidden" => "true"
    ));
    $out .= html_writer::span("Custom Recepient");
    $out .= html_writer::end_div();

    $out .= html_writer::tag("textarea", "", array(
        "class" =>"w-full",
        "rows" => "7"
    ));
    // End Body Div
    $out .= html_writer::end_div();

    // End Main Div
    $out .= html_writer::end_div();

    $out .= html_writer::start_div("modal-footer px-0 py-5", array(
        "data-region" => "footer"
    ));
    $out .= html_writer::tag("button",
        get_string("schedule", "report_elucidsitereport"),
        array(
            "class" => "btn btn-primary",
            "type" => "button",
            "data-action" => "save"
        )
    );

    $out .= html_writer::tag("button",
        get_string("reset", "report_elucidsitereport"),
        array(
            "class" => "btn btn-secondary",
            "type" => "button",
            "data-action" => "cancel"
        )
    );
    $out .= html_writer::end_div();

    // End of Form
    $out .= html_writer::end_tag("form");

    return $out;
}

/**
 * Get Scheduled email list
 * @param [string] $blockname Block Name
 * @param [string] $region Region Name
 * @return [type] [description]
 */
function get_schedule_emaillist($blockname, $region) {
    $tableheader = new html_table();

    $tableheader->head = array(
        '<span class="checkbox-custom checkbox-primary">
          <input class="selectable-all" type="checkbox">
          <label></label>
        </span>',
        "Name",
        "Component",
        "Next run" ,
        "Manage"
    );

    // Size of table cell
    $size = array("10%", "40%", "20%", "20%", "10%");
    $tableheader->size = $size;
    $tableheader->attributes = array(
        "class" => "table table-hover",
        "data-role" => "content",
        "data-plugin" => "selectable",
        "data-row-selectable" => "true"
    );

    $out = html_writer::table($tableheader);

    $tabledata = new html_table();
    $tabledata->size = $size;
    for($i; $i<=100; $i++) {
        $tabledata->data[] = array(
            '<span class="checkbox-custom checkbox-primary">
              <input class="selectable-item" type="checkbox">
              <label></label>
            </span>',
            "Active Users Report",
            "Block",
            "12 Oct 2019",
            ""
        );
    }

    $out .= html_writer::div(
        html_writer::table($tabledata),
        "overflow-scroll",
        array(
            "style" => "max-height: 250px"
        )
    );
    return $out;
}