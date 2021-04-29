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
    './common',
    'local_edwiserreports/flatpickr'
], function($, Chart, defaultConfig, V, common) {
    /* Varible for active users block */
    var cfg = null;
    var activeUsersGraph = null;
    var panel = defaultConfig.getPanel("#activeusersblock");
    var panelBody = defaultConfig.getPanel("#activeusersblock", "body");
    var panelTitle = defaultConfig.getPanel("#activeusersblock", "title");
    var panelFooter = defaultConfig.getPanel("#activeusersblock", "footer");
    var dropdownMenu = panel + " .dropdown-menu[aria-labelledby='filter-dropdown']:not('custom')";
    var dropdownItem = dropdownMenu + " .dropdown-item";
    var dropdownToggle = panel + " #filter-dropdown.dropdown-toggle";
    var flatpickrCalender = panel + " #flatpickrCalender";
    var chart = panelBody + " .ct-chart";
    var loader = panelBody + " .loader";
    var dropdownButton = panel + " button#filter-dropdown";
    var refreshBtn = panelTitle + " .refresh";
    var exportUrlLink = panel + V.exportUrlLink;
    var filter = null;
    var dropdownInput = panelTitle + " input.form-control.input";
    var listner = null;

    /**
     * Initialize
     * @param {Function} notifyListner Callback function
     */
    function init(notifyListner) {
        listner = notifyListner;

        /* Custom Dropdown hide and show */
        $(document).ready(function() {
            cfg = defaultConfig.getActiveUsersBlock();

            // If course progress block is there
            if (cfg) {
                /* Show custom dropdown */
                $(dropdownToggle).on("click", function() {
                    $(dropdownMenu).addClass("show");
                });

                /* Added Custom Value in Dropdown */
                $(dropdownInput).ready(function() {
                    var placeholder = $(dropdownInput).attr("placeholder");
                    $(dropdownInput).val(placeholder);
                });

                /* Hide dropdown when click anywhere in the screen */
                $(document).click(function(e) {
                    if (!($(e.target).hasClass("dropdown-menu") ||
                        $(e.target).parents(".dropdown-menu").length)) {
                        $(dropdownMenu).removeClass('show');
                    }
                });

                /* Select filter for active users block */
                $(dropdownItem + ":not(.custom)").on('click', function() {
                    filter = $(this).attr('value');
                    $(dropdownMenu).removeClass('show');
                    $(dropdownButton).html($(this).text());
                    getActiveUsersBlockData(filter);
                    $(flatpickrCalender).val("Custom");
                    $(dropdownInput).val("Custom");
                });

                /* Refresh when click on the refresh button */
                $(refreshBtn).on('click', function() {
                    $(this).addClass("refresh-spin");
                    getActiveUsersBlockData(filter);
                });

                createDropdownCalendar();
            } else {
                /* Notify that this event is completed */
                listner("activeUsers");
            }
        });

        /**
         * Create Calender in dropdown tp select range.
         */
        function createDropdownCalendar() {
            /* Call function to initialize the active users block graph */
            activeUsersGraph = getActiveUsersBlockData();

            $(flatpickrCalender).flatpickr({
                mode: 'range',
                altInput: true,
                altFormat: "d/m/Y",
                dateFormat: "Y-m-d",
                maxDate: "today",
                appendTo: document.getElementById("activeUser-calendar"),
                onOpen: function() {
                    $(dropdownMenu).addClass('withcalendar');
                },
                onClose: function() {
                    $(dropdownMenu).removeClass('withcalendar');
                    $(dropdownMenu).removeClass('show');
                    selectedCustomDate();
                }
            });
        }

        /**
         * After Select Custom date get active users details.
         */
        function selectedCustomDate() {
            filter = $(flatpickrCalender).val();
            var date = $(dropdownInput).val();

            /* If correct date is not selected then return false */
            if (!filter.includes("to")) {
                return;
            }

            defaultConfig.changeExportUrl(filter, exportUrlLink, V.filterReplaceFlag);
            $(dropdownButton).html(date);
            $(flatpickrCalender).val("");
            getActiveUsersBlockData(filter);
        }

        /**
         * Get data for active users block.
         * @param {String} filter Filter string
         */
        function getActiveUsersBlockData(filter) {
            $(chart).hide();
            $(loader).show();

            /* If filter is not set then select all */
            if (!filter) {
                filter = "weekly";
            }

            // Show loader.
            common.loader.show('#activeusersblock');

            $.ajax({
                url: defaultConfig.requestUrl,
                data: {
                    action: 'get_activeusers_graph_data_ajax',
                    sesskey: $(panel).data("sesskey"),
                    data: JSON.stringify({
                        filter: filter
                    })
                },
            }).done(function(response) {
                response = JSON.parse(response);
                cfg.graph.data = response.data;
                cfg.graph.labels = response.labels;
            }).fail(function(error) {
                console.log(error);
            }).always(function() {
                activeUsersGraph = generateActiveUsersGraph();
                // V.changeExportUrl(filter, exportUrlLink, V.filterReplaceFlag);
                $(panelFooter).find('.download-links input[name="filter"]').val(filter);

                // Change graph variables
                resetUpdateTime();
                setInterval(inceamentUpdateTime, 1000 * 60);
                $(refreshBtn).removeClass('refresh-spin');
                $(loader).hide();
                $(chart).fadeIn("slow");

                /* Notify that this event is completed */
                listner("activeUsers");

                // Hide loader.
                common.loader.hide('#activeusersblock');
            });
        }

        /**
         * Reset Update time in panel header.
         */
        function resetUpdateTime() {
            $(panelTitle + " #updated-time > span.minute").html(0);
        }

        /**
         * Increament update time in panel header.
         */
        function inceamentUpdateTime() {
            $(panelTitle + " #updated-time > span.minute")
            .html(parseInt($(panelTitle + " #updated-time > span.minute").text()) + 1);
        }

        /**
         * Generate Active Users graph.
         * @returns {Object} Active users graph
         */
        function generateActiveUsersGraph() {
            if (activeUsersGraph) {
                activeUsersGraph.destroy();
            }

            Chart.defaults.global.defaultFontFamily = cfg.graph.fontFamily;
            Chart.defaults.global.defaultFontStyle = cfg.graph.fontStyle;
            activeUsersGraph = new Chart(cfg.ctx, {
                type: cfg.graph.type,
                data: getGraphData(),
                options: cfg.graph.options
            });
            return activeUsersGraph;
        }

        /**
         * Get graph data.
         * @return {Object}
         */
        function getGraphData() {
            return {
                labels: cfg.graph.labels,
                datasets: [{
                    label: cfg.graph.labelName.activeUsers,
                    data: cfg.graph.data.activeUsers,
                    backgroundColor: cfg.graph.backgroundColor.activeUsers,
                    borderColor: cfg.graph.borderColor.activeUsers,
                    pointBorderColor: cfg.graph.borderColor.activeUsers,
                    pointBackgroundColor: cfg.graph.borderColor.activeUsers,
                    pointStyle: cfg.graph.pointStyle
                },
                {
                    label: cfg.graph.labelName.enrolments,
                    data: cfg.graph.data.enrolments,
                    backgroundColor: cfg.graph.backgroundColor.enrolments,
                    borderColor: cfg.graph.borderColor.enrolments,
                    pointBorderColor: cfg.graph.borderColor.enrolments,
                    pointBackgroundColor: cfg.graph.borderColor.enrolments,
                    pointStyle: cfg.graph.pointStyle
                },
                {
                    label: cfg.graph.labelName.completionRate,
                    data: cfg.graph.data.completionRate,
                    backgroundColor: cfg.graph.backgroundColor.completionRate,
                    borderColor: cfg.graph.borderColor.completionRate,
                    pointBorderColor: cfg.graph.borderColor.completionRate,
                    pointBackgroundColor: cfg.graph.borderColor.completionRate,
                    pointStyle: cfg.graph.pointStyle
                }]
            };
        }
    }

    // Must return the init function
    return {
        init: init
    };
});
