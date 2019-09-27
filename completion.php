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
use context_course;
use moodle_url;
use context_helper;

require_once(__DIR__ . '/../../config.php');
require_once('classes/output/elucidreport_renderer.php');
require_once('classes/output/elucidreport_renderable.php');

// Context system
$context = context_system::instance();
$component = "report_elucidsitereport";

// The requested section isn't in the admin tree
// It could be because the user has inadequate capapbilities or because the section doesn't exist
if (!has_capability('moodle/site:config', $context)) {
    // The requested section could depend on a different capability
    // but most likely the user has inadequate capabilities
    print_error('accessdenied', 'admin');
}

// Get require param course id
$courseid = required_param("courseid", PARAM_INT);

// Require login for course
$course = get_course($courseid);
require_login(get_course($courseid));

// Get course context
$coursecontext = context_course::instance($courseid);

// Page URL
$pageurl = new moodle_url($CFG->wwwroot . "/report/elucidsitereport/completion.php", array("courseid" => $courseid));

// Set page context
$PAGE->set_context($coursecontext);

// Set page URL
$PAGE->set_url($pageurl);

// Require JS for course completion page
$PAGE->requires->js_call_amd('report_elucidsitereport/completion', 'init', array($coursecontext->id));

// Get renderable for coourse completion page
$renderable = new \report_elucidsitereport\output\completion_renderable();
$output = $PAGE->get_renderer($component)->render($renderable);

// Print output for course completion page
echo $OUTPUT->header();
echo $OUTPUT->heading(create_page_header("completion", $course->fullname), "1", "page-title p-5");
echo $output;
echo $OUTPUT->footer();