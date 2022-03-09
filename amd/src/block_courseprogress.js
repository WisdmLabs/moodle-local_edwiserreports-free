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
    'local_edwiserreports/chart/apexcharts',
    'local_edwiserreports/defaultconfig',
    'local_edwiserreports/variables',
    './common',
    'local_edwiserreports/select2'
], function($, ApexCharts, CFG, v, common) {

    /**
     * Initialize
     * @param {function} invalidUser Callback function
     */
    function init(invalidUser) {
        var cpGraph = null;
        var panel = CFG.getPanel("#courseprogressblock");
        var panelBody = CFG.getPanel("#courseprogressblock", "body");
        var selectedCourse = panelBody + " #wdm-courseprogress-select";
        var chart = panelBody + " .ct-chart";
        var loader = panelBody + " .loader";
        var pieChart = {
            type: "pie",
            data: [0, 0, 0, 0, 0, 0],
            options: {
                responsive: true,
                legend: { position: 'bottom' },
                maintainAspectRatio: false,
                aspectRatio: 1,
                tooltips: {
                    callbacks: {
                        title: function(tooltipItem, data) {
                            return [
                                M.util.get_string('cpblocktooltip1',
                                    v.component, {
                                        "per": data.labels[tooltipItem[0].index],
                                    }),
                                M.util.get_string('cpblocktooltip2',
                                    v.component, {
                                        "val": data.datasets[0].data[tooltipItem[0].index]
                                    })
                            ];
                        },
                        label: function() {
                            return '';
                        }
                    }
                }
            },
            labels: [
                '0% - 20%',
                '21% - 40%',
                '41% - 60%',
                '61% - 80%',
                '81% - 100%'
            ],
            backgroundColor: ["#fe6384", "#36a2eb", "#fdce56", "#c70fbe", "#4ac0c0", "#ff851b"]
        };

        var form = $(panel + ' form.download-links');

        if ($(selectedCourse).length == 0) {
            return;
        }

        getCourseProgressData();
        $(panelBody + ' .singleselect').select2();

        $(selectedCourse).on("change", function() {
            $(chart).hide();
            $(loader).show();

            getCourseProgressData();
        });

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
                    pieChart.data = response.data;
                    pieChart.average = response.average == 0 ? 0 : response.average.toPrecision(2);
                })
                .fail(function(error) {
                    // console.log(error);
                    pieChart.average = '0';
                })
                .always(function() {
                    common.insight('#courseprogressblock .insight', {
                        'insight': {
                            'value': pieChart.average + '%',
                            'title': M.util.get_string('averagecourseprogress', 'local_edwiserreports')
                        }
                    });
                    cpGraph = generateCourseProgressGraph();
                    $(loader).hide();
                    $(chart).fadeIn("slow");

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
                series: pieChart.data,
                chart: {
                    type: 'pie',
                    height: 350
                },
                colors: CFG.getColorPalette(),
                fill: {
                    type: 'solid',
                },
                labels: pieChart.labels,
                legend: {
                    position: 'bottom',
                    offsetY: 0
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