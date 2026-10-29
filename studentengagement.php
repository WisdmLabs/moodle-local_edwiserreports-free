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

require_once(__DIR__ . '/../../config.php');
require_once('classes/output/renderable.php');

require_login();

// Context system.
$context = context_system::instance();
$component = "local_edwiserreports";

// Load all js files from externaljs folder.
foreach (scandir($CFG->dirroot . '/local/edwiserreports/externaljs/build/') as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) != 'js') {
        continue;
    }
    $PAGE->requires->js(new moodle_url('/local/edwiserreports/externaljs/build/' . $file));
}

local_edwiserreports_get_required_strings_for_js();

// Load color themes from constants.
local_edwiserreports\utility::load_color_pallets();

// Get context.
$context = context_system::instance();

// // Check capability.
// $capname = 'report/edwiserreports_studentengagement:view';
// if (!has_capability($capname, $context) &&
//     !can_view_block($capname)) {
//     throw new moodle_exception(get_string('noaccess', 'local_edwiserreports'));
// }

// Check if user has access to any group.
$blockbase = new local_edwiserreports\block_base();
if (empty($blockbase->get_courses_of_user($USER->id))) {
    throw new moodle_exception('noaccess', 'local_edwiserreports');
}

// Add CSS for edwiserreports.
$PAGE->requires->css('/local/edwiserreports/styles/edwiserreports.min.css');

// Page URL.
$pageurl = new moodle_url($CFG->wwwroot . "/local/edwiserreports/studentengagement.php");

// Set page context.
$PAGE->set_context($context);

// Set page layout.
$PAGE->set_pagelayout('base');

// Add theme class to body.
$PAGE->add_body_classes(array('theme_' . $PAGE->theme->name, 'local-edwiserreports', 'report-page'));

// Set page URL.
$PAGE->set_url($pageurl);

// Require JS for course completion page.
// Js call moved to templates/reports/studentengagement.mustache file.

// Get renderable for coourse completion page.
$renderable = new \local_edwiserreports\output\studentengagement_renderable();
$output = $PAGE->get_renderer($component)->render($renderable);

$PAGE->set_heading('');
$PAGE->set_title(get_string("alllearnersummary", "local_edwiserreports"));

// Print output for course completion page.
echo $OUTPUT->header();
echo $output;
echo $OUTPUT->footer();
