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
    './common',
    './defaultconfig',
    './select2'
], function(
    $,
    Notification,
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
        date: 'weekly'
    };

    /**
     * Line chart default config.
     */
    const lineChartDefault = {
        series: [],
        chart: {
            type: 'line',
            height: 350,
            dropShadow: {
                enabled: true,
                color: '#000',
                top: 18,
                left: 7,
                blur: 10,
                opacity: 0.2
            },
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
        markers: {
            size: 0
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
        stroke: {
            curve: 'smooth',
            width: 2
        },
        grid: {
            borderColor: '#e7e7e7'
        },
        xaxis: {
            categories: null,
            type: 'datetime',
            labels: {
                hideOverlappingLabels: true,
                datetimeFormatter: {
                    year: 'yyyy',
                    month: 'MMM \'yy',
                    day: 'dd MMM',
                    hour: ''
                }
            },
            tooltip: {
                enabled: false
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
        dataLabels: {
            enabled: false
        },
        colors: [CFG.getColorTheme()[2]]
    };

    /**
     * Selectors list.
     */
    var SELECTOR = {
        PANEL: '#learnertimespentonsiteblock',
        GRAPH: '#apex-chart-learnertimespentonsite-block'
    };

    /**
     * All promises.
     */
    var PROMISE = {
        /**
         * Get timespent on site using filters.
         * @returns {PROMISE}
         */
        GET_TIMESPENTONSITE: function() {
            return $.ajax({
                url: CFG.requestUrl,
                type: CFG.requestType,
                dataType: CFG.requestDataType,
                data: {
                    action: 'get_learnertimespentonsite_graph_data_ajax',
                    secret: M.local_edwiserreports.secret,
                    lang: $('html').attr('lang')
                },
            });
        },
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
        PROMISE.GET_TIMESPENTONSITE(filter)
            .done(function(response) {
                data = Object.assign({}, lineChartDefault);
                data.yaxis = {
                    labels: {
                        formatter: Common.timeFormatter
                    }
                };
                data.xaxis.categories = response.labels;
                data.series = [{
                    name: M.util.get_string('timespentonlms', 'local_edwiserreports'),
                    data: response.timespent,
                }];
                data.chart.toolbar.show = response.labels.length > 30;
                data.chart.zoom.enabled = response.labels.length > 30;
                data.tooltip.y.title.formatter = (title) => {
                    return M.util.get_string('time', 'local_edwiserreports') + ': ';
                };
                $(SELECTOR.PANEL).find('.panel-body').attr('data-charttype', 'line');
                renderGraph($(SELECTOR.PANEL).find(SELECTOR.GRAPH), data);
                Common.loader.hide(SELECTOR.PANEL);
            }).fail(function(exception) {
                Notification.exception(exception);
                Common.loader.hide(SELECTOR.PANEL);
            });
    }

    /**
     * Initialize event listeners.
     */
    function initEvents() {

        $(SELECTOR.PANEL).find(SELECTOR.DATEPICKERINPUT).flatpickr({
            mode: 'range',
            altInput: true,
            altFormat: "d/m/Y",
            dateFormat: "Y-m-d",
            maxDate: "today",
            appendTo: $(SELECTOR.PANEL).find(SELECTOR.DATEPICKER).get(0)
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

        initEvents();

        loadGraph();
    }
    return {
        init: init
    };
});
