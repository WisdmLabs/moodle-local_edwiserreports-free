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
define([
    'jquery',
    'core/notification',
    './chart/apexcharts',
    './defaultconfig',
    './common'
], function($, Notification, ApexCharts, CFG, common) {
    /**
     * Initialize
     * @param {function} invalidUser Callback function
     */
    function init(invalidUser) {
        // Global data got todays activity block
        var todaysVisits;
        var panel = CFG.getPanel("#todaysactivityblock");
        var panelBody = CFG.getPanel("#todaysactivityblock", "body");
        var flatpickrCalender = panel + " #flatpickrCalender-todaysactivity";

        /**
         * On document ready do the bellow stuff
         */
        $(document).ready(function() {
            CFG.todaysActivityBlock = CFG.getTodaysActivityBlock();

            // If course progress block is there
            if (CFG.todaysActivityBlock) {
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
            }
        });

        /**
         * Get Todays activity information
         * @param  {number} date Unix Date
         */
        function getTodaysActivity(date) {

            // Show loader.
            common.loader.show('#todaysactivityblock');

            // Send Ajax call to get todays activity information
            $.ajax({
                    url: CFG.requestUrl,
                    type: CFG.requestType,
                    dataType: CFG.requestDataType,
                    data: {
                        action: 'get_todaysactivity_data_ajax',
                        secret: M.local_edwiserreports.secret,
                        lang: $('html').attr('lang'),
                        data: JSON.stringify({
                            date: date
                        })
                    }
                }).done(function(response) {
                    if (response.error === true && response.exception.errorcode === 'invalidsecretkey') {
                        invalidUser('todaysactivityblock', response);
                        return;
                    }
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
                    Notification.exception(error);
                }).always(function() {
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
            CFG.todaysActivityBlock.graph.data = data;
            data = {
                labels: CFG.todaysActivityBlock.graph.labels,
                datasets: [{
                    label: CFG.todaysActivityBlock.graph.labelName,
                    data: CFG.todaysActivityBlock.graph.data,
                    backgroundColor: CFG.todaysActivityBlock.graph.backgroundColor
                }]
            };

            var options = {
                series: [{
                    data: CFG.todaysActivityBlock.graph.data
                }],
                chart: {
                    type: 'bar',
                    height: 150,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: false,
                        colors: {
                            ranges: [{
                                from: 0,
                                to: 100
                            }]
                        }
                    }
                },
                grid: {
                    show: false
                },
                dataLabels: {
                    enabled: false
                },
                yaxis: {
                    show: false
                },
                tooltip: {
                    theme: 'dark',
                    custom: function({
                        series,
                        seriesIndex,
                        dataPointIndex,
                        w
                    }) {
                        return '<div class="text-center p-1">' +
                            w.config.xaxis.categories[dataPointIndex] +
                            '<br>' +
                            series[seriesIndex][dataPointIndex] +
                            '</div>';
                    }
                },
                xaxis: {
                    categories: CFG.todaysActivityBlock.graph.labels,
                },
                colors: [CFG.getColorTheme()[2]]
            };

            /**
             * If Todays Activity graph is already
             * created then destroy
             */
            if (todaysVisits) {
                todaysVisits.destroy();
            }

            // Generate Todays Activity Graph
            todaysVisits = new ApexCharts($('#apex-chart-todays-activity').get(0), options);
            todaysVisits.render();
        }
    }

    // Must return the init function
    return {
        init: init
    };
});
