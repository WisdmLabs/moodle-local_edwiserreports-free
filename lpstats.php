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
use moodle_url;

require_once(__DIR__ . '/../../config.php');
require_once('classes/output/renderable.php');

// System Context
$context = context_system::instance();
$component = "report_elucidsitereport";

// Require JS for lpstats page
$PAGE->requires->js_call_amd('report_elucidsitereport/lpstats', 'init', array($context->id));

// Require CSS for lpstats page
$PAGE->requires->css('/report/elucidsitereport/styles/select2.min.css');

// Page URL
$pageurl = new moodle_url($CFG->wwwroot . "/report/elucidsitereport/lpstats.php");

// Set page context
$PAGE->set_context($context);

// Set page URL
$PAGE->set_url($pageurl);

// Get lpstats renderer
$lpstatsrenderable = new \report_elucidsitereport\output\lpstats_renderable();
$output = $PAGE->get_renderer($component)->render($lpstatsrenderable);

// Print output in page
echo $OUTPUT->header();
echo $OUTPUT->heading(create_page_header("lpstats"), "1", "page-title p-5");
echo $output;
echo $OUTPUT->footer();
