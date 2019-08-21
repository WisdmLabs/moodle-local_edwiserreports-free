define([
    'jquery',
    'report_elucidsitereport/variables',
    'report_elucidsitereport/common'
], function($, V) {
    function init(CONTEXTID) {
        var PageId = $("#wdm-completion-individual");
        var CompletionTable = PageId.find(".table");
        var loader = PageId.find(".loader");
        var Table = null;

        // Varibales for cohort filter
        var cohortId = 0;

        $(document).ready(function() {
            var courseId = V.getUrlParameter("courseid");
            getCourseCompletion(courseId, cohortId);

            /* Select cohort filter for active users block */
            $(V.cohortFilterItem).on('click', function() {
                cohortId = $(this).data('cohortid');
                $(V.cohortFilterBtn).html($(this).text());
                V.changeExportUrl(cohortId, V.exportUrlLink, V.cohortReplaceFlag);
                getCourseCompletion(courseId, cohortId);
            });
        });

        /**
         * Get Course Completion
         * @param  {Number} courseId Course Id
         * @param  {number} cohortId Cohort Id
         * @return {}
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
                ajax : url,
                dom : "<'pull-left'f><t><p>",
                oLanguage : {
                    sEmptyTable : "No users are enrolled as student"
                },
                columns : [
                    { "data": "username" },
                    { "data": "enrolledon" }, 
                    { "data": "enrolltype" },
                    { "data": "noofvisits" },
                    { "data": "completion" },
                    { "data": "compleiontime" },
                    { "data": "grade" },
                    { "data": "lastaccess" }
                ],
                columnDefs: [
                    { className: "text-left", targets: 0 },
                    { className: "text-left", targets: 1 },
                    { className: "text-center", targets: "_all" }
                ],
                initComplete: function() {
                    $(loader).hide();
                }
            });
        }
    }

    return {
        init : init
    };
	
});