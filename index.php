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

require_once(__DIR__ .'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('classes/output/renderable.php');
require_once('classes/export.php');

global $OUTPUT;

// Strings for js.
local_edwiserreports_get_required_strings_for_js();

// Set external page admin.
$context = context_system::instance();
$component = "local_edwiserreports";

require_login();

// If use want to edit page.
$edit = optional_param('edit', false, PARAM_BOOL);
if ($edit !== false) {
    $USER->editing = $edit;
    redirect($CFG->wwwroot . '/local/edwiserreports/index.php');
}

// If use want to edit page.
$reset = optional_param('reset', false, PARAM_BOOL);
if ($reset !== false) {
    $USER->editing = false;
    reset_edwiserreports_page_default();
}

// Allow users preferences set remotly.
\local_edwiserreports\utility::allow_update_userpreferences_remotly();

// Page URL.
$pageurl = new moodle_url($CFG->wwwroot."/local/edwiserreports/index.php");

// Require JS for index page.
$PAGE->requires->js_call_amd('local_edwiserreports/main', 'init');

// Require CSS for index page.
$PAGE->requires->css('/local/edwiserreports/styles/edwiserreports.min.css');

// Set page context.
$PAGE->set_context($context);

// Set page URL.
$PAGE->set_url($pageurl);

// Set Page layout.
$PAGE->set_pagelayout('standard');

// Get renderable.
$renderable = new \local_edwiserreports\output\elucidreport_renderable();
$output = $PAGE->get_renderer($component)->render($renderable);

// Set page heading.
$PAGE->set_heading(get_string("reportsdashboard", "local_edwiserreports"));
$PAGE->set_title(get_string("reportsdashboard", "local_edwiserreports"));

// Print output in page.
echo $OUTPUT->header();
echo $output;
echo $OUTPUT->footer();
