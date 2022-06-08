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
    './chart/apexcharts',
    './common',
    './defaultconfig',
    './select2'
], function(
    $,
    ApexCharts,
    Common,
    CFG
) {
    /**
     * Charts list.
     */
    var chart = null;

    /**
     * Filter for ajax.
     */
    var filter = {
        course: 0
    };

    /**
     * Bar chart default config.
     */
    const barChartDefault = {
        series: [],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false,
                tools: {
                    download: false,
                    reset: '<i class="fa fa-refresh"></i>'
                }
            },
            zoom: {
                enabled: false
            }
        },
        tooltip: {
            enabled: true,
            enabledOnSeries: undefined,
            shared: true,
            followCursor: false,
            intersect: false,
            inverseOrder: false,
            fillSeriesColor: false,
            onDatasetHover: {
                highlightDataSeries: false,
            },
            marker: {
                show: true
            },
            items: {
                display: 'flex'
            },
            fixed: {
                enabled: false,
                position: 'topRight',
                offsetX: 0,
                offsetY: 0,
            },
            y: {
                title: {}
            }
        },
        grid: {
            borderColor: '#e7e7e7'
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            categories: null,
            labels: {
                hideOverlappingLabels: true,
                trim: true,
                rotate: 300
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'left',
            offsetY: '-20',
            itemMargin: {
                horizontal: 10,
                vertical: 0
            },
        },
        noData: {
            text: M.util.get_string('nographdata', 'local_edwiserreports')
        },
        colors: [CFG.getColorTheme()[2]]
    };

    /**
     * Selectors list.
     */
    var SELECTOR = {
        PANEL: '#learnercourseprogressblock',
        GRAPH: '#apex-chart-learnercourseprogress-block'
    };

    /**
     * All promises.
     */
    var PROMISE = {
        /**
         * Get course progress using filters.
         * @param {Object} filter Filter data
         * @returns {PROMISE}
         */
        GET_COURSEPROGRESS: function(filter) {
            return $.ajax({
                url: CFG.requestUrl,
                type: CFG.requestType,
                dataType: CFG.requestDataType,
                data: {
                    action: 'get_learnercourseprogress_graph_data_ajax',
                    secret: M.local_edwiserreports.secret,
                    lang: $('html').attr('lang'),
                    data: JSON.stringify({
                        filter: filter
                    })
                },
            });
        }
    };

    /**
     * Load graph
     */
    function loadGraph() {
        let data;
        Common.loader.show(SELECTOR.PANEL);

        /**
         * Render graph.
         * @param {DOM} graph Graph element
         * @param {Object} data Graph data
         */
        function renderGraph(graph, data) {
            if (chart !== null) {
                chart.destroy();
            }
            chart = new ApexCharts(graph.get(0), data);
            chart.render();
            setTimeout(function() {
                Common.loader.hide(SELECTOR.PANEL);
            }, 1000);
        }

        PROMISE.GET_COURSEPROGRESS(filter)
            .done(function(response) {
                data = Object.assign({}, barChartDefault);
                data.yaxis = {
                    max: 100,
                    labels: {
                        formatter: (progress) => {
                            return progress + '%';
                        }
                    }
                };
                data.xaxis.categories = response.labels;
                data.series = [{
                    name: M.util.get_string('courseprogress', 'local_edwiserreports'),
                    data: response.progress,
                }];
                data.chart.toolbar.show = response.labels.length > 30;
                data.chart.zoom.enabled = response.labels.length > 30;
                data.tooltip.y.title.formatter = (title) => {
                    return M.util.get_string('progress', 'local_edwiserreports') + ': ';
                };

                renderGraph($(SELECTOR.PANEL).find(SELECTOR.GRAPH), data);
            }).fail(function(exception) {
                Notification.exception(exception);
                Common.loader.hide(SELECTOR.PANEL);
            });
    }

    /**
     * Initialize
     * @param {function} invalidUser Callback function
     */
    function init(invalidUser) {
        if ($(SELECTOR.PANEL).length == 0) {
            return;
        }
        $(SELECTOR.PANEL).find('.singleselect').select2();

        loadGraph();
    }
    return {
        init: init
    };
});
