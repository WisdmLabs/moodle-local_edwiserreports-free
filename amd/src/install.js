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
 * @copyright   2021 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/* eslint-disable no-console */
define(['jquery', 'core/ajax'], function($, ajax) {
    var init = function() {
        var getConfig = 'local_edwiserreports_get_plugin_config';
        var getPluginConfig = ajax.call([
            {
                methodname: getConfig,
                args: {
                    pluginname: 'local_edwiserreports',
                    configname: 'edwiserreportsinstallation'
                }
            }
        ]);

        getPluginConfig[0].done(function(response) {
            if (response.success) {
                var completeInstallation = 'local_edwiserreports_complete_edwiserreports_installation';
                var completePluginInstallation = ajax.call([
                    {
                        methodname: completeInstallation,
                        args: {}
                    }
                ]);

                completePluginInstallation[0].done(function(response) {
                    console.log(response);
                });
            }
        });

        $(document).ready(function() {
            $('#page-admin-setting-manageedwiserreportss #adminsettings [type="submit"]').on('click', function(event) {
                event.preventDefault();
                var setConfig = 'local_edwiserreports_set_plugin_config';
                var setPluginConfig = ajax.call([
                    {
                        methodname: setConfig,
                        args: {
                            pluginname: 'local_edwiserreports',
                            configname: 'edwiserreportsinstallation'
                        }
                    }
                ]);

                setPluginConfig[0].done(function() {
                    $('#adminsettings').submit();
                });
            });
        });

        var positionSelector = 'select[id ^=id_s_local_edwiserreports][id $=position]';

        var currentVal = [];
        $(positionSelector).each(function(idx, val) {
            currentVal.push($(val).val());
        });

        $(positionSelector).on('change', function() {
            var _this = this;
            var posChangedIdx = false;
            $(positionSelector).each(function(idx, val) {
                if (_this.name == val.name) {
                    posChangedIdx = idx;
                    return;
                }
            });

            var prevSelectVal = parseInt(currentVal[posChangedIdx]);
            var currSelectVal = parseInt($(this).val());

            $(positionSelector).each(function(idx, val) {
                var currVal = parseInt($(val).val());
                if (_this.name !== val.name) {
                    if (prevSelectVal > currSelectVal && prevSelectVal > currVal && currSelectVal <= currVal) {
                        $(val).val(parseInt(currVal) + 1);
                    } else if (prevSelectVal < currSelectVal && prevSelectVal < currVal && currSelectVal >= currVal) {
                        $(val).val(parseInt(currVal) - 1);
                    }
                }
            });

            currentVal = [];
            $(positionSelector).each(function(idx, val) {
                currentVal.push($(val).val());
            });
        });
    };

    return {
        init: init
    };
});
