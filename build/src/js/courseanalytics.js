define([
    'jquery',
    'report_elucidsitereport/variables',
    'report_elucidsitereport/jquery.dataTables',
    'report_elucidsitereport/dataTables.bootstrap4'
], function($, V) {
    function init(CONTEXTID) {
        var PageId = $("#wdm-courseanalytics-individual");
        var RecentVisits = PageId.find(".recent-visits .table");
        var RecentEnroled = PageId.find(".recent-enrolment .table");
        var RecentCompletion = PageId.find(".recent-completion .table");
        var loader = PageId.find(".loader");

        $(document).ready(function() {
            var sesskey = PageId.data("sesskey");
            var courseId = V.getUrlParameter("courseid");

            $.ajax({
                url: V.requestUrl,
                type: V.requestType,
                dataType: V.requestDataType,
                data: {
                    action: 'get_courseanalytics_data_ajax',
                    sesskey: sesskey,
                    data: JSON.stringify({
                        courseid: courseId
                    })
                },
            })
            .done(function(response) {
                /* Generate Recent Visit Table */
                generateDataTable(RecentVisits, response.data.recentvisits);

                /* Generate Recent Enrolment Table */
                generateDataTable(RecentEnroled, response.data.recentenrolments);

                /* Generate Recent Completion Table */
                generateDataTable(RecentCompletion, response.data.recentcompletions);
            })
            .fail(function(error) {
                console.log(error);
            });
        });

        /* Generate Data Table for specific blocks */
        function generateDataTable(table, data) {
            var emptyStr = "No users has Enrolled in the course";;
            $(table).show();

            if (table == RecentCompletion){
                emptyStr = "No users has completed any course";
            }

            table.DataTable({
                data : data,
                oLanguage : {
                    sEmptyTable : emptyStr
                },
                columnDefs: [
                    { className: "text-left", targets: 0 },
                    { className: "text-center", targets: "_all" }
                ],
                order: [[ 1, 'desc' ]],
                initComplete: function() {
                    $(loader).hide();
                },
                paging: false,
                bInfo : false,
                searching : false
            });
        }
    }

    return {
        init : init
    };
	
});