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
    'core/modal_factory',
    'core/modal_events',
    'core/fragment',
    'core/templates',
    'local_edwiserreports/variables',
    'local_edwiserreports/select2',
    'local_edwiserreports/jquery.dataTables',
    'local_edwiserreports/dataTables.bootstrap4',
    'local_edwiserreports/jquery-asPieProgress',
    'local_edwiserreports/common'
], function($, ModalFactory, ModalEvents, Fragment, Templates, V) {
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
        var dataTable = null;
        var certificateid = null;

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

            if (dataTable) {
                dataTable.destroy();
            }

            dataTable = CertTable.DataTable({
                ajax: V.generateUrl(V.requestUrl, params),
                columnDefs: [
                    {
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
                    {"data": "username"},
                    {"data": "email"},
                    {"data": "issuedate"},
                    {"data": "dateenrolled"},
                    {"data": "grade"},
                    {"data": "courseprogress"}
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
                    $('.dataTables_paginate > .pagination').addClass('pagination-sm pull-right');
                    $('.dataTables_filter').addClass('pagination-sm pull-right');
                    createPieProgress('');
                },
                bInfo: false,
                responsive: true
            });
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
            // CertDropdown.remove();
            // $(document).find(CertSelect).show();
            $(document).find(CertSelect).select2();

            certificateid = $(CertSelect).val();
            getCertificateDetail(certificateid);

            /* Select cohort filter for active users block */
            $(cohortFilterItem).on('click', function() {
                cohortId = $(this).data('cohortid');
                V.changeExportUrl(cohortId, exportUrlLink, V.cohortReplaceFlag);
                $(cohortFilterBtn).html($(this).text());
                getCertificateDetail(certificateid, cohortId);
            });

            $(document).on("change", CertSelect, function() {
                certificateid = $(this).val();
                getCertificateDetail(certificateid);
                $('.download-links input[name="filter"]').val(certificateid);
                // V.changeExportUrl(certificateid, exportUrlLink, V.filterReplaceFlag);
            });
        });
    }

    return {
        init: init
    };

});
