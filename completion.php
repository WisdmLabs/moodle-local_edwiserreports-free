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

use context_course;
use moodle_url;
use context_helper;

require_once(__DIR__ . '/../../config.php');
require_once('classes/output/elucidreport_renderer.php');
require_once('classes/output/elucidreport_renderable.php');

// require_login();

$courseid = required_param("courseid", PARAM_INT);
$coursecontext = context_course::instance($courseid);
$params = array(
	"courseid" => $courseid
);

$pageurl = new moodle_url($CFG->wwwroot . "/report/elucidsitereport/completion.php", $params);

$PAGE->set_context($coursecontext);
$PAGE->set_url($pageurl);
$PAGE->requires->js_call_amd('report_elucidsitereport/completion', 'init', array($coursecontext->id));

$course = get_course($courseid);
require_login($course);

$completion = new \report_elucidsitereport\output\completion();
$completionrenderable = new \report_elucidsitereport\output\completion_renderable();
$output = $completion->get_renderer()->render($completionrenderable);

echo $OUTPUT->header();
echo create_back_button($CFG->wwwroot . "/report/elucidsitereport/");
echo $OUTPUT->heading(get_string("completionheader", "report_elucidsitereport"), 1 , "page-title p-5 mb-10");
echo $output;
echo $OUTPUT->footer();