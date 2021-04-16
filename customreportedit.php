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
 * @copyright   2020 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ .'/../../config.php');
require_once($CFG->dirroot . '/local/edwiserreports/classes/output/custom_reports_block.php');
require_once($CFG->dirroot . '/local/edwiserreports/locallib.php');

// Set external page admin.
$context = context_system::instance();
$component = "local_edwiserreports";

require_login();

// Check capability.
if (!has_capability('report/local_edwiserreports:view', $context)) {
    throw new moodle_exception(get_string('noaccess', 'local_edwiserreports'));
}


$reportsid = optional_param('id', 0, PARAM_INT);
if ($reportsid) {
    if (!$DB->record_exists('edwreports_custom_reports', array('id' => $reportsid))) {
        throw new moodle_exception('customreportsidnotmatch', 'error');
    }
}

if (!has_capability('report/edwiserreports_customreports:manage', $context)) {
    throw new moodle_exception('restrictaccess', 'error');
}

local_edwiserreports_get_required_strings_for_js();

// Page URL.
$pageurl = new moodle_url($CFG->wwwroot."/local/edwiserreports/customreportedit.php");

// Require CSS for index page.
$PAGE->requires->css('/local/edwiserreports/styles/edwiserreports.min.css');

// Set page context.
$PAGE->set_context($context);

// Set page URL.
$PAGE->set_url($pageurl);

// Set Page layout.
$PAGE->set_pagelayout('standard');

// Get renderable.
$renderable = new \local_edwiserreports\output\custom_reports_block($reportsid);
$output = $PAGE->get_renderer($component)->render($renderable);

// Set page heading.
$PAGE->set_heading(get_string("customreportedit", "local_edwiserreports"));
$PAGE->set_title(get_string("customreportedit", "local_edwiserreports"));

// Print output in page.
echo $OUTPUT->header();
echo $output;
echo $OUTPUT->footer();
