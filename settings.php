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

defined('MOODLE_INTERNAL') || die();

// $ADMIN->add('reports', new admin_category(
//     'local_sitereport_settings',
//     new lang_string('pluginname', 'local_sitereport')
// ));
// $ADMIN->add('local_sitereport_settings', new admin_externalpage(
//     'elucidsitereport_dashboard',
//     new lang_string('myhome'), "/local/sitereport/index.php"
// ));
// $ADMIN->add('local_sitereport_settings', new admin_externalpage(
//     'elucidsitereport_settings',
//     new lang_string('settings'), "$CFG->wwwroot/local/sitereport/reports_settings.php"
// ));

// $ADMIN->add('localplugins',
//     new admin_category(
//         'local_sitereport_settings',
//         new lang_string('pluginname', 'local_sitereport')
//     )
// );

require_once($CFG->dirroot . '/local/sitereport/lib.php');

$blocks = get_default_block_settings();
$roles = array_map(function ($role) {
    return $role->localname;
}, role_fix_names(get_all_roles()));

$settingspage = new admin_settingpage('managesitereports', new lang_string('managesitereports', 'local_sitereport'));

foreach ($blocks as $blockid => $block) {
    $allowedroles = get_roles_with_capability('report/sitereport_' . $blockid . 'block:view');
    $settingspage->add(new admin_setting_configmultiselect(
        'local_sitereport/' . $blockid . 'roleallow',
        new lang_string($blockid . 'rolesetting', 'local_sitereport'),
        new lang_string($blockid . 'rolesettinghelp', 'local_sitereport'),
        array_keys($allowedroles),
        $roles
    ));
}

$ADMIN->add('localplugins', $settingspage);
