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
                        'report_elucidsitereport',
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
                            scrollY : "350px",
                            scrollX : true,
                            paging: false,
                            bInfo : false
                        });
                    });
                });
            });
        });

        function createCourseEngageTable(cohortId) {
            datatable = $(CourseEngageTable).DataTable( {
                ajax : url + "&cohortid=" + cohortId,
                dom : '<"pull-left"f><t><p>',
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
                initComplete: function() {
                    $(CourseEngageTable).show();
                    $(loader).hide();
                },    
                scrollY : 350,
                scrollX : true,
                paginate : false
            });
        }
    }

    return {
        init : init
    };
	
});