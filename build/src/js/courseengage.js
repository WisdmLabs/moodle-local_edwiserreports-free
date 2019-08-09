define([
    'jquery',
    'core/modal_factory',
    'core/modal_events',
    'core/fragment',
    'core/templates',
    'report_elucidsitereport/variables',
    'report_elucidsitereport/jquery.dataTables',
    'report_elucidsitereport/dataTables.bootstrap4'
], function($, ModalFactory, ModalEvents, Fragment, Templates, V) {
    function init(CONTEXTID) {
        $(document).ready(function() {
            var PageId = "#wdm-courseengage-individual";
            var CourseEngageTable = PageId + " .table";
            var loader = PageId + " .loader";
            var sesskey = $(PageId).data("sesskey");
            var url = M.cfg.wwwroot + '/report/elucidsitereport/request_handler.php';
            url += '?action=get_courseengage_data_ajax';
            url += '&sesskey=' + sesskey;

            $(CourseEngageTable).DataTable( {
                ajax : url,
                columns : [
                    { "data": "coursename" },
                    { "data": "enrolment" },
                    { "data": "visited" },
                    { "data": "activitystart" },
                    { "data": "completedhalf" },
                    { "data": "coursecompleted" }
                ],
                columnDefs: [
                    { className: "text-left", targets: 0 },
                    { className: "text-center", targets: "_all" }
                ],
                initComplete: function() {
                    console.log(loader);
                    $(loader).hide();
                }
            });
        });
    }

    return {
        init : init
    };
	
});