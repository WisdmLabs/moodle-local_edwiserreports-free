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
define([
    'jquery',
    './common',
    './defaultconfig',
    'core/notification',
    './select2',
    './jquery.dataTables',
    './dataTables.bootstrap4',
    './jquery-asPieProgress'
], function($, common, CFG, Notification) {

    /**
     * Selectors
     */
    var SELECTOR = {
        PAGE: "#wdm-certificates-individual",
        TABLE: "#wdm-certificates-individual .table",
        COHORT: "#wdm-certificates-individual .cohort-select",
        CERTIFICATE: "#wdm-certificates-individual .certificate-select",
        SEARCH: "#wdm-certificates-individual .table-search-input input",
        LENGTH: "#wdm-certificates-individual .table-length-select select",
    };

    /**
     * DataTable object.
     */
    var dataTable = null;

    /**
     * Filters
     */
    var filter = {
        cohort: 0,
        certificateid: 0
    };

    /**
     * Ajax Promises
     */
    var PROMISES = {
        /**
         * Get certificates using ajax.
         * @returns {Promise}
         */
        GET_CERTIFICATES: function() {
            return $.ajax({
                url: CFG.requestUrl,
                type: CFG.requestType,
                dataType: CFG.requestDataType,
                data: {
                    action: 'get_certificates_data_ajax',
                    secret: M.local_edwiserreports.secret,
                    lang: $('html').attr('lang'),
                    data: JSON.stringify(filter)
                },
            });
        }
    };

    /**
     * Get certificate detail using certificate id and cohort id
     */
    function loadData() {
        if (dataTable) {
            dataTable.destroy();
        }

        // Show loader when data is being loaded.
        common.loader.show(SELECTOR.PAGE);

        // Fetch certificates list.
        PROMISES.GET_CERTIFICATES().done(function(response) {
                // Render data table.
                dataTable = $(SELECTOR.TABLE).DataTable({
                    data: response.data,
                    dom: '<"edwiserreports-table"i<t><"table-pagination"p>>',
                    columnDefs: [{
                        "targets": 0,
                        "className": "align-middle"
                    }, {
                        "targets": 1,
                        "className": "align-middle"
                    }, {
                        "targets": "_all",
                        "className": "align-middle text-center"
                    }],
                    columns: [{
                        "data": "username"
                    }, {
                        "data": "email"
                    }, {
                        "data": "issuedate"
                    }, {
                        "data": "dateenrolled"
                    }, {
                        "data": "grade"
                    }, {
                        "data": "courseprogress"
                    }],
                    language: {
                        info: M.util.get_string('tableinfo', 'local_edwiserreports'),
                        infoEmpty: M.util.get_string('infoempty', 'local_edwiserreports'),
                        emptyTable: M.util.get_string('nocertificatesawarded', 'local_edwiserreports'),
                        zeroRecords: M.util.get_string('zerorecords', 'local_edwiserreports'),
                        paginate: {
                            previous: M.util.get_string('previous', 'moodle'),
                            next: M.util.get_string('next', 'moodle')
                        }
                    },
                    // eslint-disable-next-line no-unused-vars
                    initComplete: function(settings, json) {
                        $('.pie-progress').asPieProgress();
                    },
                    drawCallback: function() {
                        common.stylePaginationButton(this);
                        createPieProgress('');
                    },
                    responsive: true
                });
            }).fail(Notification.exception)
            .always(function() {
                common.loader.hide(SELECTOR.PAGE);
            });
    }

    /**
     * Create pie progress where div with .pie-progress class is present
     */
    function createPieProgress() {
        $(SELECTOR.PAGE).find('.pie-progress').asPieProgress({
            namespace: 'pie-progress',
            speed: 30,
            classes: {
                svg: 'pie-progress-svg',
                element: 'pie-progress',
                number: 'pie-progress-number',
                content: 'pie-progress-content'
            }
        });
    }

    /* eslint-disable no-unused-vars */
    /**
     * Initialize
     * @param {integer} CONTEXTID Current page context id
     */
    function init(CONTEXTID) {
        /* eslint-enable no-unused-vars */
        $(document).ready(function() {
            // Initialize select2.
            $(document).find('.singleselect').select2();

            filter.certificateid = $(SELECTOR.CERTIFICATE).val();
            loadData();

            // Certificate change.
            $(document).on("change", SELECTOR.CERTIFICATE, function() {
                filter.certificateid = $(this).val();
                loadData();
                $('.download-links input[name="filter"]').val(filter.certificateid);
            });

            $(document).on("change", SELECTOR.COHORT, function() {
                filter.cohort = $(this).val();
                loadData();
                $('.download-links input[name="cohortid"]').val(filter.cohort);
            });

            // Observer length change.
            $(SELECTOR.LENGTH).on('change', function() {
                dataTable.page.len(this.value).draw();
            });

            // Search in table.
            $(SELECTOR.SEARCH).on('input', function() {
                dataTable.search(this.value).draw();
            });

            common.handleSearchInput();
        });
    }

    return {
        init: init
    };

});