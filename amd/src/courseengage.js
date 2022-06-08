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
    'core/modal_factory',
    'core/modal_events',
    'core/fragment',
    './variables',
    './common',
    './jquery.dataTables',
    './dataTables.bootstrap4'
], function($, ModalFactory, ModalEvents, Fragment, V, common) {
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
        var modalTable = null;

        // Varibales for cohort filter
        var cohortId = 0;

        $(document).ready(function() {

            // Observer length change.
            $('body').on('change', '#wdm-courseengage-individual .table-length-input select', function() {
                datatable.page.len(this.value).draw();
            });

            // Search in table.
            $('body').on('input', '#wdm-courseengage-individual .table-search-input input', function() {
                datatable.column(0).search(this.value).draw();
            });

            // Search in modal table.
            $('body').on('input', '.courseengage-modal .table-search-input input', function() {
                modalTable.search(this.value).draw();
            });

            createCourseEngageTable(cohortId);

            /* Select cohort filter for active users block */
            $(document).on('change', '#page-local-edwiserreports-coursereport .cohort-select', function() {
                if (datatable) {
                    datatable.destroy();
                    $(CourseEngageTable).hide();
                    $(loader).show();
                }

                cohortId = $(this).val();
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
                        CONTEXTID, {
                            page: 'courseengage',
                            courseid: courseid,
                            action: action,
                            cohortid: cohortId
                        }
                    )
                }).then(function(modal) {
                    ModalRoot = modal.getRoot();
                    modal.getBody().addClass('courseengage-modal');
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
                        modalTable = ModalTable.DataTable({
                            language: {
                                emptyTable: "There are no users"
                            },
                            dom: '<"edwiserreports-table"i<t><"table-pagination"p>>',
                            drawCallback: function() {
                                ModalTable.find('th').addClass('theme-3-bg text-white');
                                common.stylePaginationButton(this);
                            },
                            lengthChange: false
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
            common.loader.show('#page-local-edwiserreports-coursereport #wdm-courseengage-individual');
            $(CourseEngageTable).show();
            $(loader).hide();

            datatable = $(CourseEngageTable).DataTable({
                ajax: url + "&cohortid=" + cohortId,
                dom: '<"edwiserreports-table"<"p-2"i><t><"table-pagination"p>>',
                columns: [{
                    "data": "coursename"
                }, {
                    "data": "enrolment"
                }, {
                    "data": "visited"
                }, {
                    "data": "activitystart"
                }, {
                    "data": "completedhalf"
                }, {
                    "data": "coursecompleted"
                }],
                columnDefs: [{
                    className: "text-left",
                    targets: 0
                }, {
                    className: "text-center modal-trigger",
                    targets: "_all"
                }],
                language: {
                    searchPlaceholder: "Search Course",
                    emptyTable: "There are no courses"
                },
                drawCallback: function() {
                    $(CourseEngageTable).find('th').addClass('theme-3-bg text-white');
                    common.stylePaginationButton(this);
                },
                initComplete: function() {
                    common.loader.hide('#page-local-edwiserreports-coursereport #wdm-courseengage-individual');
                }
            });

            datatable.page.len($('#wdm-courseengage-individual .table-length-input select').val()).draw();
            datatable.column(0).search($('#wdm-courseengage-individual .table-search-input input').val()).draw();
        }
    }

    return {
        init: init
    };
});
