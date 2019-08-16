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
        var PageId = "#wdm-courseengage-individual";
        var CourseEngageTable = PageId + " .table";
        var loader = PageId + " .loader";
        var sesskey = $(PageId).data("sesskey");
        var url = V.requestUrl + '?action=get_courseengage_data_ajax&sesskey=' + sesskey;
        var CourseEngageUsers = CourseEngageTable + " a.modal-trigger";
        var datatable = null;

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
                });
            });
        });

        function createCourseEngageTable(cohortId) {
            console.log(url + "&cohortid=" + cohortId);
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
                }
            });
        }
    }

    return {
        init : init
    };
	
});