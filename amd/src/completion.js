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
    './variables',
    './common',
    './select2'
], function($, V, common) {
    /* eslint-disable no-unused-vars */
    /**
     * Initialize
     * @param {integer} CONTEXTID Current page context id
     */
    function init(CONTEXTID) {
        /* eslint-enable no-unused-vars */
        var PageId = "#wdm-completion-individual";
        var CompletionTable = $(PageId).find(".table");
        var Table = null;
        var pageLength = 10;
        var lengthSelect = "#wdm-completion-individual .table-length-input select";
        var searchTable = "#wdm-completion-individual .table-search-input input";

        // Varibales for cohort filter
        var cohortId = 0;

        $(document).ready(function() {

            common.handleSearchInput();

            // Get course id
            var courseId = $(PageId).find('.download-links input[name="filter"]').val();

            getCourseCompletion(courseId, cohortId);

            // Select cohort filter for active users block.
            $(PageId).find('.cohort-select').on('change', function() {
                cohortId = $(this).val();
                $(PageId).find('.download-links input[name="cohortid"]').val(cohortId);
                getCourseCompletion(courseId, cohortId);
            });

            // Initialize select2.
            $(PageId).find('.singleselect').select2();

            // Observer length change.
            $(lengthSelect).on('change', function() {
                Table.page.len(this.value).draw();
                pageLength = this.value;
            });

            // Search in table.
            $(searchTable).on('input', function() {
                Table.column(0).search(this.value).draw();
            });
        });

        /**
         * Get Course Completion
         * @param  {Number} courseId Course Id
         * @param  {number} cohortId Cohort Id
         */
        function getCourseCompletion(courseId, cohortId) {
            common.loader.show(PageId);

            var params = {
                action: "get_completion_data_ajax",
                sesskey: $(PageId).data("sesskey"),
                data: JSON.stringify({
                    courseid: courseId,
                    cohortid: cohortId
                })
            };

            if (Table !== null) {
                Table.destroy();
            }

            Table = CompletionTable.DataTable({
                dom: '<"edwiserreports-table"<"table-filter d-flex"i><t><"table-pagination"p>>',
                ajax: V.generateUrl(V.requestUrl, params),
                pageLength: pageLength,
                language: {
                    info: M.util.get_string('tableinfo', 'local_edwiserreports'),
                    infoEmpty: M.util.get_string('infoempty', 'local_edwiserreports'),
                    emptyTable: M.util.get_string('nostudentsenrolled', 'local_edwiserreports'),
                    zeroRecords: M.util.get_string('zerorecords', 'local_edwiserreports'),
                    paginate: {
                        previous: M.util.get_string('previous', 'moodle'),
                        next: M.util.get_string('next', 'moodle')
                    }
                },
                columns: [{
                    "data": "username"
                }, {
                    "data": "enrolledon"
                }, {
                    "data": "enrolltype"
                }, {
                    "data": "noofvisits"
                }, {
                    "data": "completion"
                }, {
                    "data": "compleiontime"
                }, {
                    "data": "grade"
                }, {
                    "data": "lastaccess"
                }],
                drawCallback: function() {
                    common.stylePaginationButton(this);
                },
                columnDefs: [{
                    className: "text-left",
                    targets: 0
                }, {
                    className: "text-left",
                    targets: 1
                }, {
                    className: "text-center",
                    targets: "_all"
                }],
                initComplete: function() {
                    common.loader.hide(PageId);
                }
            });
        }
    }

    return {
        init: init
    };

});
