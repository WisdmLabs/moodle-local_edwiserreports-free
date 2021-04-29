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

namespace local_edwiserreports;

use context_system;
use moodle_url;
use moodle_exception;

require_once(__DIR__ . '/../../config.php');
require_once('classes/output/renderable.php');

// Require login.
require_login();

local_edwiserreports_get_required_strings_for_js();

// System Context.
$context = context_system::instance();
$component = "local_edwiserreports";

// Check capability.
$capname = 'report/edwiserreports_certificatesblock:view';
if (!has_capability($capname, $context) &&
    !can_view_block($capname)) {
    throw new moodle_exception(get_string('noaccess', 'local_edwiserreports'));
}

// Require JS for certificates page.
$PAGE->requires->js_call_amd('local_edwiserreports/certificates', 'init', array($context->id));

// Add CSS for edwiserreports.
$PAGE->requires->css('/local/edwiserreports/styles/edwiserreports.min.css');

// Page URL.
$pageurl = new moodle_url($CFG->wwwroot . "/local/edwiserreports/certificates.php");

// Set page context.
$PAGE->set_context($context);

// Set Page layout.
$PAGE->set_pagelayout('standard');

// Set page URL.
$PAGE->set_url($pageurl);

// Get renderable for certificates page.
$renderable = new \local_edwiserreports\output\certificates_renderable();
$output = $PAGE->get_renderer($component)->render($renderable);

$PAGE->set_heading(get_string("certificatestats", "local_edwiserreports"));
$PAGE->set_title(get_string("certificatestats", "local_edwiserreports"));

// Print output in page.
echo $OUTPUT->header();
echo $output;
echo $OUTPUT->footer();
