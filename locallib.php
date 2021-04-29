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
 * @package     local_edwiserreports
 * @category    admin
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/cohort/lib.php");
require_once($CFG->dirroot . "/local/edwiserreports/classes/constants.php");

/**
 * Get Export Link to export data from blocks and individual page
 * @param  string $url  Url prifix to get export link
 * @param  object $data Object for additional data
 * @return string       Moodle link detail
 */
function local_edwiserreports_get_block_exportlinks($url, $data) {
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

    $region = "block";
    $links->blockactiveusers = local_edwiserreports_get_exportlinks($url, $region, "activeusers", "weekly");
    $links->blockactivecourses = local_edwiserreports_get_exportlinks($url, $region, "activecourses");
    $links->blockcourseprogress = local_edwiserreports_get_exportlinks($url, $region, "courseprogress", $cpfilter);
    $links->blockcertificates = local_edwiserreports_get_exportlinks($url, $region, "certificates");
    $links->blockf2fsessions = local_edwiserreports_get_exportlinks($url, $region, "f2fsession");
    $links->blocklpstats = local_edwiserreports_get_exportlinks($url, $region, "lpstats", $lpfilter);
    $links->blockinactiveusers = local_edwiserreports_get_exportlinks(
        $url,
        $region,
        "inactiveusers",
        "never",
        false,
        false,
        "mt-20"
    );
    return $links;
}

/**
 * Get Export Link to export link array
 * @param  string $url         Url url for export link
 * @param  string $region      Region for export
 * @param  string $blockname   Block to export
 * @param  string $filter      Filter for data to export
 * @param  int    $cohortid    Cohort id
 * @param  string $action      Action of a page report
 * @param  string $customclass Custom class
 * @return array               Array of export link
 */
function local_edwiserreports_get_exportlinks(
        $url,
        $region,
        $blockname,
        $filter = false,
        $cohortid = false,
        $action = false,
        $customclass = ''
    ) {
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

    $out = new stdClass();
    $out->export = local_edwiserreports_get_exportlink_array($url, $blockname, $params, $region);
    $out->customclass = $customclass;
    return $out;
}

/**
 * Get Export Link to export link array
 * @param  string $url       Url for export link
 * @param  string $blockname Block to export
 * @param  array  $params    Prameters for link
 * @param  string $region    Block region
 * @return array             Array of export link
 */
function local_edwiserreports_get_exportlink_array($url, $blockname, $params, $region) {
    $context = context_system::instance();

    return array(
        array(
            "name" => get_string("csv", "local_edwiserreports"),
            "icon" => "file-o",
            "link" => (new moodle_url($url, array_merge(array("format" => "csv"), $params)))->out(),
            "action" => 'csv',
            "blockname" => $blockname,
            "region" => $region,
            "contextid" => $context->id
        ),
        array(
            "name" => get_string("excel", "local_edwiserreports"),
            "icon" => "file-excel-o",
            "link" => (new moodle_url($url, array_merge(array("format" => "excel"), $params)))->out(),
            "action" => 'excel',
            "blockname" => $blockname,
            "region" => $region,
            "contextid" => $context->id
        ),
        array(
            "name" => get_string("pdf", "local_edwiserreports"),
            "icon" => "file-pdf-o",
            "link" => (new moodle_url($url, array_merge(array("format" => "pdf"), $params)))->out(),
            "action" => 'pdf',
            "blockname" => $blockname,
            "region" => $region,
            "contextid" => $context->id
        ),
        array(
            "name" => get_string("email", "local_edwiserreports"),
            "icon" => "envelope-o",
            "link" => (new moodle_url($url, array_merge(array("format" => "email"), $params)))->out(),
            "action" => 'email',
            "blockname" => $blockname,
            "region" => $region,
            "contextid" => $context->id
        ),
        array(
            "name" => get_string("emailscheduled", "local_edwiserreports"),
            "icon" => "envelope-o",
            "link" => (new moodle_url($url, array_merge(array("format" => "emailscheduled"), $params)))->out(),
            "action" => 'emailscheduled',
            "blockname" => $blockname,
            "region" => $region,
            "contextid" => $context->id,
            "sesskey" => sesskey()
        )
    );
}

/**
 * Get Users Filter for filer the data
 * @param  bool  $customfields  Custom Fields
 * @param  bool  $cohortfilter  Cohort Filters
 * @param  bool  $rangeselector Range Selector
 * @return array                Array of filters
 */
function local_edwiserreports_get_userfilters($customfields, $cohortfilter, $rangeselector) {
    $userfilters = new stdClass();

    if ($cohortfilter) {
        $userfilters->cohortfilter = local_edwiserreports_get_cohort_filter();
    }

    if ($rangeselector) {
        $userfilters->rangeselector = true;
    }

    return $userfilters;
}

/**
 * Get Cohort filter Filter for filer the data
 * @return array Array of Cohort filters
 */
function local_edwiserreports_get_cohort_filter() {
    global $DB, $USER;

    // Fetch all cohorts
    // passing 0,0 -> page_number, number of record, 0 means.
    $allcohorts = cohort_get_all_cohorts(0, 0);

    $usercontext = context_user::instance($USER->id);

    $cohorts = [];

    // Users visibility check.
    foreach ($allcohorts['cohorts'] as $key => $value) {
        if (cohort_can_view_cohort($key, $usercontext)) {
            $cohorts[] = $value;
        }
    }

    if (empty($cohorts)) {
        // Returning false if no cohorts are present.
        return false;
    }

    $cohortfilter = new stdClass();
    $cohortfilter->text = get_string('cohorts', 'local_edwiserreports');
    $cohortfilter->values = $cohorts;

    return $cohortfilter;
}


/**
 * Create individual pageheader
 * @param  string  $blockname  Block name
 * @param  string  $coursename Course name
 * @return string              HTML header string
 */
function local_edwiserreports_create_page_header($blockname, $coursename = false) {
    global $CFG;

    // Create backurl.
    $backurl = $CFG->wwwroot . "/local/edwiserreports/";
    $component = "local_edwiserreports";

    // Start page header.
    $out = html_writer::start_div("d-md-flex mb-10", array("id" => "esr-page-header"));

    // Back button link.
    $out .= html_writer::start_div("");
    $out .= html_writer::link($backurl,
        '<i class="icon fa fa-arrow-left"></i>',
        array(
            "class" => "btn btn-sm btn-default",
            "data-toggle" => "tooltip",
            "data-original-title" => get_string("back"),
            "data-placement" => "bottom"
        )
    );
    $out .= html_writer::end_div();

    // If coursename then send as param in getstring.
    $params = array();
    if ($coursename) {
        $params["coursename"] = $coursename;
    }

    // Create header.
    $out .= html_writer::span(get_string($blockname . "header", $component, $params), "px-md-10");

    // End pageheader.
    $out .= html_writer::end_div();

    // Return output.
    return $out;
}

/**
 * Create back button for each individual page
 * @param  object $backurl Moodle Url Object
 * @return string          Html string for back button
 */
function local_edwiserreports_create_back_button($backurl) {
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
 * @param  string  $plugintype Plugin Type
 * @param  string  $pluginname Plugin Name
 * @return boolean             True|False based on plugin exist
 */
function local_edwiserreports_has_plugin($plugintype, $pluginname) {
    $plugins = core_plugin_manager::instance()->get_plugins_of_type($plugintype);

    return array_key_exists($pluginname, $plugins);
}

/**
 * Get Schedule Email form
 *
 * @param  int    $id         Unique scheduled email id
 * @param  string $formaction Form action post/get
 * @param  string $blockname  Block name
 * @param  string $region     Block region
 * @return string             HTML schedule email modal content
 */
function local_edwiserreports_get_schedule_emailform($id, $formaction, $blockname, $region) {
    global $DB;

    // Default values for form.
    $esrid = null;
    $esrname = '';
    $esremailenable = true;
    $esrrecepient = '';
    $esrsubject = '';
    $esrmessage = '';
    $esrduration = 0;
    $esrtime = 0;
    $esrlastrun = '';
    $esrnextrun = '';

    // Get data from table.
    $table = "edwreports_schedemails";
    $blockcompare = $DB->sql_compare_text('blockname');
    $componentcompare = $DB->sql_compare_text('component');
    $sql = "SELECT * FROM {edwreports_schedemails}
            WHERE $blockcompare LIKE :blockname
            AND $componentcompare LIKE :component";
    $params = array(
        "blockname" => $blockname,
        "component" => $region
    );

    // If data exist then replace the data.
    $rec = $DB->get_record_sql($sql, $params);
    if ($rec && $emaildata = json_decode($rec->emaildata)) {
        if (is_array($emaildata) && isset($emaildata[$id])) {
            $esrid = $id;
            $esrname = $emaildata[$id]->esrname;
            $esremailenable = $emaildata[$id]->esremailenable;
            $esrrecepient = $emaildata[$id]->esrrecepient;
            $esrsubject = $emaildata[$id]->esrsubject;
            $esrmessage = $emaildata[$id]->esrmessage;
            $esrduration = $emaildata[$id]->esrduration;
            $esrtime = $emaildata[$id]->esrtime;
            $esrlastrun = $emaildata[$id]->esrlastrun;
            $esrnextrun = $emaildata[$id]->esrnextrun;
            $reportparams = $emaildata[$id]->reportparams;
        }
    }

    // Generate Modal for email schedule
    // Form Start.
    $out = html_writer::start_tag("form", array(
        "action" => $formaction
    ));

    $out .= local_edwiserreports_get_email_schedule_header($esremailenable, $esrduration, $esrtime);

    // Start Body Div.
    $out .= html_writer::start_div("w-full my-10");

    // Name of scheduled email.
    $out .= html_writer::start_div("mb-5");
    $out .= html_writer::tag("i", "", array(
        "class" => "icon fa fa-calendar-o my-auto",
        "aria-hidden" => "true"
    ));
    $out .= html_writer::span("Name");
    $out .= html_writer::end_div();
    $out .= html_writer::start_tag("input", array(
        "type" => "text",
        "value" => $esrname,
        "name" => "esr-name",
        "class" => "w-full mb-10"
    ));

    // Recepient Input Text.
    $out .= html_writer::start_div("mb-5");
    $out .= html_writer::tag("i", "", array(
        "class" => "icon fa fa-user my-auto",
        "aria-hidden" => "true"
    ));
    $out .= html_writer::span("Recepient");
    $out .= html_writer::end_div();
    $out .= html_writer::start_tag("input", array(
        "type" => "text",
        "value" => $esrrecepient,
        "name" => "esr-recepient",
        "class" => "w-full mb-10"
    ));

    // Subject Input Text.
    $out .= html_writer::start_div("mb-5");
    $out .= html_writer::span("Subject");
    $out .= html_writer::end_div();
    $out .= html_writer::start_tag("input", array(
        "type" => "text",
        "value" => $esrsubject,
        "name" => "esr-subject",
        "class" => "w-full mb-10"
    ));

    // Message box for emails.
    $out .= html_writer::start_div("mb-5");
    $out .= html_writer::span("Message");
    $out .= html_writer::end_div();
    $out .= html_writer::tag("textarea", $esrmessage, array(
        "value" => $esrmessage,
        "name" => "esr-message",
        "class" => "form-control w-full mb-10",
        "rows" => "4"
    ));

    // Error message box.
    $out .= html_writer::div("", "esr-form-error");

    // Hidden inputs.
    $out .= html_writer::tag("input", "", array(
        "value" => $esrduration,
        "type" => "text",
        "id" => "esr-sendduration",
        "name" => "esr-duration",
        "class" => "d-none"
    ));

    $out .= html_writer::tag("input", "", array(
        "value" => $esrtime,
        "type" => "text",
        "id" => "esr-sendtime",
        "name" => "esr-time",
        "class" => "d-none"
    ));

    $out .= html_writer::tag("input", "", array(
        "value" => $esrid,
        "type" => "text",
        "id" => "esr-id",
        "name" => "esr-id",
        "class" => "d-none"
    ));

    // End Body Div.
    $out .= html_writer::end_div();

    // End Main Div.
    $out .= html_writer::end_div();

    $out .= html_writer::start_div("modal-footer px-0 py-5", array(
        "data-region" => "footer"
    ));
    $out .= html_writer::tag("button",
        get_string("schedule", "local_edwiserreports"),
        array(
            "class" => "btn btn-primary",
            "type" => "button",
            "data-action" => "save"
        )
    );

    $out .= html_writer::tag("button",
        get_string("reset", "local_edwiserreports"),
        array(
            "class" => "btn btn-secondary",
            "type" => "button",
            "data-action" => "cancel"
        )
    );
    $out .= html_writer::end_div();

    // End of Form.
    $out .= html_writer::end_tag("form");

    return $out;
}

/**
 * Get email schedule header
 * @param  bool   $emailenable Enable email
 * @param  int    $duration    Email duration
 * @param  int    $time        Time for email
 * @return string              HTML content
 */
function local_edwiserreports_get_email_schedule_header($emailenable, $duration, $time) {
    // Select which sropdown has to be select.
    $daily = $weekly = $monthly = false;
    $dayofweek = $timeofday = $dayofmonth = 0;

    // Set the time value for weeks day.
    switch($duration) {
        case LOCAL_SITEREPORT_ESR_DAILY_EMAIL:
            if ($time <= 3) {
                $timeofday = $time;
            }
            $daily = true;
            break;
        case LOCAL_SITEREPORT_ESR_LOCAL_SITEREPORT_WEEKLY_EMAIL:
            if ($time <= 6) {
                $dayofweek = $time;
            }
            $weekly = true;
            break;
        case LOCAL_SITEREPORT_ESR_LOCAL_SITEREPORT_MONTHLY_EMAIL:
            if ($time <= 3) {
                $dayofmonth = $time;
            }
            $monthly = true;
            break;
        default:
            $daily = true;
            $duration = 0; // Set Default value.
    }

    // Start Main Div.
    $out = html_writer::start_div();

    // Start Header Div.
    $out .= html_writer::start_div("d-flex");

    // Duration Count Dropdown Start.
    $out .= html_writer::tag("i", "", array(
        "class" => "icon fa fa-calendar my-auto",
        "aria-hidden" => "true"
    ));
    // Header Duration Count.
    $out .= html_writer::span(get_string("emailthisreport", "local_edwiserreports"), "my-auto mx-5");

    // Email Shcedule Duration.
    $out .= html_writer::span(local_edwiserreports_get_duration_dropdown($duration), "duration-dropdown dropdown");

    $out .= html_writer::span(get_string("onevery", "local_edwiserreports"), "my-auto mx-5");

    // Weeks Dropdown.
    $out .= html_writer::span(local_edwiserreports_get_weeks_dropdown($dayofweek, $weekly), "weekly-dropdown dropdown");
    // Times Dropdown.
    $out .= html_writer::span(local_edwiserreports_get_times_dropdown($timeofday, $daily), "daily-dropdown dropdown");
    // Monthly Dropdown.
    $out .= html_writer::span(local_edwiserreports_get_monthly_dropdown($dayofmonth, $monthly), "monthly-dropdown dropdown");

    $out .= local_edwiserreports_create_toggle_switch_for_emails("", $emailenable, "", "", "ml-auto");
    // End Header Div.
    $out .= html_writer::end_div();

    return $out;
}

/**
 * Get Times dropdown
 * @param  int     $time   Time in integer
 * @param  bool    $active True to show dropdown
 * @return string          HTML dropdown content
 */
function local_edwiserreports_get_times_dropdown($time = 0, $active = false) {
    $dnone = '';
    if (!$active) {
        $dnone = "display: none;";
    }

    $timesdropdown = html_writer::tag("button", get_string("times_" . $time, "local_edwiserreports"), array(
        "data-value" => $time,
        "type" => "button",
        "class" => "btn btn-default btn-sm dropdown-toggle mx-5",
        "id" => "timesdropdown",
        "data-toggle" => "dropdown",
        "data-managedby" => LOCAL_SITEREPORT_ESR_DAILY_EMAIL,
        "aria-expanded" => "false",
        "style" => $dnone
    ));

    $timesdropdown .= html_writer::start_div("dropdown-menu", array(
        "aria-labelledby" => "timesdropdown",
        "role" => "menu"
    ));

    // Get all 7 weeks.
    for ($i = 0; $i <= 3; $i++) {
        $str = get_string("times_". $i, "local_edwiserreports");
        $timesdropdown .= '<a class="dropdown-item" href="javascript:void(0)"
        data-value="'. $i .'" role="menuitem">'. $str .'</a>';
    }

    $timesdropdown .= html_writer::end_div();

    return $timesdropdown;
}

/**
 * Get weeks dropdown
 * @param  int     $time   Time in integer
 * @param  bool    $active True to show dropdown
 * @return string          HTML dropdown content
 */
function local_edwiserreports_get_weeks_dropdown($time = 0, $active = false) {
    $dnone = '';
    if (!$active) {
        $dnone = "display: none;";
    }

    $weeksdropdown = html_writer::tag("button", get_string("week_" . $time, "local_edwiserreports"), array(
        "data-value" => $time,
        "type" => "button",
        "class" => "btn btn-default btn-sm dropdown-toggle mx-5",
        "id" => "weeksdropdown",
        "data-toggle" => "dropdown",
        "data-managedby" => LOCAL_SITEREPORT_ESR_LOCAL_SITEREPORT_WEEKLY_EMAIL,
        "aria-expanded" => "false",
        "style" => $dnone
    ));

    $weeksdropdown .= html_writer::start_div("dropdown-menu", array(
        "aria-labelledby" => "weeksdropdown",
        "role" => "menu"
    ));

    // Get all 7 weeks.
    for ($i = 0; $i <= 6; $i++) {
        $str = get_string("week_". $i, "local_edwiserreports");
        $weeksdropdown .= '<a class="dropdown-item" href="javascript:void(0)"
        data-value="'. $i .'" role="menuitem">'. $str .'</a>';
    }

    $weeksdropdown .= html_writer::end_div();

    return $weeksdropdown;
}

/**
 * Get quaterly dropdown
 * @param  int     $time   Which quater is selected
 * @param  bool    $active True to show dropdown
 * @return string          HTML string for quaterly dropdown
 */
function local_edwiserreports_get_monthly_dropdown($time = 0, $active = false) {
    $dnone = '';
    if (!$active) {
        $dnone = "display: none;";
    }

    $monthlydropdown = html_writer::tag("button", get_string("monthly_" . $time, "local_edwiserreports"), array(
        "data-value" => $time,
        "type" => "button",
        "class" => "btn btn-default btn-sm dropdown-toggle mx-5",
        "id" => "monthlydropdown",
        "data-toggle" => "dropdown",
        "data-managedby" => LOCAL_SITEREPORT_ESR_LOCAL_SITEREPORT_MONTHLY_EMAIL,
        "aria-expanded" => "false",
        "style" => $dnone
    ));

    $monthlydropdown .= html_writer::start_div("dropdown-menu", array(
        "aria-labelledby" => "monthlydropdown",
        "role" => "menu"
    ));

    // Get all 7 weeks.
    for ($i = 0; $i <= 2; $i++) {
        $str = get_string("monthly_". $i, "local_edwiserreports");
        $monthlydropdown .= '<a class="dropdown-item" href="javascript:void(0)"
        data-value="'. $i .'" role="menuitem">'. $str .'</a>';
    }

    $monthlydropdown .= html_writer::end_div();

    return $monthlydropdown;
}

/**
 * Get duration dropdown
 * @param  int    $duration Duration in integer
 * @return string           HTML dropdown button
 */
function local_edwiserreports_get_duration_dropdown($duration = 0) {
    // Create count dropdown.
    $durationdropdown = html_writer::tag("button", get_string("duration_" .$duration, "local_edwiserreports"), array(
        "data-value" => $duration,
        "type" => "button",
        "class" => "btn btn-default btn-sm dropdown-toggle",
        "id" => "durationcount",
        "data-toggle" => "dropdown",
        "aria-expanded" => "false"
    ));

    $durationdropdown .= html_writer::start_div("dropdown-menu", array(
        "aria-labelledby" => "durationcount",
        "role" => "menu"
    ));

    for ($i = 0; $i <= 2; $i++) {
        $durationdropdown .= '<a class="dropdown-item" href="javascript:void(0)"
        data-value="'. $i .'" role="menuitem">'. get_string("duration_" .$i, "local_edwiserreports") .'</a>';
    }

    $durationdropdown .= html_writer::end_div();

    return $durationdropdown;
}

/**
 * Get Scheduled email list
 * @return array Email list
 */
function local_edwiserreports_get_schedule_emaillist() {
    global $DB;

    $emails = array();
    $rec = $DB->get_records('edwreports_schedemails');
    foreach ($rec as $key => $val) {
        // If it dosent have email data.
        if (!$emaildata = json_decode($val->emaildata)) {
            continue;
        }

        // If dta is not an array.
        if (!is_array($emaildata)) {
            continue;
        }

        // If everythings is ok then.
        foreach ($emaildata as $key => $emailinfo) {
            $data = array();
            $data["esrname"] = $emailinfo->esrname;
            $data["esrnextrun"] = date("d M y", $emailinfo->esrnextrun);
            $data["esrfrequency"] = $emailinfo->esrfrequency;
            $data["esrcomponent"] = $val->blockname;
            $data["esrmanage"] = local_edwiserreports_create_toggle_switch_for_emails(
                $key,
                $emailinfo->esremailenable,
                $val->blockname,
                $val->component
            ) . local_edwiserreports_create_manage_icons_for_emaillist(
                $key,
                $val->blockname,
                $val->component,
                $emailinfo->esremailenable
            );
            $emails = $data;
        }
    }
    return $emails;
}

/**
 * Create select icon for email list
 * @param  bool   $select True (If selected)
 * @return string         Html string to render select
 */
function local_edwiserreports_carete_select_icons_for_emaillist($select) {
    $selectparam = array(
        "class" => "checkbox-custom checkbox-primary",
        "type" => "checkbox"
    );

    if ($select) {
        $selectparam["checked"] = "checked";
    }

    $out = html_writer::start_span("checkbox-custom checkbox-primary");
    $out .= html_writer::start_tag("input", $selectparam);
    $out .= html_writer::tag("label", "");
    $out .= html_writer::end_span();
    return $out;
}

/**
 * Create mange icons to manage email list
 *
 * @param  int     $id        Unique email id
 * @param  string  $blockname Block name
 * @param  string  $region    Region of email
 * @param  bool    $enable    True to enable email
 * @return string             Html manage icon strin
 */
function local_edwiserreports_create_manage_icons_for_emaillist($id, $blockname, $region, $enable) {
    $manage = html_writer::start_span("row esr-manage-scheduled-emails m-0 p-0 justify-content-center");
    $manage .= local_edwiserreports_create_toggle_switch_for_emails(
        $id,
        $enable,
        $blockname,
        $region
    );
    $manage .= html_writer::link('javascript:void(0)',
        '<i class="fa fa-edit mx-1"></i>',
        array(
            "class" => "esr-email-sched-setting",
            "data-blockname" => $blockname,
            "data-region" => $region,
            "data-id" => $id,
            "data-sesskey" => sesskey(),
            "data-toggle" => "tooltip",
            "title" => get_string('edit', 'local_edwiserreports')
        )
    );
    $manage .= html_writer::link('javascript:void(0)',
        '<i class="fa fa-trash mx-1 text-danger"></i>',
        array(
            "class" => "esr-email-sched-delete",
            "data-blockname" => $blockname,
            "data-region" => $region,
            "data-id" => $id,
            "data-sesskey" => sesskey(),
            "data-toggle" => "tooltip",
            "title" => get_string('delete', 'local_edwiserreports')
        )
    );

    return $manage;
}

/**
 * Create toggle switch to enable disable emails
 *
 * @param  int     $id          Unique id
 * @param  bool    $emailenable True to enable email
 * @param  string  $blockname   Block to schedule email
 * @param  string  $region      Region for email
 * @param  string  $customclass Custom class to show for toggle
 * @return string               Html string for toggle switch
 */
function local_edwiserreports_create_toggle_switch_for_emails($id, $emailenable, $blockname, $region, $customclass = '') {
    $toggleid = "esr-toggle-" . $blockname . "-" . $region . "-" . $id;
    $switchparams = array(
        "id" => $toggleid,
        "type" => "checkbox",
        "value" => true,
        "name" => "esr-emailenable",
        "data-sesskey" => sesskey(),
        "data-blockname" => $blockname,
        "data-region" => $region,
        "data-id" => $id,
    );

    if ($emailenable) {
        $switchparams["checked"] = "checked";
    }

    // Toggle Switch For Enable and Disable Start.
    $out = html_writer::start_div("my-auto w-auto d-inline-block ". $customclass);
    $out .= html_writer::label(
        html_writer::tag("input", "", $switchparams).
        html_writer::div(
            html_writer::div("", "switch-background bg-primary").
            html_writer::div("", "switch-lever bg-primary"),
            "switch-container esr-enable-disable-form"
        ), $toggleid, true,
        array(
            "class" => "esr-switch",
            "title" => "Enable/Disable email",
            "data-toggle" => "tootip"
        )
    );
    $out .= html_writer::end_div();
    // Toggle Switch For Enable and Disable End.
    return $out;
}

/**
 * Get email schedule duration time
 * @param  int $duration Duration
 * @param  int $time     Time
 * @return int           Run time
 */
function local_edwiserreports_get_email_schedule_next_run($duration, $time) {
    $timenow = time();
    $frequency = '';
    // According to duation and time calculate the next scheduled time.
    switch($duration) {
        case LOCAL_SITEREPORT_ESR_LOCAL_SITEREPORT_WEEKLY_EMAIL:
            $day = get_string("week_" . $time, "local_edwiserreports");
            $weekstr = 'next ' . $day;

            // Calculate time.
            $schedtime = strtotime($weekstr);
            $frequency = get_string("everyweeks", "local_edwiserreports", array("day" => $day));
            break;
        case LOCAL_SITEREPORT_ESR_LOCAL_SITEREPORT_MONTHLY_EMAIL:
            // Get last date of the month.
            $lastdate = date("d", strtotime('last day of this month'));
            $day = $time;

            // If it is greater then then assign as time.
            if ($time > (int) $lastdate) {
                $time = (int) $lastdate;
            }

            // Get month string.
            $monthstr = date($time . ' M Y', $timenow);

            // Calculate time.
            $schedtime = strtotime($monthstr);
            $frequency = get_string("everymonths", "local_edwiserreports", array("time" => $day));

            // If time has passed the add one month.
            if ($timenow > $schedtime) {
                $schedtime = $schedtime + LOCAL_SITEREPORT_ONEMONTH;
            }
            break;

        default: // Default daily emails.
            $dailystr = date("d M Y", $timenow);

            // Calculate time.
            $schedtime = strtotime($dailystr) + $time * 60 * 60;

            if ($time < 10) {
                $day = get_string("time0".$time, "local_edwiserreports");
            } else {
                $day = get_string("time".$time, "local_edwiserreports");
            }

            // Get frequency string.
            $frequency = get_string("everydays", "local_edwiserreports", array("time" => $day));

            // If scheduledtime has been passed then add one day.
            if ($timenow > $schedtime) {
                $schedtime = $schedtime + LOCAL_SITEREPORT_ONEDAY;
            }
    }

    // Return scheduled time.
    return array($frequency, $schedtime);
}

/**
 * Prepare export filename
 * @param  array  $params Params to prepare filename
 * @return string Filename
 */
function local_edwiserreports_prepare_export_filename($params) {
    return "report_" . implode("_", $params);
}

/**
 * Get required strings for js
 */
function local_edwiserreports_get_required_strings_for_js() {
    global $PAGE;

    $stringman = get_string_manager();
    $strings = $stringman->load_component_strings('local_edwiserreports', 'en');
    $PAGE->requires->strings_for_js(array_keys($strings), 'local_edwiserreports');

    // Require string from role component.
    $str = array(
        'inherit',
        'allow',
        'prevent',
        'prohibit'
    );
    $PAGE->requires->strings_for_js($str, 'role');
}

/**
 * Reset Site Report Page to default
 */
function reset_edwiserreports_page_default() {
    global $CFG, $DB, $USER;
    $blocks = \local_edwiserreports\utility::get_reports_block();

    foreach ($blocks as $block) {
        $prefname = 'pref_' . $block->classname;
        $DB->delete_records('user_preferences', array('userid' => $USER->id, 'name' => $prefname));
        unset($USER->preference[$prefname]);
    }

    $customreports = $DB->get_records('edwreports_custom_reports');
    foreach ($customreports as $block) {
        $prefname = 'pref_customreportsblock-' . $block->id;
        $DB->delete_records('user_preferences', array('userid' => $USER->id, 'name' => $prefname));
        unset($USER->preference[$prefname]);
    }

    redirect($CFG->wwwroot . '/local/edwiserreports/index.php');
}

/**
 * Check if any of the blocks are present in reports dashboard.
 */
function is_block_present_indashboard() {
    // Check if any of the block is present.
    $hasblock = false;
    $blocks = \local_edwiserreports\utility::get_reports_block();
    foreach ($blocks as $key => $block) {
        if ($block->classname == 'customreportsblock') {
            if (can_view_block('customreportsroleallow-' . $block->id)) {
                $hasblock = true;
            }
        } else {
            $capname = 'report/edwiserreports_' . $block->classname . ':view';
            if (has_capability($capname, context_system::instance()) ||
                can_view_block($capname)) {
                $hasblock = true;
                continue;
            }
        }
    }

    return $hasblock;
}

/**
 * Check if user has course level role in the system
 * @param  [int]     $userid        Users Id
 * @param  [string]  $roleshortname Role Short Name
 * @return [boolean]                Status
 */
function has_user_role($userid, $roleshortname) {
    global $DB;

    $roleid = $DB->get_field('role', 'id', array('shortname' => $roleshortname));
    return $DB->record_exists('role_assignments', ['userid' => $userid, 'roleid' => $roleid]);
}

/**
 * Function to get the users role in any courses
 * @param String $capname Capability name
 */
function can_view_block($capname) {
    global $DB, $USER;

    $canviewblocks = false;
    if (strpos($capname, 'customreportsroleallow') !== false) {
        $configstr = get_config('local_edwiserreports', $capname);
        if ($configstr) {
            $roleids = explode(',', $configstr);
            list($insql, $inparams) = $DB->get_in_or_equal($roleids, SQL_PARAMS_NAMED);
            $sql = 'SELECT * FROM {role} WHERE id ' . $insql;
            $allowedrole = $DB->get_records_sql($sql, $inparams);
        } else {
            $allowedarchetype = array('mamnager', 'coursecreator');
            list($insql, $inparams) = $DB->get_in_or_equal($allowedarchetype, SQL_PARAMS_NAMED);
            $sql = 'SELECT * FROM {role} WHERE archetype ' . $insql;
            $allowedrole = $DB->get_records_sql($sql, $inparams);
        }
    } else {
        $allowedrole = get_roles_with_capability($capname);
    }

    foreach ($allowedrole as $role) {
        if (has_user_role($USER->id, $role->shortname)) {
            $canviewblocks = true;
            continue;
        }
    }

    return $canviewblocks;
}
