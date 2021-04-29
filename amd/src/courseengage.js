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
    'local_edwiserreports/jquery.dataTables',
    'local_edwiserreports/dataTables.bootstrap4',
    'local_edwiserreports/common'
], function($, ModalFactory, ModalEvents, Fragment, Templates, V) {
    /**
     * Initialize
     * @param {integer} CONTEXTID Current page context id
     */
    function init(CONTEXTID) {
        var PageId = "#wdm-courseengage-individual";
        var CourseEngageTable = PageId + " .table";
        var loader = PageId + " .loader";
        var sesskey = $(PageId).data("sesskey");
        var url = V.requestUrl + '?action=get_courseengage_data_ajax&sesskey=' + sesskey;
        var CourseEngageUsers = CourseEngageTable + " a.modal-trigger";
        var datatable = null;
        var exportUrlLink = ".dropdown-menu[aria-labelledby='export-dropdown'] .dropdown-item";

        // Varibales for cohort filter
        var cohortFilterBtn = "#cohortfilter";
        var cohortFilterItem = cohortFilterBtn + " ~ .dropdown-menu .dropdown-item";
        var cohortId = 0;

        $(document).ready(function() {
            createCourseEngageTable(cohortId);

            /* Select cohort filter for active users block */
            $(document).on('click', cohortFilterItem, function() {
                if (datatable) {
                    datatable.destroy();
                    $(CourseEngageTable).hide();
                    $(loader).show();
                }

                cohortId = $(this).data('cohortid');
                $("#engagement").find('.download-links input[name="cohortid"]').val(cohortId);
                V.changeExportUrl(cohortId, exportUrlLink, V.cohortReplaceFlag);
                $(cohortFilterBtn).html($(this).text());
                createCourseEngageTable(cohortId);
            });

            $(document).on('click', CourseEngageUsers, function() {
                var action = $(this).data("action");
                var courseid = $(this).data("courseid");
                var coursename = $(this).data("coursename");
                var ModalRoot = null;

                // eslint-disable-next-line promise/catch-or-return
                ModalFactory.create({
                    body: Fragment.loadFragment(
                        'local_edwiserreports',
                        'userslist',
                        CONTEXTID,
                        {
                            page: 'courseengage',
                            courseid: courseid,
                            action: action,
                            cohortid: cohortId
                        }
                    )
                }).then(function(modal) {
                    ModalRoot = modal.getRoot();
                    ModalRoot.find('.modal-dialog').addClass('modal-lg');
                    modal.setTitle(coursename);
                    modal.show();
                    ModalRoot.on(ModalEvents.hidden, function() {
                        modal.destroy();
                    });

                    ModalRoot.on(ModalEvents.shown, function() {
                        $(window).resize();
                    });

                    ModalRoot.on(ModalEvents.bodyRendered, function() {
                        var ModalTable = ModalRoot.find(".modal-table");

                        // If empty then remove colspan
                        if (ModalTable.find("tbody").hasClass("empty")) {
                            ModalTable.find("tbody").empty();
                        }

                        // Create dataTable for userslist
                        ModalRoot.find(".modal-table").DataTable({
                            language: {
                                searchPlaceholder: "Search User",
                                emptyTable: "There are no users"
                            },
                            drawCallback: function() {
                                ModalRoot.find('.dataTables_paginate > .pagination').addClass('pagination-sm pull-right');
                                ModalRoot.find('.dataTables_filter').addClass('pagination-sm pull-right');
                            },
                            lengthChange: false,
                            bInfo: false
                        });
                    });
                    return;
                });
            });
        });

        /**
         * Create course engagement table
         * @param  {int} cohortId Cohort ID
         */
        function createCourseEngageTable(cohortId) {
            $(CourseEngageTable).show();
            $(loader).hide();

            datatable = $(CourseEngageTable).DataTable({
                ajax: url + "&cohortid=" + cohortId,
                columns: [
                    {"data": "coursename"},
                    {"data": "enrolment"},
                    {"data": "visited"},
                    {"data": "activitystart"},
                    {"data": "completedhalf"},
                    {"data": "coursecompleted"}
                ],
                columnDefs: [
                    {className: "text-left", targets: 0},
                    {className: "text-center modal-trigger", targets: "_all"}
                ],
                language: {
                    searchPlaceholder: "Search Course",
                    emptyTable: "There are no courses"
                },
                drawCallback: function() {
                    $('.dataTables_paginate > .pagination').addClass('pagination-sm pull-right');
                    $('.dataTables_filter').addClass('pagination-sm pull-right');
                },
                bInfo: false
            });
        }
    }

    return {
        init: init
    };
});
