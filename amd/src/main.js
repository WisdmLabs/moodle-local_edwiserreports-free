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
