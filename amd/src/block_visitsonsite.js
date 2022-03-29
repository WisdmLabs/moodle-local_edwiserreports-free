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
 * @package     local_edwiserreports
 * @copyright   2021 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/* eslint-disable no-console */
define([
    'jquery',
    './chart/apexcharts',
    './common',
    './defaultconfig'
], function(
    $,
    ApexCharts,
    common,
    CFG
) {

    /**
     * DOM element selectors list.
     */
    let SELECTOR = {
        PANEL: '#visitsonsiteblock',
        INSIGHT: '#visitsonsiteblock .insight',
        GRAPH: '#apex-chart-visitsonsite-block',
    };

    let PROMISE = {
        /**
         * Get visits on site.
         * @returns {PROMISE}
         */
        GET_VISITSONSITE: function() {
            return $.ajax({
                url: CFG.requestUrl,
                type: CFG.requestType,
                dataType: CFG.requestDataType,
                data: {
                    action: 'get_visitsonsite_graph_data_ajax',
                    secret: M.local_edwiserreports.secret
                },
            });
        }
    };

    /**
     * Chart object.
     */
    let chart = null;

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
            y: {
                formatter: undefined,
                title: {},
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
        colors: [CFG.getColorTheme()[2]],
        noData: {
            text: M.util.get_string('nographdata', 'local_edwiserreports')
        }
    };

    /**
     * Load graph
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

        PROMISE.GET_VISITSONSITE()
            .done(function(response) {
                if (response.error === true && response.exception.errorcode === 'invalidsecretkey') {
                    invalidUser('visitsonsiteblock', response);
                    return;
                }
                let data = Object.assign({}, lineChartDefault);
                data.series = [{
                    name: M.util.get_string('visitsonlms', 'local_edwiserreports'),
                    data: response.visits,
                }];
                data.xaxis.categories = response.labels;
                data.chart.toolbar.show = response.labels.length > 30;
                data.chart.zoom.enabled = response.labels.length > 30;
                data.tooltip.y.title.formatter = () => {
                    return M.util.get_string('visits', 'local_edwiserreports') + ': ';
                }
                common.insight(SELECTOR.INSIGHT, response.insight);
                renderGraph($(SELECTOR.PANEL).find(SELECTOR.GRAPH), data);
            }).fail(function(exception) {
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

        loadGraph(invalidUser);

        $(SELECTOR.PANEL).find('.singleselect').select2();
    }

    // Must return the init function
    return {
        init: init
    };
});
