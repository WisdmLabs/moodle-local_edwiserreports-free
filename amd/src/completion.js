/* eslint-disable no-unused-vars */
define([
    'jquery',
    'local_edwiserreports/variables',
    'local_edwiserreports/common'
], function($, V) {
    /**
     * Initialize
     * @param {integer} CONTEXTID Current page context id
     */
    function init(CONTEXTID) {
        CONTEXTID = null;
        var PageId = $("#wdm-completion-individual");
        var CompletionTable = PageId.find(".table");
        var loader = PageId.find(".loader");
        var Table = null;

        // Varibales for cohort filter
        var cohortId = 0;

        $(document).ready(function() {
            // Get course id
            var courseId = $(PageId).find('.download-links input[name="filter"]').val();

            getCourseCompletion(courseId, cohortId);

            /* Select cohort filter for active users block */
            $(V.cohortFilterItem).on('click', function() {
                cohortId = $(this).data('cohortid');
                $(V.cohortFilterBtn).html($(this).text());
                // V.changeExportUrl(cohortId, V.exportUrlLink, V.cohortReplaceFlag);
                $(PageId).find('.download-links input[name="cohortid"]').val(cohortId);
                getCourseCompletion(courseId, cohortId);
            });
        });

        /**
         * Get Course Completion
         * @param  {Number} courseId Course Id
         * @param  {number} cohortId Cohort Id
         */
        function getCourseCompletion(courseId, cohortId) {
            if (Table) {
                Table.destroy();
                CompletionTable.hide();
                loader.show();
            }

            var params = {
                action: "get_completion_data_ajax",
                sesskey: PageId.data("sesskey"),
                data: JSON.stringify({
                    courseid: courseId,
                    cohortid: cohortId
                })
            };
            var url = V.generateUrl(V.requestUrl, params);

            CompletionTable.show();
            Table = CompletionTable.DataTable({
                ajax: url,
                oLanguage: {
                    sEmptyTable: "No users are enrolled as student",
                    sSearchPlaceholder: "Search User"
                },
                columns: [
                    {"data": "username"},
                    {"data": "enrolledon"},
                    {"data": "enrolltype"},
                    {"data": "noofvisits"},
                    {"data": "completion"},
                    {"data": "compleiontime"},
                    {"data": "grade"},
                    {"data": "lastaccess"}
                ],
                drawCallback: function() {
                    $('.dataTables_paginate > .pagination').addClass('pagination-sm pull-right');
                    $('.dataTables_filter').addClass('pagination-sm pull-right');
                },
                columnDefs: [
                    {className: "text-left", targets: 0},
                    {className: "text-left", targets: 1},
                    {className: "text-center", targets: "_all"}
                ],
                initComplete: function() {
                    $(loader).hide();
                },
                bInfo: false
            });
        }
    }

    return {
        init: init
    };

});
