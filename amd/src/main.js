define([
    'report_elucidsitereport/block_accessinfo',
    'report_elucidsitereport/block_activecourses',
    'report_elucidsitereport/block_activeusers',
    'report_elucidsitereport/block_courseprogress',
    'report_elucidsitereport/block_inactiveusers',
    'report_elucidsitereport/block_lpstats',
    'report_elucidsitereport/block_realtimeusers',
    'report_elucidsitereport/block_todaysactivity',
    'report_elucidsitereport/common'
], function (
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
        activeUsers : {
            func : activeUsers,
            status : false
        },
        courseProgress : {
            func : courseProgress,
            status : false
        },
        realTimeUsers : {
            func : realTimeUsers,
            status : false
        },
        accessInfo : {
            func : accessInfo,
            status : false
        },
        lpStatsBlock : {
            func : lpStatsBlock,
            status : false
        }
        ,
        inActiveUsers : {
            func : inActiveUsers,
            status : false
        },
        todaysActivity : {
            func : todaysActivity,
            status : false
        },
        activeCourses : {
            func : activeCourses,
            status : false
        }
    };

    /**
     * Notify listner to listen if done execution
     * @param  {[type]} event [description]
     * @return {[type]}       [description]
     */
    var notifyListner = function (event) {
        notif[event]["status"] = true;
        var blockName = getKeyByValue(notif, false);
        if (blockName) {
            executeFunctionByName(blockName);
        }
    }

    /**
     * Execute function by name
     * @param  {string} blockName Block Name
     */
    var executeFunctionByName = function (blockName) {
        notif[blockName]["func"].init(notifyListner);
    }

    /**
     * Get key by value
     * @param  {object} obj Object on search
     * @param  {string} value Value to search
     * @return {string} object key
     */
    var getKeyByValue = function(obj, value) {
        for(var prop in obj) {
            if(obj.hasOwnProperty(prop)) {
                 if(obj[prop]["status"] === value)
                     return prop;
            }
        }
    }

    /**
     * Init main.js
     */
    var init = function () {
        var blockName = getKeyByValue(notif, false);
        notif[blockName]["func"].init(notifyListner);
    }

    // Must return the init function
    return {
        init : init
    };
});