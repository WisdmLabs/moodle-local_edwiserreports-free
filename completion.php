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
use context_course;
use moodle_url;
use context_helper;

require_once(__DIR__ . '/../../config.php');
require_once('classes/output/renderable.php');

// Context system.
$context = context_system::instance();
$component = "local_sitereport";

// Get require param course id.
$courseid = required_param("courseid", PARAM_INT);

// Require login for course.
$course = get_course($courseid);
require_login(get_course($courseid));

// Get course context.
$coursecontext = context_course::instance($courseid);

// Require CSS.
$PAGE->requires->css('/local/sitereport/styles/loader.css');

// Page URL.
$pageurl = new moodle_url($CFG->wwwroot . "/local/sitereport/completion.php", array("courseid" => $courseid));

// Set page context.
$PAGE->set_context($coursecontext);

$PAGE->set_pagelayout('course');

// Set page URL.
$PAGE->set_url($pageurl);

// Require JS for course completion page.
$PAGE->requires->js_call_amd('local_sitereport/completion', 'init', array($coursecontext->id));

// Get renderable for coourse completion page.
$renderable = new \local_sitereport\output\completion_renderable();
$output = $PAGE->get_renderer($component)->render($renderable);

$PAGE->set_heading(get_string("completionheader", "local_sitereport", array('coursename' => $course->fullname)));
$PAGE->set_title(get_string("completionheader", "local_sitereport", array('coursename' => $course->fullname)));

// Print output for course completion page.
echo $OUTPUT->header();
echo $output;
echo $OUTPUT->footer();
