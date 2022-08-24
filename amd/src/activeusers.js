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
    './variables',
    './common',
    './jquery.dataTables',
    './dataTables.bootstrap4',
    './flatpickr'
], function($, ModalFactory, ModalEvents, Notification, Fragment, Templates, V, common) {

    var filter = 'last7days';

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
        var cohortFilter = '.cohort-select';
        var cohortId = 0;
        var dropdownInput = "#userfilter input.form-control.input";
        var sesskey = null;
        var DataTable = null;
        var modalTable = null;
        var searchTable = PageId + " .table-search-input input";
        var lengthSelect = PageId + " .table-length-input select";
        var flatpickr = null;

        var SELECTOR = {
            DATESELECTED: '.selected-period',
            DATE: '.edwiserreports-calendar',
            DATEMENU: '.edwiserreports-calendar + .dropdown-menu',
            DATEITEM: '.edwiserreports-calendar + .dropdown-menu .dropdown-item',
            DATEPICKER: '.edwiserreports-calendar + .dropdown-menu .dropdown-calendar',
            DATEPICKERINPUT: '.edwiserreports-calendar + .dropdown-menu .flatpickr'
        };

        // Initialize select2.
        $(PageId).find('.singleselect').select2();

        $(PageId).find('.download-links input[name="cohortid"]').val(cohortId);
        $(PageId).find('.download-links input[name="filter"]').val(filter);

        $(document).ready(function() {

            common.handleSearchInput();

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

            /* Select filter for active users block */
            $(dropdownItem + ":not(.custom)").on('click', function() {
                filter = $(this).data('value');

                // Set custom selected item as active.
                $(SELECTOR.DATEITEM).removeClass('active');
                $(this).addClass('active');

                // Show selected item on dropdown button.
                $(SELECTOR.DATE).html($(this).text());

                // Hide dropdown.
                $(SELECTOR.DATEMENU).removeClass('show');

                // Clear custom date.
                flatpickr.clear();
                createActiveUsersTable(cohortId);
            });

            // Cohort filter.w
            $(PageId + " " + cohortFilter).on('change', function() {
                cohortId = $(this).val();
                $(PageId).find('.download-links input[name="cohortid"]').val(cohortId);
                createActiveUsersTable(cohortId);
            });

            createActiveUsersTable(cohortId);
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
                var modalFilter = $(this).data("filter");
                var ModalRoot = null;

                // eslint-disable-next-line no-eval
                var titleDate = V.formatDate(new Date(eval(modalFilter * 86400 * 1000)), "d MMM yyyy");

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
                        CONTEXTID, {
                            page: 'activeusers',
                            filter: modalFilter,
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
                        modalTable = ModalRoot.find(".modal-table").DataTable({
                            dom: '<"edwiserreports-table"<"p-2"i><t><"table-pagination"p>>',
                            language: {
                                info: M.util.get_string('tableinfo', 'local_edwiserreports'),
                                infoEmpty: M.util.get_string('infoempty', 'local_edwiserreports'),
                                emptyTable: M.util.get_string('nousers', 'local_edwiserreports'),
                                zeroRecords: M.util.get_string('zerorecords', 'local_edwiserreports'),
                                paginate: {
                                    previous: M.util.get_string('previous', 'moodle'),
                                    next: M.util.get_string('next', 'moodle')
                                }
                            },
                            drawCallback: function() {
                                common.stylePaginationButton(this);
                            }
                        });

                        ModalRoot.find('.table-search-input input').on('input', function() {
                            modalTable.search(this.value).draw();
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
            flatpickr = $(SELECTOR.DATEPICKERINPUT).flatpickr({
                mode: 'range',
                altInput: true,
                altFormat: "d M Y",
                dateFormat: "Y-m-d",
                maxDate: "today",
                appendTo: $(SELECTOR.DATEPICKER).get(0),
                onOpen: function() {
                    $(SELECTOR.DATEMENU).addClass('withcalendar');
                    $(SELECTOR.DATE).dropdown('update');
                },
                onClose: function() {
                    $(SELECTOR.DATEMENU).removeClass('withcalendar');
                    selectedCustomDate();
                }
            });
        }

        /**
         * After Select Custom date get active users details.
         */
        function selectedCustomDate() {
            let date = $(SELECTOR.DATEPICKERINPUT).val(); // Y-m-d format
            let dateAlternate = $(SELECTOR.DATEPICKERINPUT).next().val().replace("to", "-"); // Date d M Y format.
            $(SELECTOR.DATEPICKERINPUT).next().val(dateAlternate);

            /* If correct date is not selected then return false */
            if (!date.includes(" to ")) {
                flatpickr.clear();
                return;
            }

            // Set active class to custom date selector item.
            $(SELECTOR.DATEITEM).removeClass('active');
            $(SELECTOR.DATEITEM + '.custom').addClass('active');

            // Show custom date to dropdown button.
            $(SELECTOR.DATE).html(dateAlternate);

            filter = date;

            // Hide dropdown.
            $(SELECTOR.DATEMENU).removeClass('show');
            createActiveUsersTable(cohortId);
        }

        /**
         * Create Active Users Table.
         * @param {integer} cohortId Integer string.
         */
        function createActiveUsersTable(cohortId) {
            $(PageId).find('.download-links input[name="filter"]').val(filter);
            let date, day, month, year;
            let months = [
                M.util.get_string('january', 'local_edwiserreports'),
                M.util.get_string('february', 'local_edwiserreports'),
                M.util.get_string('march', 'local_edwiserreports'),
                M.util.get_string('april', 'local_edwiserreports'),
                M.util.get_string('may', 'local_edwiserreports'),
                M.util.get_string('june', 'local_edwiserreports'),
                M.util.get_string('july', 'local_edwiserreports'),
                M.util.get_string('august', 'local_edwiserreports'),
                M.util.get_string('september', 'local_edwiserreports'),
                M.util.get_string('october', 'local_edwiserreports'),
                M.util.get_string('november', 'local_edwiserreports'),
                M.util.get_string('december', 'local_edwiserreports')
            ];
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

                $.each(response.dates, function(idx, val) {
                    date = new Date(val * 86400 * 1000);
                    day = date.getDate();
                    month = months[date.getMonth()];
                    year = date.getFullYear();
                    ActiveUsers[idx] = {
                        date: `${(day < 10 ? '0' + day : day)} ${month} ${year}`,
                        filter: response.dates[idx],
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
                        Notification.exception(ex);
                    }).always(function() {
                        DataTable = $(ActiveUsersTable).DataTable({
                            responsive: true,
                            dom: '<"edwiserreports-table"<"p-2"i><t><"table-pagination"p>>',
                            order: [
                                [0, 'desc']
                            ],
                            language: {
                                info: M.util.get_string('tableinfo', 'local_edwiserreports'),
                                infoEmpty: M.util.get_string('infoempty', 'local_edwiserreports'),
                                emptyTable: M.util.get_string('noactiveusers', 'local_edwiserreports'),
                                zeroRecords: M.util.get_string('zerorecords', 'local_edwiserreports'),
                                paginate: {
                                    previous: M.util.get_string('previous', 'moodle'),
                                    next: M.util.get_string('next', 'moodle')
                                }
                            },
                            columnDefs: [{
                                    "targets": 0,
                                    "className": "text-left"
                                },
                                {
                                    "targets": "_all",
                                    "className": "text-center",
                                }
                            ],
                            drawCallback: function() {
                                common.stylePaginationButton(this);
                            }
                        });
                        DataTable.page.len($(lengthSelect).val());
                        DataTable.column(0).search($(searchTable).val()).draw();
                        $(ActiveUsersTable).show();
                        $(loader).hide();

                        // Hide loader.
                        common.loader.hide("#wdm-activeusers-individual");
                    });
            }).fail(function(error) {
                Notification.exception(error);
                // Hide loader.
                common.loader.hide("#wdm-activeusers-individual");
            });
        }
        // Observer length change.
        $(lengthSelect).on('change', function() {
            DataTable.page.len(this.value).draw();
        });

        // Search in table.
        $(searchTable).on('input', function() {
            DataTable.column(0).search(this.value).draw();
        });
    }
    return {
        init: init
    };

});