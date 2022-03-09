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
define([
    'jquery',
    './common',
    './defaultconfig',
    './select2'
], function(
    $,
    common,
    CFG
) {

    /**
     * Selector
     */
    var SELECTOR = {
        PAGE: '#learner',
        TABLE: '#learner table'
    };

    /**
     * Datatable object.
     */
    var dataTable = null;

    /**
     * All promises.
     */
    var PROMISE = {
        /**
         * Get student engagement table data based on filters.
         * @param {Object} filter Filter data
         * @returns {PROMISE}
         */
        GET_DATA: function(filter) {
            return $.ajax({
                url: CFG.requestUrl,
                type: CFG.requestType,
                dataType: CFG.requestDataType,
                data: {
                    action: 'get_learner_table_data_ajax',
                    secret: M.local_edwiserreports.secret,
                    data: JSON.stringify({
                        filter: filter
                    })
                },
            });
        },
    }

    /**
     * Initialize datable.
     */
    function initializeDatatable() {
        dataTable = $(SELECTOR.TABLE).DataTable({
            paging: true,
            processing: true,
            serverSide: true,
            rowId: 'DT_RowId',
            deferRendering: true,
            scrollY: "400px",
            scrollX: true,
            scrollCollapse: true,
            autoWidth: true,
            ordering: false,
            columns: [
                { data: "fullname" },
                { data: "progress" },
                { data: "activitiescompleted" },
                { data: "timespentoncourse" },
                { data: "grades" }
            ],
            dom: '<"edwiserreports-table"<"table-filter d-flex"fl>i<t><"table-pagination"p>>',
            language: {
                searchPlaceholder: M.util.get_string('searchcourse', 'local_edwiserreports'),
                emptyTable: M.util.get_string('emptytable', 'local_edwiserreports')
            },
            buttons: [],
            drawCallback: function() {
                common.stylePaginationButton(this);
            },
            rowCallback: function(row, data) {
                $('td:eq(3)', row).html(data.timespentoncourse == 0 ? '-' : common.timeFormatter(data.timespentoncourse));
            },
            // eslint-disable-next-line no-unused-vars
            ajax: function(data, callback, settings) {
                common.loader.show(SELECTOR.PAGE);
                PROMISE.GET_DATA({
                    'search': data.search.value,
                    'start': data.start,
                    'length': data.length
                }).done(function(response) {
                    common.loader.hide(SELECTOR.PAGE);
                    callback(response);
                }).fail(function(exception) {
                    common.loader.hide(SELECTOR.PAGE);
                });
            }
        });
    }

    return {
        init: function() {
            $(document).ready(function() {
                initializeDatatable();
            });
        }
    };

});