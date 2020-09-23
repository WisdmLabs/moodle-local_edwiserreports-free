define([
    'jquery',
    'core/modal_factory',
    'core/modal_events',
    'core/fragment',
    'core/templates',
    'local_sitereport/variables',
    'local_sitereport/jquery.dataTables',
    'local_sitereport/dataTables.bootstrap4',
    'local_sitereport/common'
], function($, ModalFactory, ModalEvents, Fragment, Templates, V) {
    function init(CONTEXTID) {
        var PageId = "#wdm-courseprogress-individual";
        var CourseProgressTable = PageId + " .table";
        var loader = PageId + " .loader";
        var ModalTrigger = CourseProgressTable + " a.modal-trigger";
        var dropdownBody = ".table-dropdown";
        var dropdownTable = PageId + " .dataTables_wrapper .row:first-child > div:first-child";
        var datatable = null;

        // Varibales for cohort filter
        var cohortFilterBtn   = "#cohortfilter";
        var cohortFilterItem  = cohortFilterBtn + " ~ .dropdown-menu .dropdown-item";
        var cohortId = 0;
        var sesskey = $(PageId).data("sesskey");
        var url = V.requestUrl + '?action=get_courseprogress_graph_data_ajax&sesskey=' + sesskey;

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
                        'local_sitereport',
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

                    ModalRoot.on(ModalEvents.bodyRendered, function () {
                        var ModalTable = ModalRoot.find(".modal-table");

                        // If empty then remove colspan
                        if (ModalTable.find("tbody").hasClass("empty")) {
                            ModalTable.find("tbody").empty();
                        }

                        // Create dataTable for userslist
                        ModalRoot.find(".modal-table").DataTable({
                            language: {
                                searchPlaceholder: "Search users",
                                emptyTable: "There are no users"
                            },
                            drawCallback: function () {
                                $('.dataTables_paginate > .pagination').addClass('pagination-sm pull-right');
                                $('.dataTables_filter').addClass('pagination-sm pull-right');
                            },
                            // scrollY : "350px",
                            // scrollX : true,
                            // paging: false,
                            lengthChange: false,
                            bInfo : false
                        });
                    });
                });
            });
        });

        // Generate course progress table 
        function generateCourseProgressTable(cohortId) {
            $(CourseProgressTable).show();
            $(loader).hide();

            var data = JSON.stringify({
                courseid : "all",
                cohortid : cohortId
            });

            datatable = $(CourseProgressTable).DataTable( {
                ajax : url + "&cohortid=" + cohortId + "&data=" + data,
                // dom : '<"pull-left"f><t><p>',
                columns : [
                    { "data": "coursename" },
                    { "data": "enrolments" },
                    { "data": "completed100" },
                    { "data": "completed80" },
                    { "data": "completed60" },
                    { "data": "completed40" },
                    { "data": "completed20" },
                    { "data": "completed00" }
                ],
                columnDefs: [
                    { className: "text-left", targets: 0 },
                    { className: "text-center modal-trigger", targets: "_all" }
                ],
                language: {
                    searchPlaceholder: "Search courses",
                    emptyTable: "There are no courses"
                },
                drawCallback: function () {
                    $('.dataTables_paginate > .pagination').addClass('pagination-sm pull-right');
                    $('.dataTables_filter').addClass('pagination-sm pull-right');
                },
                // scrollY : 350,
                // scrollX : true,
                // paginate : false,
                // sScrollX : "100%",
                // bScrollCollapse : true
                bInfo: false
            });

            // $.ajax({
            //     url: V.requestUrl,
            //     data: {
            //         action: 'get_courseprogress_graph_data_ajax',
            //         sesskey: sesskey,
            //         data: JSON.stringify({
            //             courseid : "all",
            //             cohortid : cohortId
            //         })
            //     },
            // }).done(function(response) {
            //     var context = {
            //         courseprogress : response,
            //         sesskey : sesskey
            //     };

            //     Templates.render('local_sitereport/courseprogress', context)
            //     .then(function(html, js) {
            //         Templates.replaceNode(PageId, html, js);
            //         datatable = $(CourseProgressTable).DataTable({
            //             // dom : '<"pull-left"f><t><p>',
            //             order : [[0, 'desc']],
            //             bLengthChange : false,
            //             initComplete: function() {
            //                 $(dropdownTable + " .dropdown").show();
            //             },
            //             columnDefs : [
            //                 {
            //                     "targets": 0,
            //                     "className": "text-left" 
            //                 },
            //                 {
            //                     "targets": "_all",
            //                     "className": "text-center",
            //                 }
            //             ],
            //             language: {
            //                 searchPlaceholder: "Search courses",
            //                 emptyTable: "There are no courses"
            //             },
            //             // scrollY : 350,
            //             // scrollX : true,
            //             // paginate : false,
            //             // sScrollX : "100%",
            //             // bScrollCollapse : true
            //             binfo: false
            //         });
            //         $(CourseProgressTable).show();
            //         $(loader).hide();
            //     }).fail(function(ex) {
            //         console.log(ex);
            //     }).always(function() {
            //         $(window).resize();
            //     });
            // }).fail(function(error) {
            //     console.log(error);
            // });
        }
    }

    return {
        init : init
    };
	
});
