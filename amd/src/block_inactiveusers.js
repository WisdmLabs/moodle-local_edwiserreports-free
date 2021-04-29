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
    'local_edwiserreports/jquery.dataTables',
    'local_edwiserreports/dataTables.bootstrap4'
], function(
    $,
    Chart,
    cfg,
    V,
    common
) {

    /**
     * Initialize
     * @param {function} notifyListner Callback function
     */
    function init(notifyListner) {
        var activeUsersTable;
        var panel = cfg.getPanel("#inactiveusersblock");
        var panelBody = cfg.getPanel("#inactiveusersblock", "body");
        var panelTitle = cfg.getPanel("#inactiveusersblock", "title");
        var table = panelBody + " #inactiveuserstable";
        var tableWrapper = panelBody + " #inactiveuserstable_wrapper";
        var loader = panelBody + " .loader";
        var dropdown = panelTitle + " .dropdown-menu .dropdown-item";
        var dropdownToggle = panelTitle + " button.dropdown-toggle";
        var inActiveUsersTable = null;
        var exportUrlLink = panel + " .dropdown-menu[aria-labelledby='export-dropdown'] .dropdown-item";

        if ($(panel).length) {
            // Get inactive users data on load
            getInactiveUsersData($(dropdown).data("value"));

            /**
             * On click of dropdown get inactive user list based on filter
             */
            $(dropdown).on("click", function() {
                // Get filter
                var filter = $(this).data("value");
                $(panel).find('.download-links input[name="filter"]').val(filter);

                // If table is already created then destroy the tablw
                if (activeUsersTable) {
                    activeUsersTable.destroy();
                }

                // Show load and remove table
                $(loader).show();
                $(table).hide();
                $(tableWrapper).hide();

                // Set dropdown button value
                $(dropdownToggle).html($(this).html());

                // Change export data url
                cfg.changeExportUrl(filter, exportUrlLink, V.filterReplaceFlag);

                // Get inactive users
                getInactiveUsersData($(this).data("value"));
            });
        } else {
            notifyListner("inActiveUsers");
        }

        /**
         * Get inactive users list based on filter
         * @param  {string} filter Filter
         */
        function getInactiveUsersData(filter) {

            // Show loader.
            common.loader.show('#inactiveusersblock');

            $.ajax({
                url: cfg.requestUrl,
                type: 'GET',
                dataType: 'json',
                data: {
                    action: 'get_inactiveusers_data_ajax',
                    sesskey: $(panel).data("sesskey"),
                    data: JSON.stringify({
                        filter: filter
                    })
                },
            })
                .done(function(response) {
                    createInactiveUsersTable(response.data);
                })
                .fail(function(error) {
                    console.log(error);
                }).always(function() {
                    notifyListner("inActiveUsers");

                    // Hide loader.
                    common.loader.hide('#inactiveusersblock');
                });
        }

        /**
         * Create inactive users table
         * @param  {String} data Table data
         */
        function createInactiveUsersTable(data) {
            // If table is creted then destroy table
            if (inActiveUsersTable) {
                // Remove table data first
                $("#inactiveuserstable tbody").remove();
                inActiveUsersTable.destroy();
            }

            // Display loader
            $(loader).hide();
            $(table).show();

            // Create inactive users table
            inActiveUsersTable = $(table).DataTable({
                data: data,
                // Dom : '<"pull-left"f><t>',
                aaSorting: [[2, 'desc']],
                oLanguage: {
                    sEmptyTable: "No inactive users are available.",
                    sSearchPlaceholder: "Search Users"
                },
                columnDefs: [
                    {
                        "targets": 2,
                        "className": "text-center"
                    }
                ],
                drawCallback: function() {
                    $('.dataTables_paginate > .pagination').addClass('pagination-sm pull-right');
                    $('.dataTables_filter').addClass('pagination-sm pull-right');
                },
                responsive: true,
                lengthChange: false,
                bInfo: false
            });
        }
    }

    // Must return the init function
    return {
        init: init
    };
});
