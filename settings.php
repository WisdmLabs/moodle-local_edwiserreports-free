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
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/edwiserreports/lib.php');

$blocks = local_edwiserreports_get_default_block_settings();
$roles = array_map(function ($role) {
    return $role->localname;
}, role_fix_names(get_all_roles()));

$settingspage = new admin_settingpage('manageedwiserreportss', new lang_string('manageedwiserreportss', 'local_edwiserreports'));

$availsizedesktop = array(
    LOCAL_SITEREPORT_BLOCK_LARGE => get_string('large', 'local_edwiserreports'),
    LOCAL_SITEREPORT_BLOCK_MEDIUM => get_string('medium', 'local_edwiserreports'),
    LOCAL_SITEREPORT_BLOCK_SMALL => get_string('small', 'local_edwiserreports')
);

$availsizetablet = array(
    LOCAL_SITEREPORT_BLOCK_LARGE => get_string('large', 'local_edwiserreports'),
    LOCAL_SITEREPORT_BLOCK_MEDIUM => get_string('medium', 'local_edwiserreports')
);

$positions = range(1, count($blocks), 1);

$currentpos = 0;
foreach ($blocks as $blockid => $block) {
    $settingspage->add(new admin_setting_heading(
        'local_edwiserreports/' . $blockid,
        new lang_string($blockid . 'header', 'local_edwiserreports'),
        ''
    ));

    $settingspage->add(new admin_setting_configselect(
        'local_edwiserreports/' . $blockid . 'position',
        new lang_string($blockid . 'position', 'local_edwiserreports'),
        new lang_string($blockid . 'positionhelp', 'local_edwiserreports'),
        $currentpos++,
        $positions
    ));

    // Desktopview for blocks.
    $settingspage->add(new admin_setting_configselect(
        'local_edwiserreports/' . $blockid . 'desktopsize',
        new lang_string($blockid . 'desktopsize', 'local_edwiserreports'),
        new lang_string($blockid . 'desktopsizehelp', 'local_edwiserreports'),
        LOCAL_SITEREPORT_BLOCK_MEDIUM,
        $availsizedesktop
    ));

    // Tablet view for blocks.
    $settingspage->add(new admin_setting_configselect(
        'local_edwiserreports/' . $blockid . 'tabletsize',
        new lang_string($blockid . 'tabletsize', 'local_edwiserreports'),
        new lang_string($blockid . 'tabletsizehelp', 'local_edwiserreports'),
        LOCAL_SITEREPORT_BLOCK_LARGE,
        $availsizetablet
    ));

    // Roles setting for blocks.
    $allowedroles = get_roles_with_capability('report/edwiserreports_' . $blockid . 'block:view');
    $settingspage->add(new admin_setting_configmultiselect(
        'local_edwiserreports/' . $blockid . 'roleallow',
        new lang_string($blockid . 'rolesetting', 'local_edwiserreports'),
        new lang_string($blockid . 'rolesettinghelp', 'local_edwiserreports'),
        array_keys($allowedroles),
        $roles
    ));
}

$ADMIN->add('localplugins', $settingspage);

if (optional_param('section', '', PARAM_TEXT) == 'manageedwiserreportss') {
    global $PAGE;
    $PAGE->requires->js(new moodle_url('/local/edwiserreports/settings.js'));
    $PAGE->requires->js_call_amd('local_edwiserreports/settings', 'init');
}
