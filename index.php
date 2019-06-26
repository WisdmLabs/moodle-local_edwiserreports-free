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

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once('classes/output/elucidreport_renderer.php');
require_once('classes/output/elucidreport_renderable.php');

admin_externalpage_setup('elucidsitereport');

$PAGE->requires->js_call_amd('report_elucidsitereport/activeusersblock', 'init');
$PAGE->requires->js_call_amd('report_elucidsitereport/courseprogressblock', 'init');
$PAGE->requires->js_call_amd('report_elucidsitereport/lpstatsblock', 'init');
$PAGE->requires->js_call_amd('report_elucidsitereport/todaysactivityblock', 'init');

$elucidreport = new \report_elucidsitereport\output\elucidreport();
$reportrenderable = new \report_elucidsitereport\output\elucidreport_renderable();
$output = $elucidreport->get_renderer()->render($reportrenderable);

echo $OUTPUT->header();
echo $output;
echo $OUTPUT->footer();
