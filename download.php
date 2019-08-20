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

use report_elucidsitereport\export;

require_once __DIR__ .'/../../config.php';
require_once $CFG->dirroot."/report/elucidsitereport/classes/export.php";

require_login();

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url($CFG->wwwroot . "/report/elucidsitereport/index.php");

if ($format = optional_param("format", false, PARAM_TEXT)) {
	$region = required_param("region", PARAM_TEXT);
	$blockname = required_param("blockname", PARAM_TEXT);
	$filter = optional_param("filter", false, PARAM_TEXT);

	$export = new export($format, $region, $blockname);
	$data = $export->get_exportable_data($filter);

	if ($data) {
		$export->data_export($region."_".$blockname, $data);
	}
}

redirect($PAGE->url, get_string("emailsent", "report_elucidsitereport"));