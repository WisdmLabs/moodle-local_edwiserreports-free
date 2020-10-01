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

require_once($CFG->dirroot . '/local/sitereport/lib.php');

$blocks = get_default_block_settings();
$roles = array_map(function ($role) {
    return $role->localname;
}, role_fix_names(get_all_roles()));

$settingspage = new admin_settingpage('managesitereports', new lang_string('managesitereports', 'local_sitereport'));

$availsizedesktop = array(
    LOCAL_SITEREPORT_BLOCK_LARGE => get_string('large', 'local_sitereport'),
    LOCAL_SITEREPORT_BLOCK_MEDIUM => get_string('medium', 'local_sitereport'),
    LOCAL_SITEREPORT_BLOCK_SMALL => get_string('small', 'local_sitereport')
);

$availsizetablet = array(
    LOCAL_SITEREPORT_BLOCK_LARGE => get_string('large', 'local_sitereport'),
    LOCAL_SITEREPORT_BLOCK_MEDIUM => get_string('medium', 'local_sitereport')
);

$positions = range(1, count($blocks), 1);

$currentpos = 0;
foreach ($blocks as $blockid => $block) {
    $settingspage->add(new admin_setting_heading(
        'local_sitereport/' . $blockid,
        new lang_string($blockid . 'header', 'local_sitereport'),
        ''
    ));

    $settingspage->add(new admin_setting_configselect(
        'local_sitereport/' . $blockid . 'position',
        new lang_string($blockid . 'position', 'local_sitereport'),
        new lang_string($blockid . 'positionhelp', 'local_sitereport'),
        $currentpos++,
        $positions
    ));

    // Desktopview for blocks.
    $settingspage->add(new admin_setting_configselect(
        'local_sitereport/' . $blockid . 'desktopsize',
        new lang_string($blockid . 'desktopsize', 'local_sitereport'),
        new lang_string($blockid . 'desktopsizehelp', 'local_sitereport'),
        LOCAL_SITEREPORT_BLOCK_MEDIUM,
        $availsizedesktop
    ));

    // Tablet view for blocks.
    $settingspage->add(new admin_setting_configselect(
        'local_sitereport/' . $blockid . 'tabletsize',
        new lang_string($blockid . 'tabletsize', 'local_sitereport'),
        new lang_string($blockid . 'tabletsizehelp', 'local_sitereport'),
        LOCAL_SITEREPORT_BLOCK_LARGE,
        $availsizetablet
    ));

    // Roles setting for blocks.
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

if (optional_param('section', '', PARAM_TEXT) == 'managesitereports') {
    global $PAGE;
    $PAGE->requires->js(new moodle_url('/local/sitereport/settings.js'));
    $PAGE->requires->js_call_amd('local_sitereport/settings', 'init');
}
