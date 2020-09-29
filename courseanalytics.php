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

require_once(__DIR__ . '/../../config.php');
require_once('classes/output/renderable.php');

// System context.
$context = context_system::instance();
$component = "local_sitereport";

// Get required param course id.
$courseid = required_param("courseid", PARAM_INT);

// Required course login.
$course = get_course($courseid);
require_login($course);

// Get course context.
$coursecontext = context_course::instance($courseid);

// Page URL.
$pageurl = new moodle_url($CFG->wwwroot . "/local/sitereport/courseanalytics.php", array("courseid" => $courseid));

// Set page context.
$PAGE->set_context($coursecontext);

$PAGE->set_pagelayout('course');

// Set page URL.
$PAGE->set_url($pageurl);

// Require JS for course analytics page.
$PAGE->requires->js_call_amd('local_sitereport/courseanalytics', 'init', array($coursecontext->id));

// Get renderer for course analytics.
$renderable = new \local_sitereport\output\courseanalytics_renderable();
$output = $PAGE->get_renderer($component)->render($renderable);

$PAGE->set_heading(get_string("courseanalyticsheader", "local_sitereport", array('coursename' => $course->fullname)));

// Print output in page.
echo $OUTPUT->header();
echo $output;
echo $OUTPUT->footer();
