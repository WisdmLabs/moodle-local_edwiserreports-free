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
            var PageId = "#wdm-courseprogress-individual";
            var CourseProgressTable = PageId + " .table";
            var loader = PageId + " .loader";
            var ModalTrigger = CourseProgressTable + " a";
            var dropdownBody = ".table-dropdown";
            var dropdownTable = PageId + " .dataTables_wrapper .row:first-child > div:first-child";

            $.ajax({
                url: V.requestUrl,
                data: {
                    action: 'get_courseprogress_graph_data_ajax',
                    sesskey: $(PageId).data("sesskey"),
                    data: JSON.stringify({
                        courseid : "all"
                    })
                },
            }).done(function(response) {
                var context = {
                    courseprogress : response
                };

                Templates.render('report_elucidsitereport/courseprogress', context)
                .then(function(html, js) {
                    Templates.replaceNode(PageId, html, js);
                }).fail(function(ex) {
                    console.log(ex);
                }).always(function() {
                    $(CourseProgressTable).DataTable({
                        order : [[0, 'desc']],
                        bLengthChange : false,
                        pageLength : 50,
                        initComplete: function() {
                            $(dropdownTable + " .dropdown").show();
                        },
                        columnDefs : [
                            {
                                "targets": 0,
                                "className": "text-left" 
                            },
                            {
                                "targets": "_all",
                                "className": "text-center",
                            }
                        ]
                    });
                    $(CourseProgressTable).removeClass("d-none");
                    $(loader).remove();
                });
            }).fail(function(error) {
                console.log(error);
            });

            // $('#wdm-activeusers-individual .table').DataTable();
            $(document).on('click', ModalTrigger, function() {
                var minval = $(this).data("minvalue");
                var maxval = $(this).data("maxvalue");
                var courseid = $(this).data("courseid");
                var coursename = $(this).data("coursename");
                var ModalRoot = null;

                ModalFactory.create({
                    body: Fragment.loadFragment(
                        'report_elucidsitereport',
                        'userslist',
                        CONTEXTID,
                        {
                            page : 'courseprogress',
                            courseid : courseid,
                            minval : minval,
                            maxval : maxval,
                        }
                    )
                }).then(function(modal) {
                    ModalRoot = modal.getRoot();
                    modal.setTitle(coursename);
                    modal.show();
                    ModalRoot.on(ModalEvents.hidden, function () {
                        modal.destroy();
                    });
                });
            });
        });
    }

    return {
        init : init
    };
	
});