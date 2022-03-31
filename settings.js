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
 * Plugin administration pages js.
 *
 * @package     local_edwiserreports
 * @copyright   2021 wisdmlabs <support@wisdmlabs.com>
 * @author      Yogesh Shirsath
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('local_edwiserreports/settings', ['jquery'], function($) {
    return {
        init: function() {
            /**
             * Selectors.
             */
            var SELECTORS = {
                TAB: '.edwiserreportstab',
                ACTIVE: '[name="activetab"]',
                LICENSE: '.edwiserreportstab-license',
                SUBMITBUTTON: '.settingsform button[type="submit"]'
            };

            /**
             * Check active tab is license.
             * If license tab then hide submit button.
             */
            function checkLicenseTab() {
                if ($(SELECTORS.TAB + '.active').is(SELECTORS.LICENSE)) {
                    $(SELECTORS.SUBMITBUTTON).hide();
                } else {
                    $(SELECTORS.SUBMITBUTTON).show();
                }
                $(SELECTORS.ACTIVE).val($(SELECTORS.TAB + '.active').attr('href').replace('#', ''));
            }

            $(document).ready(function() {
                // Preventing reload notification
                window.onbeforeunload = null;

                if ($(SELECTORS.TAB).length !== 0) {
                    // Tab change.
                    $('a[data-toggle="tab"]').on('shown.bs.tab', function() {
                        checkLicenseTab();
                    });

                    checkLicenseTab();
                }

                if ($('[name="theme-radio"]').length !== 0) {
                    // Hide default select options.
                    $('[name="theme-radio"][value="' + $('#id_s_local_edwiserreports_theme')
                        .hide().val() + '"]').prop('checked', true);

                    // Handling custom radio options.
                    $('[name="theme-radio"]').on('change', function() {
                        $('#id_s_local_edwiserreports_theme').val($('[name="theme-radio"]:checked').val());
                    });
                }

                // Disabling frequency setting.
                $('#admin-trackfrequency [name]').prop('disabled', true);

                // Disabling precalculated.
                $('#admin-precalculated [name]').prop('disabled', true);

                // Pro block settings disabled.
                [
                    'visitsonsite',
                    'timespentonsite',
                    'timespentoncourse',
                    'courseactivitystatus',
                    'learnertimespentonsite',
                    'grade',
                    'learnercourseprogress'
                ]
                .forEach(function(item) {
                    $('[id^="admin-' + item + '"] [name]').prop('disabled', true);
                });

                $('body').on('submit', '#adminsettings', function() {
                    $(this).find('[name]').prop('disabled', false);
                });
            });
        }
    };
});
