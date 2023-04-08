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
 * Course Activities Summary report table.
 *
 * @package     local_edwiserreports
 * @copyright   2022 Wisdmlabs <support@wisdmlabs.com>
 * @author      Yogesh Shirsath
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

// Add CSS for edwiserreports.
$PAGE->requires->css('/local/edwiserreports/styles/edwiserreports.min.css');

// Page URL.
$pageurl = new moodle_url($CFG->wwwroot . "/local/edwiserreports/allcoursessummary.php");

// Set page context.
$PAGE->set_context($context);

// Set page layout.
$PAGE->set_pagelayout('base');

// Add theme class to body.
$PAGE->add_body_classes(array('theme_' . $PAGE->theme->name, 'local-edwiserreports', 'report-page'));

// Set page URL.
$PAGE->set_url($pageurl);

// Require JS for course completion page.
// Js call moved to templates/reports/allcoursessummary.mustache file.

// Get renderable for coourse completion page.
$renderable = new \local_edwiserreports\output\allcoursessummary_renderable();
$output = $PAGE->get_renderer($component)->render($renderable);

$PAGE->set_heading('');
$PAGE->set_title(get_string("allcoursessummarypro", "local_edwiserreports"));

// Print output for course completion page.
echo $OUTPUT->header();
echo $output;
echo $OUTPUT->footer();
