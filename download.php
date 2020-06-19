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

use report_elucidsitereport\export;

require_once(__DIR__ .'/../../config.php');
require_once($CFG->dirroot."/report/elucidsitereport/classes/export.php");
require_once($CFG->dirroot."/report/elucidsitereport/classes/utility.php");

// Check if users is logged in
require_login();

// Get system context
$context = context_system::instance();

// The requested section isn't in the admin tree
// It could be because the user has inadequate capapbilities or because the section doesn't exist
if (!has_capability('report/report_elucidsitereport:view', $context)) {
    // The requested section could depend on a different capability
    // but most likely the user has inadequate capabilities
    print_error('accessdenied', 'admin');
}

// Set page context
$PAGE->set_context($context);

// If format is there then go ahead to export
if ($format = optional_param("format", false, PARAM_TEXT)) {
    // Get required and option paramerter for export
    $region = required_param("region", PARAM_TEXT);
    $blockname = required_param("blockname", PARAM_TEXT);
    $filter = optional_param("filter", false, PARAM_TEXT);

    // Generate file name
    $filename = $region ."_" . $blockname . "_" . date("d_M_y", time());

    // If filter is there then add filter in the file name
    if ($filter) {
        $filename .= "_" . $filter;
    }

    // Get export object 
    $export = new export($format, $region, $blockname);

    // If format is scheduled email then dont prepare data
    if ($format == "emailscheduled") {
        $export->data_export($filename, false);
    } else {
        // Prepare exportable data
        $data = $export->get_exportable_data($filter);

        // If data is there then download data with files
        if ($data) {
            $export->data_export($filename, $data);
        }
    }
} else if ($reporttype = required_param('reporttype', PARAM_TEXT)) {
    // Get Url parameter
    $filters = required_param('filters', PARAM_TEXT);
    $enrolstartdate = optional_param('enrolstartdate', null, PARAM_TEXT);
    $enrolenddate = optional_param('enrolenddate', null, PARAM_TEXT);
    // Get export object
    $export = new export(null, null, null);
    // check report type is query report
    if ($reporttype == 'lpdetailed') {
        // Render csv data in csv file
        $export->export_lpdetailed_report_data($reporttype, $filters, $enrolstartdate, $enrolenddate);
    } else if ($reporttype == 'queryReport') {
        $data = array (
            'fields' => optional_param('checkedFields', null, PARAM_TEXT),
            'reportingmanagers' => optional_param('reportingmanagers', null, PARAM_TEXT),
            'lps' => optional_param('lps', null, PARAM_TEXT),
            'courses' => optional_param('courses', null, PARAM_TEXT),
            'enrolstartdate' => optional_param('enrolstartdate', null, PARAM_TEXT),
            'enrolenddate' => optional_param('enrolenddate', null, PARAM_TEXT),
            'completionstartdate' => optional_param('completionstartdate', null, PARAM_TEXT),
            'completionenddate' => optional_param('completionenddate', null, PARAM_TEXT),
            'cohortids' => optional_param('cohortids', '', PARAM_TEXT),
            'userids' => optional_param('userids', '', PARAM_TEXT),
            'reportlevel' => optional_param('reportlevel', 'courses', PARAM_TEXT),
            'activitytype' => optional_param('activitytype', '', PARAM_TEXT)
        );

        // Render csv data in csv file
        $export->export_csv_customquery_report_data((object) $data);
    } else {
        // Render csv data in csv file
        $export->export_csv_customreport_data($reporttype, $filters, $enrolstartdate, $enrolenddate);
    }
}
