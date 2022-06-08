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

require_once($CFG->dirroot . '/local/edwiserreports/classes/constants.php');

$ADMIN->add('modules', new admin_category('edwiserreports', new lang_string("pluginname", "local_edwiserreports")));

// Dashboard link.
$ADMIN->add('edwiserreports',
    new admin_externalpage(
        'local_edwiserreports/dashboard',
        new lang_string("myhome"),
        new moodle_url("/local/edwiserreports/index.php")
    )
);

// Settings.
$ADMIN->add('edwiserreports',
    new admin_externalpage(
        'local_edwiserreports/settings',
        new lang_string("settings"),
        new moodle_url("/admin/settings.php?section=local_edwiserreports")
    )
);

// Adding tab setting for the edwiserreports.
$settings = new local_edwiserreports_admin_settingspage_tabs(
    'local_edwiserreports',
    get_string('edwiserReport_settings', 'local_edwiserreports')
);

$ADMIN->add('localplugins', $settings);

// General Settings tab.
$page = new admin_settingpage('local_edwiserreports_general_settings', new lang_string('generalsettings', 'local_edwiserreports'));

// Track frequency.
$page->add(new admin_setting_configduration(
    'local_edwiserreports/trackfrequency',
    new lang_string('trackfrequency', 'local_edwiserreports'),
    new lang_string('trackfrequencyhelp', 'local_edwiserreports'),
    300,
    1
));

// Use precalculated data for Weekly, Monthly and Yearly filter.
$page->add(new admin_setting_configcheckbox(
    'local_edwiserreports/precalculated',
    new lang_string('precalculated', 'local_edwiserreports'),
    new lang_string('precalculatedhelp', 'local_edwiserreports'),
    1,
    1
));

$help = "<table class='table-color-theme'><tbody>";
$themes = [];
foreach (LOCAL_EDWISERREPORTS_COLOR_THEMES as $key => $theme) {
    $label = get_string('theme', 'local_edwiserreports') . ' ' . ($key + 1);
    $disabled = '';
    if ($key > 0) {
        $label .= ' <strong>(PRO)</strong>';
        $disabled = 'disabled';
    }
    $themes[] = $label;
    $help .= "<tr>";
    $help .= "<td style='font-weight: 700; color: #222;'>";
    $help .= "<div class='form-check $disabled'>
                <input class='form-check-input' name='theme-radio' type='radio' id='theme-radio-$key' value='$key' $disabled>
                <label class='form-check-label' for='theme-radio-$key'>
                    $label
                </label>
            </div>";
    $help .= "</td>";
    foreach ($theme as $color) {
        $help .= "<td style='
                    background-color: $color;
                    color: #fff;
                    font-size: 0.87rem;
                    text-transform: uppercase;
                    padding: 1rem 2.5rem;
                    border: 0.47rem solid #fff;
                '></td>";
    }
    $help .= "</tr>";
}
$help .= "</tbody></table>";
$help .= "<div class='text-left my-3'>";
$help .= "<a href='" . UPGRADE_URL . "' class='btn btn-warning text-white' target='_blank'>";
$help .= get_string('upgradetopro', 'local_edwiserreports');
$help .= "</a>";
$help .= "</div>";

$page->add(new admin_setting_configselect(
    'local_edwiserreports/theme',
    new lang_string('colortheme', 'local_edwiserreports'),
    new lang_string('colorthemehelp', 'local_edwiserreports') . $help,
    0,
    $themes
));

$settings->add($page);

// Block's Settings tab.
$page = new admin_settingpage('local_edwiserreports_blocks_settings', new lang_string('blockssettings', 'local_edwiserreports'));

$blocks = local_edwiserreports_get_default_block_settings();
$roles = array_map(function ($role) {
    return $role->localname;
}, role_fix_names(get_all_roles()));

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

$positionwarning = $PAGE->pagetype == 'admin-upgradesettings' ? get_string('positionhelpupgrade', 'local_edwiserreports') : '';

$problocks = [
    'visitsonsite',
    'timespentonsite',
    'timespentoncourse',
    'courseactivitystatus',
    'learnertimespentonsite',
    'grade',
    'learnercourseprogress'
];
foreach ($blocks as $blockid => $block) {
    $prolabel = '';
    if (in_array($blockid, $problocks)) {
        $prolabel = '<strong>(PRO)</strong>';
    }
    $prefix = $PAGE->pagetype == 'admin-upgradesettings' ? $blockid : '';

    $page->add(new admin_setting_heading(
        'local_edwiserreports/' . $blockid,
        new lang_string($blockid . 'header', 'local_edwiserreports'),
        ''
    ));

    $page->add(new admin_setting_configselect(
        'local_edwiserreports/' . $blockid . 'position',
        new lang_string($prefix . 'position', 'local_edwiserreports') . $prolabel,
        new lang_string('positionhelp', 'local_edwiserreports') . $positionwarning,
        $currentpos++,
        $positions
    ));

    // Desktopview for blocks.
    $page->add(new admin_setting_configselect(
        'local_edwiserreports/' . $blockid . 'desktopsize',
        new lang_string($prefix . 'desktopsize', 'local_edwiserreports') . $prolabel,
        new lang_string('desktopsizehelp', 'local_edwiserreports'),
        $block['desktopview'],
        $availsizedesktop
    ));

    // Tablet view for blocks.
    $page->add(new admin_setting_configselect(
        'local_edwiserreports/' . $blockid . 'tabletsize',
        new lang_string($prefix . 'tabletsize', 'local_edwiserreports') . $prolabel,
        new lang_string('tabletsizehelp', 'local_edwiserreports'),
        $block['tabletview'],
        $availsizetablet
    ));
}

$settings->add($page);

if (optional_param('section', '', PARAM_TEXT) == 'local_edwiserreports' ||
    stripos($_SERVER['REQUEST_URI'], 'upgradesettings.php') !== false) {
    global $PAGE;
    $PAGE->requires->js(new moodle_url('/local/edwiserreports/settings.js'));
    $PAGE->requires->js_call_amd('local_edwiserreports/settings', 'init');

    if (optional_param('action', '', PARAM_TEXT) == 'save-settings') {
        set_config(
            'activetab',
            optional_param('activetab', 'local_edwiserreports_general_settings', PARAM_TEXT),
            'local_edwiserreports'
        );
    }
}
