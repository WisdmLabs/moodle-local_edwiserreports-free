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
define([
    'local_edwiserreports/block_accessinfo',
    'local_edwiserreports/block_activecourses',
    'local_edwiserreports/block_activeusers',
    'local_edwiserreports/block_courseprogress',
    'local_edwiserreports/block_inactiveusers',
    'local_edwiserreports/block_lpstats',
    'local_edwiserreports/block_realtimeusers',
    'local_edwiserreports/block_todaysactivity',
    'local_edwiserreports/common'
], function(
    accessInfo,
    activeCourses,
    activeUsers,
    courseProgress,
    inActiveUsers,
    lpStatsBlock,
    realTimeUsers,
    todaysActivity
) {
    var notif = {
        activeUsers: {
            func: activeUsers,
            status: false
        },
        courseProgress: {
            func: courseProgress,
            status: false
        },
        realTimeUsers: {
            func: realTimeUsers,
            status: false
        },
        accessInfo: {
            func: accessInfo,
            status: false
        },
        lpStatsBlock: {
            func: lpStatsBlock,
            status: false
        },
        inActiveUsers: {
            func: inActiveUsers,
            status: false
        },
        todaysActivity: {
            func: todaysActivity,
            status: false
        },
        activeCourses: {
            func: activeCourses,
            status: false
        }
    };

    /**
     * Notify listner to listen if done execution
     * @param  {Event} event Triggered event
     */
    var notifyListner = function(event) {
        notif[event].status = true;
        var blockName = getKeyByValue(notif, false);
        if (blockName) {
            executeFunctionByName(blockName);
        }
    };

    /**
     * Execute function by name
     * @param  {string} blockName Block Name
     */
    var executeFunctionByName = function(blockName) {
        notif[blockName].func.init(notifyListner);
    };

    /**
     * Get key by value
     * @param  {object} obj Object on search
     * @param  {string} value Value to search
     * @return {string} object key
     */
    var getKeyByValue = function(obj, value) {
        for (var prop in obj) {
            if (obj.hasOwnProperty(prop)) {
                if (obj[prop].status === value) {
                    return prop;
                }
            }
        }
        return null;
    };

    /**
     * Init main.js
     */
    var init = function() {
        var blockName = getKeyByValue(notif, false);
        notif[blockName].func.init(notifyListner);
    };

    // Must return the init function
    return {
        init: init
    };
});
