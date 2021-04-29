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
    'local_edwiserreports/variables',
    'local_edwiserreports/select2'
], function($, Chart, cfg, V) {
    /**
     * Initialize
     * @param {function} notifyListner Callback function
     */
    function init(notifyListner) {
        var lpChart = null;
        var panel = cfg.getPanel("#lpstatsblock");
        var panelBody = cfg.getPanel("#lpstatsblock", "body");
        var selectedLp = panelBody + " #wdm-lpstats-select";
        var exportUrlLink = panel + " .dropdown-menu[aria-labelledby='export-dropdown'] .dropdown-item";
        var chart = panelBody + " .ct-chart";
        var loader = panelBody + " .loader";
        var lpStatsBlock = false;

        $(document).ready(function() {
            var lpId = $(selectedLp).val();
            lpStatsBlock = cfg.getLpStatsBlock();

            if (lpStatsBlock) {
                getLpStatsData(lpId);
                $(panelBody + ' .singleselect').select2();

                $(selectedLp).on("change", function() {
                    lpId = $(this).val();
                    $(chart).addClass("d-none");
                    $(loader).removeClass("d-none");
                    cfg.changeExportUrl(lpId, exportUrlLink, V.filterReplaceFlag);
                    lpChart.destroy();
                    getLpStatsData(lpId);
                });
            } else {
                notifyListner("lpStatsBlock");
            }
        });

        /**
         * Get learning plan stats
         * @param {Integer} lpId Learning plan id
         */
        function getLpStatsData(lpId) {
            $.ajax({
                url: cfg.requestUrl,
                type: cfg.requestType,
                dataType: cfg.requestDataType,
                sesskey: $(panel).data("sesskey"),
                data: {
                    action: 'get_lpstats_data_ajax',
                    sesskey: $(panel).data("sesskey"),
                    data: JSON.stringify({
                        lpid: lpId
                    })
                },
            })
                .done(function(response) {
                    generateLpChart(response.data);
                })
                .fail(function(error) {
                    console.log(error);
                })
                .always(function() {
                    $(loader).addClass("d-none");
                    $(chart).removeClass("d-none");
                    notifyListner("lpStatsBlock");
                });
        }

        /**
         * Generate learning plan chart.
         * @param {Object} responsedata Generate chart response data.
         */
        function generateLpChart(responsedata) {
            if (lpStatsBlock) {
                lpStatsBlock.graph.labels = responsedata.labels;
                lpStatsBlock.graph.data = responsedata.data;

                var data = {
                    labels: lpStatsBlock.graph.labels,
                    datasets: [{
                        data: lpStatsBlock.graph.data,
                        backgroundColor: lpStatsBlock.graph.backgroundColor,
                    }]
                };

                lpChart = new Chart(lpStatsBlock.ctx, {
                    data: data,
                    type: lpStatsBlock.graph.type,
                    options: lpStatsBlock.graph.options
                });
            }
        }
    }

    // Must return the init function
    return {
        init: init
    };
});
