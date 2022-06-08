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
 * Block service call and rendering defined in this file.
 *
 * @copyright   2021 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/* eslint-disable no-console */
/* eslint-disable no-unused-vars */
define([
    'jquery',
    'core/notification',
    './chart/apexcharts',
    './common',
    './defaultconfig'
], function(
    $,
    Notification,
    ApexCharts,
    common,
    CFG
) {

    /**
     * DOM element selectors list.
     */
    let SELECTOR = {
        PANEL: '#timespentoncourseblock',
        INSIGHT: '#timespentoncourseblock .insight',
        GRAPH: '#apex-chart-timespentoncourse-block',
    };

    let PROMISE = {
        /**
         * Get timespent on course
         * @returns {PROMISE}
         */
        GET_TIMESPENTONCOURSE: function() {
            return $.ajax({
                url: CFG.requestUrl,
                type: CFG.requestType,
                dataType: CFG.requestDataType,
                data: {
                    action: 'get_timespentoncourse_graph_data_ajax',
                    secret: M.local_edwiserreports.secret,
                    lang: $('html').attr('lang')
                },
            });
        }
    };

    /**
     * Chart object.
     */
    let chart = null;

    /**
     * Bar chart default config.
     */
    const barChartDefault = {
        series: [],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: true,
                tools: {
                    download: false,
                    selection: true,
                    zoom: true,
                    zoomin: true,
                    zoomout: true,
                    pan: true,
                    reset: '<i class="fa fa-refresh"></i>'
                }
            },
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
            y: {
                formatter: undefined,
                title: {},
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
        },
        plotOptions: {
            bar: {
                columnWidth: '50%'
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
                trim: true
            },
            tickPlacement: 'on'
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
        colors: [CFG.getColorTheme()[2]],
        noData: {
            text: M.util.get_string('nographdata', 'local_edwiserreports')
        }
    };

    /**
     * Load graph
     * @param {function} invalidUser Function to be called when user is invalid
     */
    function loadGraph(invalidUser) {
        common.loader.show(SELECTOR.PANEL);

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
                common.loader.hide(SELECTOR.PANEL);
            }, 1000);
        }

        PROMISE.GET_TIMESPENTONCOURSE()
            .done(function(response) {
                let data = Object.assign({}, barChartDefault);

                data.series = [{
                    name: M.util.get_string('timespentoncourse', 'local_edwiserreports'),
                    data: response.timespent,
                }];
                data.xaxis.categories = response.labels;
                data.yaxis = {
                    labels: {
                        formatter: common.timeFormatter
                    }
                };
                data.chart.toolbar.show = response.labels.length > 30;
                data.chart.zoom = {
                    enabled: response.labels.length > 30
                };
                data.tooltip.y.title.formatter = () => {
                    return M.util.get_string('time', 'local_edwiserreports') + ': ';
                };
                response.insight.insight.value = common.timeFormatter(response.insight.insight.value, {
                    dataPointIndex: 0,
                    'short': true
                }).replaceAll(',', ' ');
                response.insight.details.data[0].value = common.timeFormatter(response.insight.details.data[0].value, {
                    dataPointIndex: 0
                });
                common.insight(SELECTOR.INSIGHT, response.insight);
                renderGraph($(SELECTOR.PANEL).find(SELECTOR.GRAPH), data);
            }).fail(function(exception) {
                Notification.exception(exception);
                common.loader.hide(SELECTOR.PANEL);
            });
    }

    /**
     * Initialize
     * @param {function} invalidUser Callback function
     */
    function init(invalidUser) {

        if (!$(SELECTOR.PANEL).length) {
            return;
        }

        $(SELECTOR.PANEL).find('.singleselect').select2();

        loadGraph(invalidUser);
    }

    // Must return the init function
    return {
        init: init
    };
});
