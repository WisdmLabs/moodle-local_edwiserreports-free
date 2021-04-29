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
define([
    'jquery',
    'core/chartjs',
    'local_edwiserreports/defaultconfig',
    './common'
], function($, Chart, cfg, common) {
    /**
     * Initialize
     * @param {function} notifyListner Callback function
     */
    function init(notifyListner) {
        // Global data got todays activity block
        var todaysVisits;
        var panel = cfg.getPanel("#todaysactivityblock");
        var panelBody = cfg.getPanel("#todaysactivityblock", "body");
        var flatpickrCalender = panel + " #flatpickrCalender";

        /**
         * On document ready do the bellow stuff
         */
        $(document).ready(function() {
            cfg.todaysActivityBlock = cfg.getTodaysActivityBlock();

            // If course progress block is there
            if (cfg.todaysActivityBlock) {
                getTodaysActivity();
                /**
                 * Generate flatpicker for
                 * date filter in todays activity block
                 */
                $(flatpickrCalender).flatpickr({
                    dateFormat: "d M Y",
                    maxDate: "today",
                    defaultDate: ["today"],
                    // eslint-disable-next-line no-unused-vars
                    onChange: function(selectedDates, dateStr, instance) {
                        // $(panelBody).find("loader").show();
                        getTodaysActivity(dateStr);
                    }
                });
            } else {
                /* Notify that this event is completed */
                notifyListner("todaysActivity");
            }
        });

        /**
         * Get Todays activity information
         * @param  {number} date Unix Date
         */
        function getTodaysActivity(date) {

            // Show loader.
            common.loader.show('#todaysactivityblock');

            // Get session key
            var sesskey = $(panel).data("sesskey");

            // Send Ajax call to get todays activity information
            $.ajax({
                url: cfg.requestUrl,
                type: cfg.requestType,
                dataType: cfg.requestDataType,
                data: {
                    action: 'get_todaysactivity_data_ajax',
                    sesskey: sesskey,
                    data: JSON.stringify({
                        date: date
                    })
                }
            })
                .done(function(response) {
                    /**
                     * After getting todays activity information
                     * update the value in todays activity block
                     */
                    $.each(response.data, function(indx, el) {
                        var section = $(panelBody + " #todays-" + indx);
                        section.find(".data").html(el);
                    });

                    /* Generate Todays Activity Graph */
                    generateTodaysVisitsGraph(response.data.visitshour);
                })
                .fail(function(error) {
                    console.log(error);
                }).always(function() {
                    notifyListner("todaysActivity");

                    // Hide loader.
                    common.loader.hide('#todaysactivityblock');
                });
        }

        /**
         * Generate Todays Activity Graph
         * @param  {object} data Todays activity object
         */
        function generateTodaysVisitsGraph(data) {
            // Prepare data for generating graph
            cfg.todaysActivityBlock.graph.data = data;
            data = {
                labels: cfg.todaysActivityBlock.graph.labels,
                datasets: [{
                    label: cfg.todaysActivityBlock.graph.labelName,
                    data: cfg.todaysActivityBlock.graph.data,
                    backgroundColor: cfg.todaysActivityBlock.graph.backgroundColor
                }]
            };

            /**
             * If Todays Activity graph is already
             * created then destroy
             */
            if (todaysVisits) {
                todaysVisits.destroy();
            }

            // Generate Todays Activity Graph
            todaysVisits = new Chart(cfg.todaysActivityBlock.ctx, {
                type: cfg.todaysActivityBlock.graph.type,
                options: cfg.todaysActivityBlock.graph.options,
                data: data
            });
        }
    }

    // Must return the init function
    return {
        init: init
    };
});
