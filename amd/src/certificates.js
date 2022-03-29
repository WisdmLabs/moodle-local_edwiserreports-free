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
    'local_edwiserreports/common',
    'local_edwiserreports/variables',
    'local_edwiserreports/select2',
    'local_edwiserreports/jquery.dataTables',
    'local_edwiserreports/dataTables.bootstrap4',
    'local_edwiserreports/jquery-asPieProgress'
], function($, common, V) {
    /* eslint-disable no-unused-vars */
    /**
     * Initialize
     * @param {integer} CONTEXTID Current page context id
     */
    function init(CONTEXTID) {
        /* eslint-enable no-unused-vars */
        var PageId = $("#wdm-certificates-individual");
        var CertTable = PageId.find(".table");
        var CertSelect = "#wdm-certificates-select";
        var exportUrlLink = ".dropdown-menu[aria-labelledby='export-dropdown'] .dropdown-item";
        var dataTable;
        var certificateid = null;
        var searchTable = PageId.find(".table-search-input input");
        var lengthSelect = PageId.find(".table-length-input select");

        // Varibales for cohort filter
        var cohortId = 0;
        var cohortFilterBtn = "#cohortfilter";
        var cohortFilterItem = cohortFilterBtn + " ~ .dropdown-menu .dropdown-item";

        /**
         * Get certificate detail using certificate id and cohort id
         * @param {Integer} certificateid Certificate id
         * @param {Integer} cohortId Cohort id
         */
        function getCertificateDetail(certificateid, cohortId) {
            var params = {
                action: 'get_certificates_data_ajax',
                sesskey: $(PageId).data("sesskey"),
                data: JSON.stringify({
                    certificateid: certificateid,
                    cohortid: cohortId
                })
            };

            dataTable.ajax.url(V.generateUrl(V.requestUrl, params)).load();
        }

        /**
         * Create pie progress where div with .pie-progress class is present
         * @param {String} target Target selector.
         */
        function createPieProgress(target) {
            var element = PageId;
            if (target != '') {
                element = element.find(target);
            }
            element.find('.pie-progress').asPieProgress({
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

        $(document).ready(function() {
            // Initialize datatable.
            dataTable = CertTable.DataTable({
                dom: '<"edwiserreports-table"i<t><"table-pagination"p>>',
                columnDefs: [{
                        "targets": 0,
                        "className": "align-middle"
                    },
                    {
                        "targets": 1,
                        "className": "align-middle"
                    },
                    {
                        "targets": "_all",
                        "className": "align-middle text-center"
                    }
                ],
                columns: [
                    { "data": "username" },
                    { "data": "email" },
                    { "data": "issuedate" },
                    { "data": "dateenrolled" },
                    { "data": "grade" },
                    { "data": "courseprogress" }
                ],
                language: {
                    searchPlaceholder: "Search User",
                    emptyTable: "No certificates are awarded"
                },
                // eslint-disable-next-line no-unused-vars
                initComplete: function(settings, json) {
                    $('.pie-progress').asPieProgress();
                    CertTable.show();
                },
                drawCallback: function() {
                    common.stylePaginationButton(this);
                    createPieProgress('');
                },
                responsive: true
            });

            // Initialize select2.
            $(document).find('.singleselect').select2();

            certificateid = $(CertSelect).val();
            getCertificateDetail(certificateid);

            /* Select cohort filter for active users block */
            $(cohortFilterItem).on('click', function() {
                cohortId = $(this).data('cohortid');
                V.changeExportUrl(cohortId, exportUrlLink, V.cohortReplaceFlag);
                $(cohortFilterBtn).html($(this).text());
                getCertificateDetail(certificateid, cohortId);
            });

            // Certificate change.
            $(document).on("change", CertSelect, function() {
                certificateid = $(this).val();
                getCertificateDetail(certificateid, cohortId);
                $('.download-links input[name="filter"]').val(certificateid);
            });

            // Observer length change.
            $(lengthSelect).on('change', function() {
                dataTable.page.len(this.value).draw();
            });

            // Search in table.
            $(searchTable).on('input', function() {
                dataTable.search(this.value).draw();
            });

            common.handleSearchInput();
        });
    }

    return {
        init: init
    };

});
