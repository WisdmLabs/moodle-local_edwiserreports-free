define([
    'jquery',
    'core/modal_factory',
    'core/modal_events',
    'core/fragment',
    'core/templates',
    'report_elucidsitereport/variables',
    'report_elucidsitereport/jquery.dataTables',
    'report_elucidsitereport/dataTables.bootstrap4',
    'report_elucidsitereport/common'
], function($, ModalFactory, ModalEvents, Fragment, Templates, V) {
    function init(CONTEXTID) {
        var PageId = "#wdm-courseprogress-individual";
        var CourseProgressTable = PageId + " .table";
        var loader = PageId + " .loader";
        var ModalTrigger = CourseProgressTable + " a";
        var dropdownBody = ".table-dropdown";
        var dropdownTable = PageId + " .dataTables_wrapper .row:first-child > div:first-child";
        var datatable = null;

        // Varibales for cohort filter
        var cohortFilterBtn   = "#cohortfilter";
        var cohortFilterItem  = cohortFilterBtn + " ~ .dropdown-menu .dropdown-item";
        var cohortId = 0;
        var sesskey = null;

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
                $(cohortFilterBtn).html($(this).text());
                generateCourseProgressTable(cohortId);
            });

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
                            cohortid : cohortId
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

        // Generate course progress table 
        function generateCourseProgressTable(cohortId) {
            sesskey = $(PageId).data("sesskey");
            $.ajax({
                url: V.requestUrl,
                data: {
                    action: 'get_courseprogress_graph_data_ajax',
                    sesskey: sesskey,
                    data: JSON.stringify({
                        courseid : "all",
                        cohortid : cohortId
                    })
                },
            }).done(function(response) {
                var context = {
                    courseprogress : response,
                    sesskey : sesskey
                };

                Templates.render('report_elucidsitereport/courseprogress', context)
                .then(function(html, js) {
                    Templates.replaceNode(PageId, html, js);
                }).fail(function(ex) {
                    console.log(ex);
                }).always(function() {
                    datatable = $(CourseProgressTable).DataTable({
                        dom : '<"pull-left"f><t><p>',
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
                    $(CourseProgressTable).show();
                    $(loader).hide();
                });
            }).fail(function(error) {
                console.log(error);
            });
        }
    }

    return {
        init : init
    };
	
});