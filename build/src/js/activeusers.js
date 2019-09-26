define([
    'jquery',
    'core/modal_factory',
    'core/modal_events',
    'core/fragment',
    'core/templates',
    'report_elucidsitereport/variables',
    'report_elucidsitereport/jquery.dataTables',
    'report_elucidsitereport/dataTables.bootstrap4',
    'report_elucidsitereport/flatpickr',
    'report_elucidsitereport/common'
], function($, ModalFactory, ModalEvents, Fragment, Templates, V) {
    function init(CONTEXTID) {
        var PageId            = "#wdm-activeusers-individual";
        var ActiveUsersTable  = PageId + " .table";
        var loader            = PageId + " .loader";
        var ModalTrigger      = ActiveUsersTable + " a";
        var dropdownToggle    = "#filter-dropdown.dropdown-toggle";
        var dropdownMenu      = ".dropdown-menu[aria-labelledby='filter-dropdown']";
        var dropdownItem      = dropdownMenu + " .dropdown-item";
        var flatpickrCalender = "#flatpickrCalender";
        var dropdownButton    = "button#filter-dropdown";
        var filter            = 'weekly';
        var cohortId          = 0;
        var dropdownInput     = "#wdm-userfilter input.form-control.input";
        var sesskey           = null;
        var DataTable         = null;
        var exportUrlLink = ".dropdown-menu[aria-labelledby='export-dropdown'] .dropdown-item";

        // Varibales for cohort filter
        var cohortFilterBtn   = "#cohortfilter";
        var cohortFilterItem  = cohortFilterBtn + " ~ .dropdown-menu .dropdown-item";
        // var tableDom = '<"row"f><"row"t><"row"<"d-none"i><p>>';

        $(document).ready(function() {
            /* Show custom dropdown */
            $(dropdownToggle).on("click", function() {
                $(dropdownMenu).addClass("show");
            });

            /* Added Custom Value in Dropdown */
            $(dropdownInput).ready(function() {
                var placeholder = $(dropdownInput).attr("placeholder");
                $(dropdownInput).val(placeholder);
            });

            /* Hide dropdown when click anywhere in the screen */
            $(document).click(function(e){
                if (!($(e.target).hasClass("dropdown-menu") || 
                    $(e.target).parents(".dropdown-menu").length)) {
                    $(dropdownMenu).removeClass('show');
                }
            });

            /* Select cohort filter for active users block */
            $(cohortFilterItem).on('click', function() {
                cohortId = $(this).data('cohortid');
                $(cohortFilterBtn).html($(this).text());
                V.changeExportUrl(cohortId, exportUrlLink, V.cohortReplaceFlag);
                createActiveUsersTable(filter, cohortId);
            });

            /* Select filter for active users block */
            $(dropdownItem + ":not(.custom)").on('click', function() {
                filter = $(this).attr('value');
                V.changeExportUrl(filter, exportUrlLink, V.filterReplaceFlag);
                $(dropdownMenu).removeClass('show');
                $(dropdownButton).html($(this).text());
                createActiveUsersTable(filter, cohortId);
                $(flatpickrCalender).val("Custom");
                $(dropdownInput).val("Custom");
            });

            createActiveUsersTable();
            createModalOfUsersList();
            createDropdownCalendar();
        });

        /* Create modal of Users list */
        function createModalOfUsersList() {
            $(document).on('click', ModalTrigger, function() {
                var title = "";
                var action = $(this).data("action");
                var filter = $(this).data("filter");
                var ModalRoot = null;

                var titleDate = V.formatDate(new Date(eval(filter*1000)), "d MMM yyyy");

                if (action == "activeusers") {
                    title = M.util.get_string('activeusersmodaltitle', V.component, {
                        "date" : titleDate
                    });
                } else if (action == "enrolments") {
                    title = M.util.get_string('enrolmentsmodaltitle', V.component, {
                        "date" : titleDate
                    });
                } else if (action == "completions") {
                    title = M.util.get_string('completionsmodaltitle', V.component, {
                        "date" : titleDate
                    });
                }


                ModalFactory.create({
                    body: Fragment.loadFragment(
                        'report_elucidsitereport',
                        'userslist',
                        CONTEXTID,
                        {
                            page : 'activeusers',
                            filter : filter,
                            cohortid : cohortId,
                            action : action
                        }
                    )
                }).then(function(modal) {
                    ModalRoot = modal.getRoot();
                    modal.setTitle(title);
                    modal.show();
                    ModalRoot.on(ModalEvents.hidden, function () {
                        modal.destroy();
                    });
                });
            });
        }

        /* Create Calender in dropdown tp select range */
        function createDropdownCalendar() {
            $(flatpickrCalender).flatpickr({
                mode: 'range',
                altInput: true,
                altFormat: "d/m/Y",
                dateFormat: "Y-m-d",
                maxDate: "today",
                appendTo: document.getElementById("activeUser-calendar"),
                onOpen: function(event) {
                    $(dropdownMenu).addClass('withcalendar');
                },
                onClose: function() {
                    $(dropdownMenu).removeClass('withcalendar');
                    $(dropdownMenu).removeClass('show');
                    selectedCustomDate();
                }
            });
        }

        /* After Select Custom date get active users details */
        function selectedCustomDate() {
            filter = $(flatpickrCalender).val();
            var date = $(dropdownInput).val();

            /* If correct date is not selected then return false */
            if (!filter.includes("to")) {
                return false;
            }

            $(dropdownButton).html(date);
            $(flatpickrCalender).val("");
            V.changeExportUrl(filter, exportUrlLink, V.filterReplaceFlag);
            createActiveUsersTable(filter, cohortId);
        }

        /* Create Active Users Table */
        function createActiveUsersTable(filter, cohortId) {
            sesskey = $(PageId).data("sesskey");

            /* If datatable is already created then destroy the table */
            if (DataTable) {
                DataTable.destroy();
                $(ActiveUsersTable).hide();
                $(loader).show();
            }

            $.ajax({
                url: V.requestUrl,
                data: {
                    action: 'get_activeusers_graph_data_ajax',
                    sesskey: sesskey,
                    data: JSON.stringify({
                        filter : filter,
                        cohortid : cohortId
                    })
                },
            }).done(function(response) {
                var ActiveUsers = [];

                $.each(response.labels, function(idx, val) {
                    ActiveUsers[idx] = {
                        date : val,
                        filter : parseInt((new Date(val).getTime() / 1000)),
                        activeusers : response.data.activeUsers[idx],
                        courseenrolment : response.data.enrolments[idx],
                        coursecompletion : response.data.completionRate[idx]
                    };
                });

                var context = {
                    activeusers : ActiveUsers,
                    sesskey : sesskey
                }

                Templates.render('report_elucidsitereport/activeusers', context)
                .then(function(html, js) {
                    Templates.replaceNode(PageId, html, js);
                }).fail(function(ex) {
                    console.log(ex);
                }).always(function() {
                    DataTable = $(ActiveUsersTable).DataTable({
                        responsive: true,
                        order : [[0, 'desc']],
                        columnDefs: [
                            {
                                "targets": 0,
                                "className": "text-left"
                            },
                            {
                                "targets": "_all",
                                "className": "text-center",
                            }
                        ],
                        info : false,
                        bLengthChange : false,

                    });
                    $(ActiveUsersTable).show();
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