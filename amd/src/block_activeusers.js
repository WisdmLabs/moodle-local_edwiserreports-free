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
/* eslint-disable no-unused-vars */
/* eslint-disable no-console */
define([
    'jquery',
    './chart/apexcharts',
    'core/notification',
    './defaultconfig',
    './common',
    './flatpickr'
], function($, ApexCharts, Notification, CFG, common) {
    /* Varible for active users block */
    var activeUsersData = null;
    var activeUsersGraph = null;
    var panelBody = CFG.getPanel("#activeusersblock", "body");
    var panelFooter = CFG.getPanel("#activeusersblock", "footer");
    var chart = panelBody + " .ct-chart";
    var loader = panelBody + " .loader";
    var refreshBtn = panelBody + " .refresh";
    var filter = 'last7days';
    var timer = null;

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
            custom: function({ series, seriesIndex, dataPointIndex, w }) {
                let tooltip = `
                <div class="apexcharts-tooltip-title" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                    ${common.formatDate(new Date(w.config.xaxis.categories[dataPointIndex]), "d MMM yyyy")}
                </div>`;
                for (let index = 0; index < w.config.series.length; index++) {
                    const element = w.config.series[index];
                    if (element.data.length == 0) {
                        continue;
                    }
                    tooltip += `<div class="apexcharts-tooltip-series-group apexcharts-active" style="order: ${index}; display: flex;">
                        <span class="apexcharts-tooltip-marker" style="background-color: ${w.config.colors[index]};"></span>
                        <div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                            <div class="apexcharts-tooltip-y-group">
                                <span class="apexcharts-tooltip-text-y-label">${w.config.series[index].name}: </span>
                                <span class="apexcharts-tooltip-text-y-value">${w.config.series[index].data[dataPointIndex]}</span>
                            </div>
                        </div>
                    </div>`;
                }
                return tooltip + `
                    <span style="color: black; font-size: 0.871rem; order: 4; padding: 0px 15px;">${M.util.get_string('clickondatapoint', 'local_edwiserreports')}</span>
                `;
            }
        },
        stroke: {
            curve: 'smooth',
            width: 2,
            lineCap: 'round'
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
        yaxis: {
            labels: {
                formatter: function(val, index) {
                    return val === undefined ? val : val.toFixed(0);
                }
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
        colors: CFG.getColorTheme(),
        dataLabels: {
            enabled: false
        }
    };

    /**
     * Initialize
     * @param {function} invalidUser Callback function
     */
    function init(invalidUser) {

        /* Custom Dropdown hide and show */
        activeUsersData = CFG.getActiveUsersBlock();

        // If course progress block is there
        if (activeUsersData) {

            /* Refresh when click on the refresh button */
            $(refreshBtn).on('click', function() {
                $(this).addClass("refresh-spin");
                getActiveUsersBlockData();
            });

            // Date selector listener.
            common.dateChange(function(date) {
                filter = date;

                // Set export filter to download link.
                $('#activeusersblock').find('.download-links [name="filter"]').val(filter);
                getActiveUsersBlockData();
            });

            /* Call function to initialize the active users block graph */
            getActiveUsersBlockData();
        }

        /**
         * Get data for active users block.
         */
        function getActiveUsersBlockData() {
            $(chart).hide();
            $(loader).show();

            // Show loader.
            common.loader.show('#activeusersblock');
            $.ajax({
                url: CFG.requestUrl,
                type: CFG.requestType,
                dataType: CFG.requestDataType,
                data: {
                    action: 'get_activeusers_graph_data_ajax',
                    secret: M.local_edwiserreports.secret,
                    lang: $('html').attr('lang'),
                    data: JSON.stringify({
                        precalculated: ['weekly', 'monthly', 'yearly'].indexOf(filter) !== -1,
                        filter: filter,
                        graphajax: true
                    })
                },
            }).done(function(response) {
                if (response.error === true && response.exception.errorcode === 'invalidsecretkey') {
                    invalidUser('activeusersblock', response);
                    return;
                }
                activeUsersData.graph.data = response.data;
                activeUsersData.graph.labels = response.dates.map(date => date * 86400000);
                common.insight('#activeusersblock .insight', response.insight);
            }).fail(function(error) {
                Notification.exception(error);
            }).always(function() {
                activeUsersGraph = generateActiveUsersGraph();
                // V.changeExportUrl(filter, exportUrlLink, V.filterReplaceFlag);
                $(panelFooter).find('.download-links input[name="filter"]').val(filter);

                // Change graph variables
                resetUpdateTime();
                clearInterval(timer);
                timer = setInterval(inceamentUpdateTime, 1000 * 60);
                $(refreshBtn).removeClass('refresh-spin');
                $(loader).hide();
                $(chart).fadeIn("slow");

                // Hide loader.
                common.loader.hide('#activeusersblock');
            });
        }

        /**
         * Reset Update time in panel header.
         */
        function resetUpdateTime() {
            $(panelBody + " #updated-time > span.minute").html(0);
        }

        /**
         * Increament update time in panel header.
         */
        function inceamentUpdateTime() {
            $(panelBody + " #updated-time > span.minute")
                .html(parseInt($(panelBody + " #updated-time > span.minute").text()) + 1);
        }

        /**
         * Generate Active Users graph.
         * @returns {Object} Active users graph
         */
        function generateActiveUsersGraph() {
            if (activeUsersGraph) {
                activeUsersGraph.destroy();
            }
            activeUsersGraph = new ApexCharts($("#apex-chart-active-users").get(0), getGraphData());
            activeUsersGraph.render();
            return activeUsersGraph;
        }

        /**
         * Get graph data.
         * @return {Object}
         */
        function getGraphData() {
            let data = Object.assign({}, lineChartDefault);
            try {
                data.series = [{
                    name: activeUsersData.graph.labelName.activeUsers,
                    data: activeUsersData.graph.data.activeUsers,
                }, {
                    name: activeUsersData.graph.labelName.enrolments,
                    data: activeUsersData.graph.data.enrolments,
                }, {
                    name: activeUsersData.graph.labelName.completionRate,
                    data: activeUsersData.graph.data.completionRate,
                }];
                data.xaxis.categories = activeUsersData.graph.labels;
                data.chart.toolbar.show = activeUsersData.graph.labels.length > 30;
                data.chart.zoom.enabled = activeUsersData.graph.labels.length > 30;
            } catch (error) {
                data.series = [];
                data.xaxis.categories = [];
                data.chart.toolbar.show = false;
                data.chart.zoom.enabled = false;
            }
            return data;
        }
    }

    // Must return the init function
    return {
        init: init
    };
});
