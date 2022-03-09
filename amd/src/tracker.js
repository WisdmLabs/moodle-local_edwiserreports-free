define([
    'jquery',
    'core/ajax',
    'core/notification',
    './defaultconfig'
], function(
    $,
    Ajax,
    Notification,
    CFG
) {

    /**
     * All ajax promises.
     */
    let PROMISES = {

        /**
         * Check if plugin is installed.
         * @returns {PROMISE}
         */
        IS_INSTALLED: function() {
            return $.ajax({
                url: CFG.requestUrl,
                type: CFG.requestType,
                dataType: CFG.requestDataType,
                data: {
                    action: 'is_installed_ajax'
                },
            });
        },

        /**
         * Fetch tracking details using context id.
         * @param {Integer} contextid Current page context id
         * @returns {PROMISE}
         */
        GET_TRACKING_DETAILS: function(contextid) {
            return Ajax.call([{
                methodname: 'local_edwiserreports_get_tracking_details',
                args: {
                    contextid: contextid
                }
            }])[0];
        },

        /**
         * Send keep alive request for current activity.
         * @param {Integer} id Track id
         * @param {Integer} frequency Time to add in track
         * @returns {PROMISE}
         */
        KEEP_ALIVE: function(id, frequency) {
            return Ajax.call([{
                methodname: 'local_edwiserreports_keep_alive',
                args: {
                    id: id,
                    frequency: frequency
                }
            }], true, false, true)[0];
        }
    };
    /**
     * Timer variable.
     */
    let timer = null;

    /**
     * Seconds Ticker variable.
     */
    let ticker = null;

    /**
     * Global variable which keeps track of time.
     */
    let time = 0;

    /**
     * Update spend time to db.
     * @param {Integer} id Track id
     */
    function updateTime(id) {
        PROMISES.KEEP_ALIVE(id, time);
        time = 0;
    }

    /**
     * Initialize
     */
    function init() {
        PROMISES.GET_TRACKING_DETAILS(M.cfg.contextid)
            .done(function(response) {
                if (response.status === false) {
                    return;
                }

                // Update time on page close/unload.
                window.addEventListener('beforeunload', function(event) {
                    updateTime(response.id);
                    clearInterval(ticker);
                    clearInterval(timer);
                });
                // Seconds Increament.
                ticker = setInterval(function() {
                    time++;
                }, 1000);
                timer = setInterval(function() {
                    updateTime(response.id);
                }, response.frequency * 1000);
            }).fail(Notification.exception);
    }
    return {
        init: function() {
            PROMISES.IS_INSTALLED()
                .done(function(response) {
                    if (response.installed) {
                        init();
                    }
                });
        }
    };
});
require(['local_edwiserreports/tracker'], function(tracker) {
    $(document).ready(function() {
        tracker.init();
    });
});