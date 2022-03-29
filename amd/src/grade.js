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
 * @package     local_edwiserreports
 * @copyright   2021 wisdmlabs <support@wisdmlabs.com>
 * @author      Yogesh Shirsath
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
        PAGE: '#grade',
        TABLE: '#grade table',
        COURSE: '#grade .course-select',
        FORMFILTER: '#grade .download-links [name="filter"]',
        COHORT: '#grade .cohort-select',
        SEARCHTABLE: '#grade .table-search-input input',
        LENGTH: '#grade .table-length-input select'
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
         * Get grade table data based on filters.
         * @param {Object} filter Filter data
         * @returns {PROMISE}
         */
        GET_DATA: function(filter) {
            return $.ajax({
                url: CFG.requestUrl,
                type: CFG.requestType,
                dataType: CFG.requestDataType,
                data: {
                    action: 'get_grade_table_data_ajax',
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
            scrollX: true,
            scrollCollapse: true,
            autoWidth: true,
            columns: [
                { data: "student" },
                { data: "course" },
                { data: "activity", sortable: false },
                { data: "grade" }
            ],
            dom: '<"edwiserreports-table"i<t><"table-pagination"p>>',
            language: {
                searchPlaceholder: M.util.get_string('searchuser', 'local_edwiserreports'),
                emptyTable: M.util.get_string('emptytable', 'local_edwiserreports')
            },
            drawCallback: function() {
                common.stylePaginationButton(this);
            },
            // eslint-disable-next-line no-unused-vars
            ajax: function(data, callback, settings) {
                common.loader.show(SELECTOR.PAGE);
                // Filter: course-cohort-search-column-dir.
                $(SELECTOR.FORMFILTER).val([
                    $(SELECTOR.COURSE).val(), // Course id.
                    $(SELECTOR.COHORT).val(), // Cohort id.
                    data.search.value, // Search value.
                    data.order[0].column, // Order column.
                    data.order[0].dir // Order by column and direction.
                ].join('-'));
                PROMISE.GET_DATA({
                    'cohort': $(SELECTOR.COHORT).val(),
                    'course': $(SELECTOR.COURSE).val(),
                    'search': data.search.value,
                    'start': data.start,
                    'length': data.length,
                    'ordering': data.order[0]
                }).done(function(response) {
                    common.loader.hide(SELECTOR.PAGE);
                    callback(response);
                }).fail(function(exception) {
                    common.loader.hide(SELECTOR.PAGE);
                });
            }
        });
    }

    /**
     * Initialize
     */
    function init() {
        // Initialize select2.
        $(SELECTOR.PAGE).find('.singleselect').select2();

        // Observer course change event.
        $(SELECTOR.COURSE).on('change', function() {
            if (dataTable === null) {
                initializeDatatable();
                return;
            }
            dataTable.ajax.reload();
        });

        // Observer cohort change.
        $(SELECTOR.COHORT).on('change', function() {
            if (dataTable === null) {
                initializeDatatable();
                return;
            }
            dataTable.ajax.reload();
        });

        // Search in table.
        $('body').on('input', SELECTOR.SEARCHTABLE, function() {
            dataTable.search(this.value).draw();
        });

        // Observer length change.
        $(SELECTOR.LENGTH).on('change', function() {
            if (dataTable === null) {
                initializeDatatable();
                return;
            }
            console.log(this.value);
            dataTable.page.len(this.value);
            dataTable.ajax.reload();
        });

        initializeDatatable();
        common.handleSearchInput();
    }

    return {
        init: function() {
            $(document).ready(function() {
                init();
            });
        }
    };

});
