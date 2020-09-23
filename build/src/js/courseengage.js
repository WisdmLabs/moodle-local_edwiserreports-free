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
        var PageId = "#wdm-courseengage-individual";
        var CourseEngageTable = PageId + " .table";
        var loader = PageId + " .loader";
        var sesskey = $(PageId).data("sesskey");
        var url = V.requestUrl + '?action=get_courseengage_data_ajax&sesskey=' + sesskey;
        var CourseEngageUsers = CourseEngageTable + " a.modal-trigger";
        var datatable = null;
        var exportUrlLink = ".dropdown-menu[aria-labelledby='export-dropdown'] .dropdown-item";

        // Varibales for cohort filter
        var cohortFilterBtn   = "#cohortfilter";
        var cohortFilterItem  = cohortFilterBtn + " ~ .dropdown-menu .dropdown-item";
        var cohortId = 0;

        $(document).ready(function() {
            createCourseEngageTable(cohortId);

            /* Select cohort filter for active users block */
            $(document).on('click', cohortFilterItem, function() {
                if (datatable) {
                    datatable.destroy();
                    $(CourseEngageTable).hide();
                    $(loader).show();   
                }

                cohortId = $(this).data('cohortid');

                V.changeExportUrl(cohortId, exportUrlLink, V.cohortReplaceFlag);
                $(cohortFilterBtn).html($(this).text());
                createCourseEngageTable(cohortId);
            });

            $(document).on('click', CourseEngageUsers, function() {
                var action = $(this).data("action");
                var courseid = $(this).data("courseid");
                var coursename = $(this).data("coursename");
                var ModalRoot = null;

                ModalFactory.create({
                    body: Fragment.loadFragment(
                        'local_sitereport',
                        'userslist',
                        CONTEXTID,
                        {
                            page : 'courseengage',
                            courseid : courseid,
                            action : action,
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

                    ModalRoot.on(ModalEvents.shown, function () {
                        $(window).resize();
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
                                ModalRoot.find('.dataTables_paginate > .pagination').addClass('pagination-sm pull-right');
                                ModalRoot.find('.dataTables_filter').addClass('pagination-sm pull-right');
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

        /**
         * Create course engagement table
         * @param  {int} cohortId Cohort ID
         */
        function createCourseEngageTable(cohortId) {
            $(CourseEngageTable).show();
            $(loader).hide();

            datatable = $(CourseEngageTable).DataTable( {
                ajax : url + "&cohortid=" + cohortId,
                // dom : '<"pull-left"f><t><p>',
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
        }
    }

    return {
        init : init
    };
	
});
