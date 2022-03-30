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
    './defaultconfig',
    './variables',
    './common',
    './jquery.dataTables',
    './dataTables.bootstrap4'
], function(
    $,
    cfg,
    V,
    common
) {

    /**
     * Initialize
     * @param {function} invalidUser Callback function
     */
    function init(invalidUser) {
        var panel = cfg.getPanel("#inactiveusersblock");
        var panelBody = cfg.getPanel("#inactiveusersblock", "body");
        var panelTitle = cfg.getPanel("#inactiveusersblock", "title");
        var table = panelBody + " #inactiveuserstable";
        var tableWrapper = panelBody + " #inactiveuserstable_wrapper";
        var loader = panelBody + " .loader";
        var dropdown = panelBody + " .dropdown-menu .dropdown-item";
        var dropdownToggle = panelBody + " button.dropdown-toggle";
        var inActiveUsersTable = null;
        var exportUrlLink = panel + " .dropdown-menu[aria-labelledby='export-dropdown'] .dropdown-item";
        var searchTable = panel + " .table-search-input input";

        if ($(panel).length) {
            // Rending data table.
            inActiveUsersTable = $(table).DataTable({
                dom: '<"edwiserreports-table"<t><"table-pagination"p>>',
                aaSorting: [
                    [2, 'desc']
                ],
                oLanguage: {
                    sEmptyTable: "No inactive users are available.",
                    sSearchPlaceholder: "Search Users"
                },
                columnDefs: [{
                    "targets": 1,
                    "className": "d-none d-sm-none d-md-table-cell d-lg-table-cell "
                }, {
                    "targets": 2,
                    "className": "text-center"
                }],
                drawCallback: function() {
                    common.stylePaginationButton(this);
                    // Hide loader.
                    common.loader.hide('#inactiveusersblock');
                },
                responsive: true,
                lengthChange: false,
                bInfo: false
            });

            // Get inactive users data on load
            getInactiveUsersData($(dropdown).data("value"));

            /**
             * On click of dropdown get inactive user list based on filter
             */
            $(dropdown).on("click", function() {
                // Get filter
                var filter = $(this).data("value");
                $(panel).find('.download-links input[name="filter"]').val(filter);

                // Set dropdown button value
                $(dropdownToggle).html($(this).html());

                // Change export data url
                cfg.changeExportUrl(filter, exportUrlLink, V.filterReplaceFlag);

                // Get inactive users
                getInactiveUsersData($(this).data("value"));
            });

            // Search in table.
            $('body').on('input', searchTable, function() {
                inActiveUsersTable.search(this.value).draw();
            });
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
                    type: cfg.requestType,
                    dataType: cfg.requestDataType,
                    data: {
                        action: 'get_inactiveusers_data_ajax',
                        secret: M.local_edwiserreports.secret,
                        data: JSON.stringify({
                            filter: filter
                        })
                    },
                })
                .done(function(response) {
                    if (response.error === true && response.exception.errorcode === 'invalidsecretkey') {
                        invalidUser('inactiveusersblock', response);
                        return;
                    }
                    inActiveUsersTable.clear();
                    inActiveUsersTable.rows.add(response.data);
                    inActiveUsersTable.draw();
                })
                .fail(function(error) {
                    // console.log(error);
                }).always(function() {
                    // Hide loader.
                    common.loader.hide('#inactiveusersblock');
                });
        }
    }

    // Must return the init function
    return {
        init: init
    };
});
