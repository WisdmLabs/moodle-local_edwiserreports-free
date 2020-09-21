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

defined('MOODLE_INTERNAL') || die();

$ADMIN->add('reports', new admin_category(
    'report_elucidsitereport_settings',
    new lang_string('pluginname', 'report_elucidsitereport'
)));
$ADMIN->add('report_elucidsitereport_settings', new admin_externalpage(
    'elucidsitereport_dashboard',
    new lang_string('myhome'), "/report/elucidsitereport/index.php"
));
$ADMIN->add('report_elucidsitereport_settings', new admin_externalpage(
    'elucidsitereport_settings',
    new lang_string('settings'), "$CFG->wwwroot/report/elucidsitereport/reports_settings.php"
));
