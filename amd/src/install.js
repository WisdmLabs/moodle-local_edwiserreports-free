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
 * @copyright   2021 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/* eslint-disable no-console */
define(['jquery', 'core/ajax'], function($, ajax) {

    /**
     * Find duplicate positions in list
     * @param {Array} list Position list
     * @returns {Array}
     */
    function findDuplicates(list) {
        return list.filter((item, index) => list.indexOf(item) !== index);
    }

    /**
     * Find missing positions in list.
     * @param {Array} list Position list
     * @param {Array} dummyPositions Dummy positions to search
     * @returns {Array}
     */
    function findMissings(list, dummyPositions) {
        return dummyPositions.filter(item => list.indexOf(item) === -1);
    }

    /**
     * Fix duplicate position from list.
     * @param {Array} list Position list
     * @param {Integer} duplicate Duplicate position
     * @param {Integer} missing Missing position
     */
    function fixDuplicatePosition(list, duplicate, missing) {
        let skippedFirst = false;
        let fixer = duplicate > missing ? -1 : 1;
        list.forEach(function(item, index) {
            // Skip first duplicate item.
            if (item == duplicate && skippedFirst == false) {
                skippedFirst = true;
                return;
            }

            // Fixed duplicate item.
            if (item == duplicate) {
                list[index] = item + fixer;
                return;
            }

            // Decrease item position.
            if (duplicate > missing && item > missing && item < duplicate) {
                list[index] = item + fixer;
                return;
            }

            // Increase item position.
            if (duplicate < missing && item < missing && item > duplicate) {
                list[index] = item + fixer;
                return;
            }
        });
    }

    /**
     * Fix duplicate positions in list.
     * @param {Array} list Position list
     * @param {String} positionSelector Position select
     */
    function fixDuplicatePositions(list, positionSelector) {
        // Create dummy positions.
        let dummyPositions = [...Array(list.length).keys()];

        // Find duplicate positions.
        let duplicates = findDuplicates(list);

        // Find missing positions.
        let missings = findMissings(list, dummyPositions);
        while (duplicates.length > 0) {
            for (let i = 0; i < duplicates.length; i++) {
                let duplicate = duplicates[i];
                let missing = missings[i];
                fixDuplicatePosition(list, duplicate, missing);
            }
            duplicates = findDuplicates(list);
            missings = findMissings(list, dummyPositions);
        }

        // Apply fixed positions.
        $(positionSelector).each(function(index, position) {
            $(position).val(list[index]);
        });
    }

    /**
     * Initialize position handler.
     */
    function initializePositionsHandler() {
        var positionSelector = 'select[id ^=id_s_local_edwiserreports][id $=position]';

        var currentPositions = [];
        $(positionSelector).each(function(idx, val) {
            currentPositions.push(parseInt($(val).val()));
        });

        // Fix duplicate positions.
        fixDuplicatePositions(currentPositions, positionSelector);

        $(positionSelector).on('change', function() {
            var _this = this;
            var posChangedIdx = false;
            $(positionSelector).each(function(idx, val) {
                if (_this.name == val.name) {
                    posChangedIdx = idx;
                    return;
                }
            });

            var prevSelectVal = parseInt(currentPositions[posChangedIdx]);
            var currSelectVal = parseInt($(this).val());
            var updater = prevSelectVal > currSelectVal ? 1 : -1;

            // Rearrange other positions.
            currentPositions.forEach((position, index) => {
                if (prevSelectVal == position) {
                    position = currSelectVal;
                } else if (prevSelectVal > currSelectVal) {
                    if (position < prevSelectVal && position >= currSelectVal) {
                        position += updater;
                    }
                } else if (prevSelectVal < currSelectVal) {
                    if (position > prevSelectVal && position <= currSelectVal) {
                        position += updater;
                    }
                }
                currentPositions[index] = position;
            });

            $(positionSelector).each(function(index, position) {
                $(position).val(currentPositions[index]);
            });

        });
    }

    var init = function() {
        var getConfig = 'local_edwiserreports_get_plugin_config';
        var getPluginConfig = ajax.call([{
            methodname: getConfig,
            args: {
                pluginname: 'local_edwiserreports',
                configname: 'edwiserreportsinstallation'
            }
        }]);

        getPluginConfig[0].done(function(response) {
            if (response.success) {
                var completeInstallation = 'local_edwiserreports_complete_edwiserreports_installation';
                var completePluginInstallation = ajax.call([{
                    methodname: completeInstallation,
                    args: {}
                }]);

                completePluginInstallation[0].done();
            }
        });

        $(document).ready(function() {
            $('#page-admin-setting-manageedwiserreports #adminsettings [type="submit"]').on('click', function(event) {
                event.preventDefault();
                var setConfig = 'local_edwiserreports_set_plugin_config';
                var setPluginConfig = ajax.call([{
                    methodname: setConfig,
                    args: {
                        pluginname: 'local_edwiserreports',
                        configname: 'edwiserreportsinstallation'
                    }
                }]);

                setPluginConfig[0].done(function() {
                    $('#adminsettings').submit();
                });
            });

            initializePositionsHandler();
        });
    };

    return {
        init: init
    };
});
