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
        PANEL: '#siteaccessblock',
        GRAPH: '#apex-chart-siteaccess-block',
        SHADE: '.siteaccess-values-shade'
    };

    /**
     * Promise list.
     */
    let PROMISE = {
        /**
         * Get site access information.
         * @returns {PROMISE}
         */
        GET_VISITSONSITE: function() {
            return $.ajax({
                url: CFG.requestUrl,
                type: CFG.requestType,
                dataType: CFG.requestDataType,
                data: {
                    action: 'get_siteaccess_data_ajax',
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
    const heatmapChart = {
        series: [],
        chart: {
            height: 500,
            type: 'heatmap',
            toolbar: {
                show: false
            }
        },
        xaxis: {
            categories: [
                M.util.get_string('week_0', 'local_edwiserreports'),
                M.util.get_string('week_1', 'local_edwiserreports'),
                M.util.get_string('week_2', 'local_edwiserreports'),
                M.util.get_string('week_3', 'local_edwiserreports'),
                M.util.get_string('week_4', 'local_edwiserreports'),
                M.util.get_string('week_5', 'local_edwiserreports'),
                M.util.get_string('week_6', 'local_edwiserreports')
            ]
        },
        dataLabels: {
            enabled: false
        },
        colors: [CFG.getColorTheme()[2]],
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
                    invalidUser('siteaccessblock', response);
                    return;
                }
                let data = Object.assign({}, heatmapChart);
                data.series = response.data.siteaccess.reverse();
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
        $(SELECTOR.SHADE).css('background-image', 'linear-gradient(to right, rgba(0, 0, 0, 0), ' + CFG.getColorTheme()[2] + ')');
    }

    // Must return the init function
    return {
        init: init
    };
});