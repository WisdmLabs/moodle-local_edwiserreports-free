/* eslint-disable no-unused-vars */
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
    './common',
    './select2'
], function($, Notification, ApexCharts, CFG, common) {

    /**
     * Initialize
     * @param {function} invalidUser Callback function
     */
    function init(invalidUser) {
        var cpGraph = null;
        var panel = CFG.getPanel("#courseprogressblock");
        var panelBody = CFG.getPanel("#courseprogressblock", "body");
        var selectedCourse = panelBody + " #wdm-courseprogress-select";
        var loader = panelBody + " .loader";
        var position = 'right';
        var donutChart = {
            data: [0, 0, 0, 0, 0, 0],
            labels: [
                '81% - 100%',
                '61% - 80%',
                '41% - 60%',
                '21% - 40%',
                '0% - 20%'
            ]
        };

        var form = $(panel + ' form.download-links');

        if ($(selectedCourse).length == 0) {
            return;
        }

        getCourseProgressData();
        $(panelBody + ' .singleselect').select2();

        $(selectedCourse).on("change", function() {
            $(loader).show();

            getCourseProgressData();
        });

        // Handling legend position based on width.
        setInterval(function() {
            if (cpGraph === null) {
                return;
            }
            let width = $(panel).find('.apexcharts-canvas').width();
            let newPosition = width >= 400 ? 'right' : 'bottom';
            if (newPosition == position) {
                return;
            }
            position = newPosition;
            cpGraph.updateOptions({
                legend: {
                    position: position
                }
            });
        }, 1000);

        /**
         * Get progress data through ajax
         */
        function getCourseProgressData() {
            var courseId = $(selectedCourse).val();
            form.find('input[name="filter"]').val(courseId);

            // If progress graph already exist then destroy
            if (cpGraph) {
                cpGraph.destroy();
            }

            // Show loader.
            common.loader.show('#courseprogressblock');

            $.ajax({
                    url: CFG.requestUrl,
                    type: CFG.requestType,
                    dataType: CFG.requestDataType,
                    data: {
                        action: 'get_courseprogress_graph_data_ajax',
                        secret: M.local_edwiserreports.secret,
                        lang: $('html').attr('lang'),
                        data: JSON.stringify({
                            courseid: courseId
                        })
                    },
                })
                .done(function(response) {
                    if (response.error === true && response.exception.errorcode === 'invalidsecretkey') {
                        invalidUser('courseprogressblock', response);
                        return;
                    }
                    donutChart.data = response.data;
                    donutChart.average = response.average == 0 ? 0 : response.average.toPrecision(2);
                    donutChart.tooltipStrings = response.tooltip;
                    common.insight('#courseprogressblock .insight', response.insight);
                })
                .fail(function(ex) {
                    Notification.exception(ex);
                    donutChart.average = '0';
                })
                .always(function() {
                    cpGraph = generateCourseProgressGraph();
                    $(loader).hide();

                    // Hide loader.
                    common.loader.hide('#courseprogressblock');
                });
        }

        /**
         * Generate course progress graph.
         * @returns {Object} chart object
         */
        function generateCourseProgressGraph() {

            var options = {
                series: donutChart.data.reverse(),
                chart: {
                    type: 'donut',
                    height: 350
                },
                colors: CFG.getColorTheme(),
                fill: {
                    type: 'solid',
                },
                labels: donutChart.labels,
                dataLabels: {
                    enabled: false
                },
                tooltip: {
                    custom: function({
                        series,
                        seriesIndex,
                        dataPointIndex,
                        w
                    }) {
                        let value = series[seriesIndex];
                        let tooltip = value < 2 ? donutChart.tooltipStrings.single : donutChart.tooltipStrings.plural;
                        let label = w.config.labels[seriesIndex];
                        let color = w.config.colors[seriesIndex];
                        return `<div class="custom-donut-tooltip" style="color: ${color};">
                                <span style="font-weight: 500;"> ${label}:</span>
                                <span style="font-weight: 700;"> ${value} ${tooltip}</span>
                            </div>`;
                    }
                },
                legend: {
                    position: position,
                    formatter: function(seriesName, opts) {
                        return [seriesName + ": " + opts.w.globals.series[opts.seriesIndex]];
                    }
                }
            };

            var chart = new ApexCharts($('#apex-chart-course-progress').get(0), options);
            chart.render();

            // Return chart object
            return chart;
        }
    }

    // Must return the init function
    return {
        init: init
    };
});
