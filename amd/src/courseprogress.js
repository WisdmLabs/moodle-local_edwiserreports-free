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
        var PageId = "#wdm-courseprogress-individual";
        var CourseProgressTable = PageId + " .table";
        var loader = PageId + " .loader";
        var ModalTrigger = CourseProgressTable + " a.modal-trigger";
        var datatable = null;

        // Varibales for cohort filter
        var cohortFilterBtn = "#cohortfilter";
        var cohortFilterItem = cohortFilterBtn + " ~ .dropdown-menu .dropdown-item";
        var cohortId = 0;
        var sesskey = $(PageId).data("sesskey");
        var url = V.requestUrl + '?action=get_courseprogress_graph_data_ajax&sesskey=' + sesskey;

        $(document).ready(function() {
            generateCourseProgressTable(cohortId);

            /* Select cohort filter for active users block */
            $(document).on('click', cohortFilterItem, function() {
                if (datatable) {
                    datatable.destroy();
                    $(CourseProgressTable).hide();
                    $(loader).show();
                }
                cohortId = $(this).data('cohortid');
                $("#progress").find('.download-links input[name="cohortid"]').val(cohortId);
                $(cohortFilterBtn).html($(this).text());
                generateCourseProgressTable(cohortId);
            });

            $(document).on('click', ModalTrigger, function() {
                var minval = $(this).data("minvalue");
                var maxval = $(this).data("maxvalue");
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
                            page: 'courseprogress',
                            courseid: courseid,
                            minval: minval,
                            maxval: maxval,
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
                                $('.dataTables_paginate > .pagination').addClass('pagination-sm pull-right');
                                $('.dataTables_filter').addClass('pagination-sm pull-right');
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
         * Generate course progress table
         * @param {Number} cohortId Cohort id
         */
        function generateCourseProgressTable(cohortId) {
            $(CourseProgressTable).show();
            $(loader).hide();

            var data = JSON.stringify({
                courseid: "all",
                cohortid: cohortId
            });

            datatable = $(CourseProgressTable).DataTable({
                ajax: url + "&cohortid=" + cohortId + "&data=" + data,
                columns: [
                    {"data": "coursename"},
                    {"data": "enrolments"},
                    {"data": "completed100"},
                    {"data": "completed80"},
                    {"data": "completed60"},
                    {"data": "completed40"},
                    {"data": "completed20"},
                    {"data": "completed00"}
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
