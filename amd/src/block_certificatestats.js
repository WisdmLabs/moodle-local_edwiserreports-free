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
 * Certificate block.
 *
 * @copyright   2022 Wisdmlabs <support@wisdmlabs.com>
 * @author      Yogesh Shirsath
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/* eslint-disable no-unused-vars */
/* eslint-disable no-console */
define([
    'jquery',
    'core/notification',
    './defaultconfig',
    './common',
], function(
    $,
    Notification,
    CFG,
    common
) {

    /**
     * Selectors.
     */
    var SELECTOR = {
        PANEL: '#certificatesblock',
        TABLE: '#certificatesblock table',
        SEARCH: '.table-search-input input'
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
         * Get certificates.
         * @returns {PROMISE}
         */
        GET_CERTIFICATES: function() {
            return $.ajax({
                url: CFG.requestUrl,
                type: CFG.requestType,
                dataType: CFG.requestDataType,
                data: {
                    action: 'get_certificates_data_ajax',
                    secret: M.local_edwiserreports.secret,
                    lang: $('html').attr('lang')
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

        PROMISE.GET_CERTIFICATES().done(function(response) {
                if (dataTable !== null) {
                    dataTable.destroy();
                }
                dataTable = $(SELECTOR.TABLE).DataTable({
                    responsive: true,
                    data: response.data,
                    dom: '<"edwiserreports-table"<t><"table-pagination"p>>',
                    columns: [{
                        "data": "name"
                    }, {
                        "data": "coursename"
                    }, {
                        "data": "issued"
                    }, {
                        "data": "notissued"
                    }],
                    columnDefs: [{
                        "targets": [0, 1],
                        "className": "text-left"
                    }, {
                        "targets": "_all",
                        "className": "text-center",
                    }],
                    language: {
                        info: M.util.get_string('tableinfo', 'local_edwiserreports'),
                        infoEmpty: M.util.get_string('infoempty', 'local_edwiserreports'),
                        emptyTable: M.util.get_string('nocertificates', 'local_edwiserreports'),
                        zeroRecords: M.util.get_string('zerorecords', 'local_edwiserreports'),
                        paginate: {
                            previous: M.util.get_string('previous', 'moodle'),
                            next: M.util.get_string('next', 'moodle')
                        }
                    },
                    drawCallback: function() {
                        common.stylePaginationButton(this);
                    },
                    lengthChange: false,
                    bInfo: false
                });
            })
            .fail(Notification.exception)
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

        // Search in table.
        $('body').on('input', `${SELECTOR.PANEL} ${SELECTOR.SEARCH}`, function() {
            dataTable.columns(0).search($(this).val()).draw();
        });
    }

    // Must return the init function
    return {
        init: init
    };
});
