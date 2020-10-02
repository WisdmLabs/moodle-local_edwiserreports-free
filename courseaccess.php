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

use context_course;
use moodle_url;
use context_system;
use \local_sitereport\output\courseaccess;

require_once(__DIR__ . '/../../config.php');
require_once('classes/output/renderable.php');

require_login();

local_sitereport_get_required_strings_for_js();

$context = context_system::instance();

$courseid = required_param("courseid", PARAM_INT);
$coursecontext = context_course::instance($courseid);
$params = array(
    "courseid" => $courseid
);

$pageurl = new moodle_url($CFG->wwwroot . "/local/sitereport/courseaccess.php", $params);

$PAGE->set_context($coursecontext);
$PAGE->set_url($pageurl);
$PAGE->requires->js_call_amd('local_sitereport/courseaccess', 'init', array($coursecontext->id));

$courseaccess = new local_sitereport\output\courseaccess();
$courseaccessrenderable = new \local_sitereport\output\courseaccess_renderable();
$output = $courseaccess->get_renderer()->render($courseaccessrenderable);

$course = get_course($courseid);
$PAGE->set_heading($course->fullname . ": " . get_string("courseaccessheader", "local_sitereport"));

echo $OUTPUT->header();
echo $output;
echo $OUTPUT->footer();
