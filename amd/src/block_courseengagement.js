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
 * Course Engagement block.
 *
 * @copyright   2022 Wisdmlabs <support@wisdmlabs.com>
 * @author      Yogesh Shirsath
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/* eslint-disable no-console, no-unused-vars */
define([
    'jquery',
    'core/modal_factory',
    'core/modal_events',
    'core/fragment',
    'local_edwiserreports/defaultconfig',
    'local_edwiserreports/common',
], function(
    $,
    ModalFactory,
    ModalEvents,
    Fragment,
    CFG,
    common
) {

    /**
     * Selectors.
     */
    var SELECTOR = {
        PANEL: '#courseengagementblock',
        COHORT: '.cohort-select',
        TABLE: '#courseengagementblock table',
        SEARCH: '.table-search-input input',
        USERS: '#courseengagementblock table a.modal-trigger',
        MODALSEARCH: '.courseengage-modal .table-search-input input'
    };

    /**
     * Filter.
     */
    var filter = {
        cohort: 0
    };

    /**
     * Data table object.
     */
    var dataTable = null;

    /**
     * Promises list.
     */
    let PROMISE = {
        /**
         * Get timespent on site using filters.
         * @param {Object} filter Filter data
         * @returns {PROMISE}
         */
        GET_COURSEENGAGEMENT: function(filter) {
            return $.ajax({
                url: CFG.requestUrl,
                type: CFG.requestType,
                dataType: CFG.requestDataType,
                data: {
                    action: 'get_courseengagement_data_ajax',
                    secret: M.local_edwiserreports.secret,
                    lang: $('html').attr('lang'),
                    data: JSON.stringify({
                        filter: filter
                    })
                },
            });
        }
    };

    /**
     * Load data to dataTable using ajax.
     */
    function loadData() {
        // Show loader.
        common.loader.show(SELECTOR.PANEL);

        PROMISE.GET_COURSEENGAGEMENT(filter).done(function(response) {
                if (dataTable !== null) {
                    dataTable.destroy();
                }
                dataTable = $(SELECTOR.TABLE).DataTable({
                    data: response.data,
                    dom: '<"edwiserreports-table"<t><"table-pagination"p>>',
                    columns: [{
                        "data": "coursename"
                    }, {
                        "data": "category"
                    }, {
                        "data": "enrolment"
                    }, {
                        "data": "coursecompleted"
                    }, {
                        "data": "completionspercentage"
                    }, {
                        "data": "visited"
                    }, {
                        "data": "averagevisits"
                    }, {
                        "data": "timespent"
                    }, {
                        "data": "averagetimespent"
                    }],
                    columnDefs: [{
                        className: "text-left",
                        targets: 0
                    }, {
                        className: "text-center",
                        targets: "_all"
                    }],
                    info: false,
                    language: {
                        infoEmpty: M.util.get_string('infoempty', 'local_edwiserreports'),
                        emptyTable: M.util.get_string('nocourses', 'local_edwiserreports'),
                        zeroRecords: M.util.get_string('zerorecords', 'local_edwiserreports'),
                        paginate: {
                            previous: M.util.get_string('previous', 'moodle'),
                            next: M.util.get_string('next', 'moodle')
                        }
                    },
                    drawCallback: function() {
                        $(SELECTOR.TABLE).find('th').addClass('theme-3-bg text-white');
                        common.stylePaginationButton(this);
                    }
                });
            })
            .fail(function(data) {
                console.log(data);
            })
            .always(function() {
                // Hide loader.
                common.loader.hide(SELECTOR.PANEL);
            });

    }

    /**
     * Initialize
     * @param {function} invalidUser Callback function
     */
    function init(invalidUser) {
        // Block not present on page.
        if ($(SELECTOR.PANEL).length === 0) {
            return;
        }
        // Enable select2 on cohort filter.
        $(SELECTOR.PANEL).find('.singleselect').select2();

        loadData();
    }

    // Must return the init function
    return {
        init: init
    };
});