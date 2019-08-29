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
require_once('classes/output/elucidreport_renderer.php');
require_once('classes/output/elucidreport_renderable.php');

$context = context_system::instance();
// The requested section isn't in the admin tree
// It could be because the user has inadequate capapbilities or because the section doesn't exist
if (!has_capability('moodle/site:config', $context)) {
    // The requested section could depend on a different capability
    // but most likely the user has inadequate capabilities
    print_error('accessdenied', 'admin');
}

$PAGE->requires->js_call_amd('report_elucidsitereport/f2fsessions', 'init', array($context->id));
$PAGE->requires->css('/report/elucidsitereport/styles/select2.min.css');

$pageurl = new moodle_url($CFG->wwwroot . "/report/elucidsitereport/f2fsessions.php");

$PAGE->set_context($context);
$PAGE->set_url($pageurl);

$f2fsessions = new \report_elucidsitereport\output\f2fsessions();
$f2fsessionsrenderable = new \report_elucidsitereport\output\f2fsessions_renderable();
$output = $f2fsessions->get_renderer()->render($f2fsessionsrenderable);

echo $OUTPUT->header();
echo create_back_button($CFG->wwwroot . "/report/elucidsitereport/");
echo $OUTPUT->heading(get_string("f2fsessionsheader", "report_elucidsitereport"), 1, "page-title p-5 mb-10");
echo $output;
echo $OUTPUT->footer();
