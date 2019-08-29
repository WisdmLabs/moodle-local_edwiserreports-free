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

require_login();

$context = context_system::instance();
// The requested section isn't in the admin tree
// It could be because the user has inadequate capapbilities or because the section doesn't exist
if (!has_capability('moodle/site:config', $context)) {
    // The requested section could depend on a different capability
    // but most likely the user has inadequate capabilities
    print_error('accessdenied', 'admin');
}

$context = context_system::instance();
$PAGE->requires->js_call_amd('report_elucidsitereport/courseprogress', 'init', array($context->id));
$PAGE->requires->js_call_amd('report_elucidsitereport/courseengage', 'init', array($context->id));

$pageurl = new moodle_url($CFG->wwwroot . "/report/elucidsitereport/coursereport.php");

$PAGE->set_context($context);
$PAGE->set_url($pageurl);

$coursereport = new \report_elucidsitereport\output\coursereport();
$coursereportrenderable = new \report_elucidsitereport\output\coursereport_renderable();
$output = $coursereport->get_renderer()->render($coursereportrenderable);

echo $OUTPUT->header();
echo create_back_button($CFG->wwwroot . "/report/elucidsitereport/");
echo $OUTPUT->heading(get_string("coursereports", "report_elucidsitereport"), 1, "page-title p-5 mb-20");
echo $output;
echo $OUTPUT->footer();
