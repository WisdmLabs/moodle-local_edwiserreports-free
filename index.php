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

require_once(__DIR__ .'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('classes/output/renderable.php');
require_once('classes/export.php');

// Strings for js.
$PAGE->requires->strings_for_js([
    'cpblocktooltip1',
    'cpblocktooltip2',
    'lpstatstooltip',
    'per100-80',
    'per80-60',
    'per60-40',
    'per40-20',
    'per20-0',
    'per100'
], 'report_elucidsitereport');

// Set external page admin.
$context = context_system::instance();
$component = "report_elucidsitereport";

require_login();

// Allow users preferences set remotly
\report_elucidsitereport\utility::allow_update_userpreferences_remotly();

// The requested section isn't in the admin tree
// It could be because the user has inadequate capapbilities or because the section doesn't exist.
if (!has_capability('report/report_elucidsitereport:view', $context)) {
    // The requested section could depend on a different capability
    // But most likely the user has inadequate capabilities.
    print_error('accessdenied', 'admin');
}

// Page URL.
$pageurl = new moodle_url($CFG->wwwroot."/report/elucidsitereport/index.php");

// Require JS for index page.
$PAGE->requires->js_call_amd('report_elucidsitereport/main', 'init');

// Require CSS for index page.
$PAGE->requires->css('/report/elucidsitereport/styles/loader.css');

// Set page context.
$PAGE->set_context($context);

// Set page URL.
$PAGE->set_url($pageurl);

// Get renderable.
$renderable = new \report_elucidsitereport\output\elucidreport_renderable();
$output = $PAGE->get_renderer($component)->render($renderable);

// Set page heading.
$PAGE->set_heading(get_string("pluginname", "report_elucidsitereport"));

// Print output in page.
echo $OUTPUT->header();
echo $output;
echo $OUTPUT->footer();
