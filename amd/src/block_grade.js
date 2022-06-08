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
 * Grade table js.
 *
 * @copyright   2021 wisdmlabs <support@wisdmlabs.com>
 * @author      Yogesh Shirsath
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/* eslint-disable no-unused-vars */
define([
    'jquery',
    'core/ajax',
    'core/templates',
    './chart/apexcharts',
    './common',
    './defaultconfig',
    './select2'
], function(
    $,
    Ajax,
    Templates,
    ApexCharts,
    common,
    CFG
) {

    /**
     * Chart.
     */
    var chart = null;

    /**
     * Default position.
     */
    var position = 'right';

    /**
     * Pie chart default config.
     */
    const pieChartDefault = {
        chart: {
            type: 'donut',
            height: 350
        },
        fill: {
            type: 'solid',
        },
        legend: {
            position: position,
            formatter: function(seriesName, opts) {
                return [seriesName + ": " + opts.w.globals.series[opts.seriesIndex]];
            }
        },
        colors: CFG.getColorTheme(),
        dataLabels: {
            enabled: false
        },
        noData: {
            text: M.util.get_string('nographdata', 'local_edwiserreports')
        }
    };

    /**
     * Selectors list.
     */
    var SELECTOR = {
        PANEL: '#gradeblock',
        GRAPH: '.graph',
        GRAPHLABEL: '.graph-label',
    };

    /**
     * All promises.
     */
    var PROMISE = {
        /**
         * Get graph data using filters.
         * @returns {PROMISE}
         */
        GET_GRAPH_DATA: function() {
            return $.ajax({
                url: CFG.requestUrl,
                type: CFG.requestType,
                dataType: CFG.requestDataType,
                data: {
                    action: 'get_grade_graph_data_ajax',
                    secret: M.local_edwiserreports.secret,
                    lang: $('html').attr('lang')
                },
            });
        }
    };

    /**
     * Load graph
     */
    function loadGraph() {
        let data;
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
        PROMISE.GET_GRAPH_DATA()
            .done(function(response) {
                data = Object.assign({}, pieChartDefault);
                data.legend.position = position;
                data.labels = response.labels.reverse();
                data.series = response.grades.reverse();
                $(SELECTOR.PANEL).find(SELECTOR.GRAPH).data('responseTitle', response.header);
                data.responseTitle = response.header;
                data.tooltip = {
                    custom: function({
                        series,
                        seriesIndex,
                        dataPointIndex,
                        w
                    }) {
                        let value = series[seriesIndex];
                        let tooltip = value < 2 ? response.tooltip.single : response.tooltip.plural;
                        let label = w.config.labels[seriesIndex];
                        let color = w.config.colors[seriesIndex];
                        return `<div class="custom-donut-tooltip" style="color: ${color};">
                                <span style="font-weight: 500;"> ${label}:</span>
                                <span style="font-weight: 700;"> ${value} ${M.util.get_string(
                                    tooltip,
                                    'local_edwiserreports'
                                )}</span>
                            </div>`;
                    }
                };
                $(SELECTOR.PANEL).find(SELECTOR.GRAPH).toggleClass('empty-donut', data.labels.length == 0);
                data.chart.events = {
                    mounted: function() {
                        $(SELECTOR.PANEL).find(SELECTOR.GRAPH).find('.apexcharts-legend')
                            .prepend(`<label class="graph-label w-100 text-center">
                            ${$(SELECTOR.PANEL).find(SELECTOR.GRAPH).data('responseTitle')}</label>`);
                    },
                };
                data.chart.events.updated = data.chart.events.mounted;
                common.insight('#gradeblock .insight', {
                    'insight': {
                        'value': response.average + '%',
                        'title': 'averagegrade'
                    }
                });
                renderGraph($(SELECTOR.PANEL).find(SELECTOR.GRAPH), data);
            });
        common.loader.hide(SELECTOR.PANEL);
    }

    /**
     * Initialize event listeners.
     */
    function initEvents() {

        // Handling legend position based on width.
        setInterval(function() {
            if (chart === null) {
                return;
            }
            let width = $(SELECTOR.PANEL).find(SELECTOR.GRAPH).width();
            let newPosition = width >= 400 ? 'right' : 'bottom';
            if (newPosition == position) {
                return;
            }
            position = newPosition;
            chart.updateOptions({
                legend: {
                    position: position
                }
            });
        }, 1000);
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
        initEvents();
        loadGraph();
    }
    return {
        init: init
    };
});
