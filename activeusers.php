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

namespace report_elucidsitereport;

use context_system;
use moodle_url;

require_once(__DIR__ . '/../../config.php');
require_once('classes/output/renderable.php');

// Required login
require_login();

// System Context
$context = context_system::instance();
$component = 'report_elucidsitereport';

// The requested section isn't in the admin tree
// It could be because the user has inadequate capapbilities or because the section doesn't exist
if (!has_capability('report/report_elucidsitereport:view', $context)) {
    // The requested section could depend on a different capability
    // but most likely the user has inadequate capabilities
    print_error('accessdenied', 'admin');
}


// Require JS for active users page
$PAGE->requires->js_call_amd(
    'report_elucidsitereport/activeusers',
    'init',
    array($context->id)
);

// Set css for active usres page
$PAGE->requires->css('/report/elucidsitereport/styles/flatpickr.min.css');

// Add js string for this page
$PAGE->requires->strings_for_js([
    'activeusersmodaltitle',
    'enrolmentsmodaltitle',
    'completionsmodaltitle'
], $component);

// Page URL for active users page
$pageurl = new moodle_url($CFG->wwwroot . "/report/elucidsitereport/activeusers.php");

// Set page context
$PAGE->set_context($context);

// Set page URL
$PAGE->set_url($pageurl);

// Get active users renderable
$renderable = new \report_elucidsitereport\output\activeusers_renderable();
$output = $PAGE->get_renderer($component)->render($renderable);

// Print output in the page
echo $OUTPUT->header();
echo $OUTPUT->heading(create_page_header("activeusers"), "1", "page-title p-5");
echo $output;
echo $OUTPUT->footer();
