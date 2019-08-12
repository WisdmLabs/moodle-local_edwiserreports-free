define([
    'jquery',
    'report_elucidsitereport/variables',
    'report_elucidsitereport/jquery.dataTables',
    'report_elucidsitereport/dataTables.bootstrap4'
], function($, V) {
    function init(CONTEXTID) {
        var PageId = $("#wdm-courseaccess-individual");
        var CompletionTable = PageId.find(".table");
        var loader = PageId.find(".loader");
        var url = V.requestUrl + "?action=get_courseaccess_data_ajax";

        $(document).ready(function() {
            var sesskey = PageId.data("sesskey");
            var courseId = V.getUrlParameter("courseid");
            var params = JSON.stringify({
                courseid: courseId
            });

            url += "&sesskey=" + sesskey;
            url += "&data=" + params;

            $(CompletionTable).show();

            CompletionTable.DataTable({
                ajax : url,
                oLanguage : {
                    sEmptyTable : "No users are enrolled as student"
                },
                columns : [
                    { "data": "username" },
                    { "data": "useremail" },
                    { "data": "visitscount" },
                    { "data": "lastvists" },
                    { "data": "enrolledon" },
                    { "data": "completion" }
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
        });
    }

    return {
        init : init
    };
	
});