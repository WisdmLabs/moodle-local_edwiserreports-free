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
 * @package     local_sitereport
 * @category    admin
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_sitereport;

use context_system;
use moodle_url;

require_once(__DIR__ . '/../../config.php');
require_once('classes/output/renderable.php');

// Required login.
require_login();

local_sitereport_get_recquired_strings_for_js();

// System Context.
$context = context_system::instance();
$component = 'local_sitereport';


// Require JS for active users page.
$PAGE->requires->js_call_amd(
    'local_sitereport/activeusers',
    'init',
    array($context->id)
);

// Set css for active usres page.
$PAGE->requires->css('/local/sitereport/styles/flatpickr.min.css');

// Require CSS.
$PAGE->requires->css('/local/sitereport/styles/loader.css');

// Add js string for this page.
$PAGE->requires->strings_for_js([
    'activeusersmodaltitle',
    'enrolmentsmodaltitle',
    'completionsmodaltitle'
], $component);

// Page URL for active users page.
$pageurl = new moodle_url($CFG->wwwroot . "/local/sitereport/activeusers.php");

// Set page context.
$PAGE->set_context($context);

// Set page URL.
$PAGE->set_url($pageurl);

// Get active users renderable.
$renderable = new \local_sitereport\output\activeusers_renderable();
$output = $PAGE->get_renderer($component)->render($renderable);

$PAGE->set_heading(get_string("activeusersheader", "local_sitereport"));
$PAGE->set_title(get_string("activeusersheader", "local_sitereport"));

// Print output in the page.
echo $OUTPUT->header();
echo $output;
echo $OUTPUT->footer();
