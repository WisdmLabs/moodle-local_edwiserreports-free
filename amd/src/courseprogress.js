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
    './dataTables.bootstrap4',
    './select2'
], function($, ModalFactory, ModalEvents, Fragment, V, common) {
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
        var modalTable = null;

        // Cohort variable.
        var cohortId = 0;

        var sesskey = $(PageId).data("sesskey");
        var url = V.requestUrl + '?action=get_courseprogress_graph_data_ajax&sesskey=' + sesskey;

        // Initialize select2.
        $('#page-local-edwiserreports-coursereport').find('.singleselect').select2();

        $(document).ready(function() {

            common.handleSearchInput();

            // Observer length change.
            $('body').on('change', '#wdm-courseprogress-individual .table-length-input select', function() {
                datatable.page.len(this.value).draw();
            });

            // Search in table.
            $('body').on('input', '#wdm-courseprogress-individual .table-search-input input', function() {
                datatable.column(0).search(this.value).draw();
            });

            // Search in modal table.
            $('body').on('input', '.courseprogress-modal .table-search-input input', function() {
                modalTable.search(this.value).draw();
            });

            generateCourseProgressTable(cohortId);

            /* Select cohort filter for active users block */
            $(document).on('change', '#page-local-edwiserreports-coursereport .cohort-select', function() {
                if (datatable) {
                    datatable.destroy();
                    $(CourseProgressTable).hide();
                    $(loader).show();
                }
                cohortId = $(this).val();
                $("#page-local-edwiserreports-coursereport").find('.download-links input[name="cohortid"]').val(cohortId);
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
                        CONTEXTID, {
                            page: 'courseprogress',
                            courseid: courseid,
                            minval: minval,
                            maxval: maxval,
                            cohortid: cohortId
                        }
                    )
                }).then(function(modal) {
                    ModalRoot = modal.getRoot();
                    modal.getBody().addClass('courseprogress-modal');
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
                        modalTable = ModalTable.DataTable({
                            language: {
                                info: M.util.get_string('tableinfo', 'local_edwiserreports'),
                                infoEmpty: M.util.get_string('infoempty', 'local_edwiserreports'),
                                emptyTable: M.util.get_string('nousers', 'local_edwiserreports'),
                                zeroRecords: M.util.get_string('zerorecords', 'local_edwiserreports'),
                                paginate: {
                                    previous: M.util.get_string('previous', 'moodle'),
                                    next: M.util.get_string('next', 'moodle')
                                }
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
         * Generate course progress table
         * @param {Number} cohortId Cohort id
         */
        function generateCourseProgressTable(cohortId) {
            common.loader.show('#page-local-edwiserreports-coursereport #wdm-courseprogress-individual');
            $(CourseProgressTable).show();
            $(loader).hide();

            var data = JSON.stringify({
                courseid: "all",
                cohortid: cohortId
            });

            datatable = $(CourseProgressTable).DataTable({
                ajax: url + "&cohortid=" + cohortId + "&data=" + data,
                dom: '<"edwiserreports-table"<"p-2"i><t><"table-pagination"p>>',
                columns: [{
                    "data": "coursename"
                }, {
                    "data": "enrolments"
                }, {
                    "data": "completed"
                }, {
                    "data": "completed81to100"
                }, {
                    "data": "completed61to80"
                }, {
                    "data": "completed41to60"
                }, {
                    "data": "completed21to40"
                }, {
                    "data": "completed0to20"
                }],
                columnDefs: [{
                    className: "text-left",
                    targets: 0
                }, {
                    className: "text-center modal-trigger",
                    targets: "_all"
                }],
                language: {
                    info: M.util.get_string('tableinfo', 'local_edwiserreports'),
                    infoEmpty: M.util.get_string('infoempty', 'local_edwiserreports'),
                    emptyTable: M.util.get_string('nocourses', 'local_edwiserreports'),
                    zeroRecords: M.util.get_string('zerorecords', 'local_edwiserreports'),
                    paginate: {
                        previous: M.util.get_string('previous', 'moodle'),
                        next: M.util.get_string('next', 'moodle')
                    }
                },
                drawCallback: function() {
                    $(CourseProgressTable).find('th').addClass('theme-3-bg text-white');
                    common.stylePaginationButton(this);
                },
                initComplete: function() {
                    common.loader.hide('#page-local-edwiserreports-coursereport #wdm-courseprogress-individual');
                }
            });

            datatable.page.len($('#wdm-courseprogress-individual .table-length-input select').val()).draw();
            datatable.column(0).search($('#wdm-courseprogress-individual .table-search-input input').val()).draw();
        }
    }

    return {
        init: init
    };

});
