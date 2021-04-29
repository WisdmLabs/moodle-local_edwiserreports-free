// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Plugin administration pages are defined here.
 *
 * @package     local_edwiserreports
 * @copyright   2021 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/* eslint-disable no-console */
define([
    'jquery',
    'core/modal_factory',
    'core/modal_events',
    'core/notification',
    'core/fragment',
    'core/templates',
    'local_edwiserreports/variables',
    './common',
    'local_edwiserreports/jquery.dataTables',
    'local_edwiserreports/dataTables.bootstrap4',
    'local_edwiserreports/flatpickr',
    'local_edwiserreports/common'
], function($, ModalFactory, ModalEvents, Notification, Fragment, Templates, V, common) {
    /**
     * Initialize
     * @param {integer} CONTEXTID Current page context id
     */
    function init(CONTEXTID) {
        var PageId = "#wdm-activeusers-individual";
        var ActiveUsersTable = PageId + " .table";
        var loader = PageId + " .loader";
        var ModalTrigger = ActiveUsersTable + " a";
        var dropdownToggle = "#filter-dropdown.dropdown-toggle";
        var dropdownMenu = ".dropdown-menu[aria-labelledby='filter-dropdown']";
        var dropdownItem = dropdownMenu + " .dropdown-item";
        var flatpickrCalender = "#flatpickrCalender";
        var dropdownButton = "button#filter-dropdown";
        var filter = 'weekly';
        var cohortId = 0;
        var dropdownInput = "#wdm-userfilter input.form-control.input";
        var sesskey = null;
        var DataTable = null;

        // Varibales for cohort filter
        var cohortFilterBtn = "#cohortfilter";
        var cohortFilterItem = cohortFilterBtn + " ~ .dropdown-menu .dropdown-item";
        // Var tableDom = '<"row"f><"row"t><"row"<"d-none"i><p>>';
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
            $(document).click(function(e) {
                if (!($(e.target).hasClass("dropdown-menu") ||
                    $(e.target).parents(".dropdown-menu").length)) {
                    $(dropdownMenu).removeClass('show');
                }
            });

            /* Select cohort filter for active users block */
            $(cohortFilterItem).on('click', function() {
                cohortId = $(this).data('cohortid');
                $(cohortFilterBtn).html($(this).text());
                $(PageId).find('.download-links input[name="cohortid"]').val(cohortId);
                createActiveUsersTable(filter, cohortId);
            });

            /* Select filter for active users block */
            $(dropdownItem + ":not(.custom)").on('click', function() {
                filter = $(this).attr('value');
                $(PageId).find('.download-links input[name="filter"]').val(filter);
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

        /**
         * Create modal of Users list
         */
        function createModalOfUsersList() {
            $(document).on('click', ModalTrigger, function() {
                var title = "";
                var action = $(this).data("action");
                var filter = $(this).data("filter");
                var ModalRoot = null;

                // eslint-disable-next-line no-eval
                var titleDate = V.formatDate(new Date(eval(filter * 1000)), "d MMM yyyy");

                if (action == "activeusers") {
                    title = M.util.get_string('activeusersmodaltitle', V.component, {
                        "date": titleDate
                    });
                } else if (action == "enrolments") {
                    title = M.util.get_string('enrolmentsmodaltitle', V.component, {
                        "date": titleDate
                    });
                } else if (action == "completions") {
                    title = M.util.get_string('completionsmodaltitle', V.component, {
                        "date": titleDate
                    });
                }

                ModalFactory.create({
                    body: Fragment.loadFragment(
                        'local_edwiserreports',
                        'userslist',
                        CONTEXTID,
                        {
                            page: 'activeusers',
                            filter: filter,
                            cohortid: cohortId,
                            action: action
                        }
                    )
                }).then(function(modal) {
                    ModalRoot = modal.getRoot();
                    ModalRoot.find('.modal-dialog').addClass('modal-lg');
                    modal.setTitle(title);
                    modal.show();
                    ModalRoot.on(ModalEvents.hidden, function() {
                        modal.destroy();
                    });

                    ModalRoot.on(ModalEvents.bodyRendered, function() {
                        var ModalTable = ModalRoot.find(".modal-table");

                        // If empty then remove colspan
                        if (ModalTable.find("tbody").hasClass("empty")) {
                            ModalTable.find("tbody").empty();
                        }

                        // Create dataTable for userslist
                        ModalRoot.find(".modal-table").DataTable({
                            language: {
                                searchPlaceholder: "Search User",
                                emptyTable: "There are no users"
                            },
                            drawCallback: function() {
                                $('.dataTables_paginate > .pagination').addClass('pagination-sm pull-right');
                                $('.dataTables_filter').addClass('pagination-sm pull-right');
                            },
                            // ScrollY : "350px",
                            // scrollX : true,
                            // paging: false,
                            bInfo: false
                        });
                    });
                    return;
                }).fail(Notification.exception);
            });
        }

        /**
         * Create Calender in dropdown tp select range.
         */
        function createDropdownCalendar() {
            $(flatpickrCalender).flatpickr({
                mode: 'range',
                altInput: true,
                altFormat: "d/m/Y",
                dateFormat: "Y-m-d",
                maxDate: "today",
                appendTo: document.getElementById("activeUser-calendar"),
                onOpen: function() {
                    $(dropdownMenu).addClass('withcalendar');
                },
                onClose: function() {
                    $(dropdownMenu).removeClass('withcalendar');
                    $(dropdownMenu).removeClass('show');
                    selectedCustomDate();
                }
            });
        }

        /**
         * After Select Custom date get active users details.
         */
        function selectedCustomDate() {
            filter = $(flatpickrCalender).val();
            var date = $(dropdownInput).val();

            /* If correct date is not selected then return false */
            if (!filter.includes("to")) {
                return;
            }

            $(dropdownButton).html(date);
            $(flatpickrCalender).val("");
            $(PageId).find('.download-links input[name="filter"]').val(filter);
            createActiveUsersTable(filter, cohortId);
        }

        /**
         * Create Active Users Table.
         * @param {string} filter Filter string.
         * @param {integer} cohortId Integer string.
         */
        function createActiveUsersTable(filter, cohortId) {
            sesskey = $(PageId).data("sesskey");

            /* If datatable is already created then destroy the table */
            if (DataTable) {
                DataTable.destroy();
                $(ActiveUsersTable).hide();
                $(loader).show();
            }

            // Show loader.
            common.loader.show("#wdm-activeusers-individual");

            $.ajax({
                url: V.requestUrl,
                data: {
                    action: 'get_activeusers_graph_data_ajax',
                    sesskey: sesskey,
                    data: JSON.stringify({
                        filter: filter,
                        cohortid: cohortId
                    })
                },
            }).done(function(response) {
                var ActiveUsers = [];
                response = JSON.parse(response);

                $.each(response.labels, function(idx, val) {
                    ActiveUsers[idx] = {
                        date: val,
                        filter: parseInt((new Date(val).getTime() / 1000)),
                        activeusers: response.data.activeUsers[idx],
                        courseenrolment: response.data.enrolments[idx],
                        coursecompletion: response.data.completionRate[idx]
                    };
                });

                var context = {
                    activeusers: ActiveUsers,
                    sesskey: sesskey
                };

                // eslint-disable-next-line promise/catch-or-return
                Templates.render('local_edwiserreports/activeuserstable', context)
                .then(function(html, js) {
                    Templates.replaceNode(ActiveUsersTable, html, js);
                    return;
                }).fail(function(ex) {
                    console.log(ex);
                }).always(function() {
                    DataTable = $(ActiveUsersTable).DataTable({
                        responsive: true,
                        // Dom : '<"pull-left"f><t><p>',
                        order: [[0, 'desc']],
                        language: {
                            searchPlaceholder: "Search Date",
                            emptyTable: "There are no active users"
                        },
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
                        info: false,
                        drawCallback: function() {
                            $('.dataTables_paginate > .pagination').addClass('pagination-sm pull-right');
                            $('.dataTables_filter').addClass('pagination-sm pull-right');
                        }
                        // ScrollY : 350,
                        // scrollX : true,
                        // paginate : false
                    });
                    $(ActiveUsersTable).show();
                    $(loader).hide();

                    // Hide loader.
                    common.loader.hide("#wdm-activeusers-individual");
                });
            }).fail(function(error) {
                console.log(error);
                // Hide loader.
                common.loader.hide("#wdm-activeusers-individual");
            });
        }
    }
    return {
        init: init
    };

});
