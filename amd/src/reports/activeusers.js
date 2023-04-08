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
 * Site Overview Status Report page.
 *
 * @package     local_edwiserreports
 * @author      Yogesh Shirsath
 * @copyright   2022 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/* eslint-disable no-console */
define('local_edwiserreports/reports/activeusers', [
    'jquery',
    'core/modal_factory',
    'core/modal_events',
    'core/notification',
    'core/fragment',
    'core/templates',
    'local_edwiserreports/variables',
    'local_edwiserreports/common',
    'local_edwiserreports/jquery.dataTables',
    'local_edwiserreports/dataTables.bootstrap4',
    'local_edwiserreports/flatpickr'
], function($, ModalFactory, ModalEvents, Notification, Fragment, Templates, V, common) {

    /**
     * Selector list.
     */
    var SELECTOR = {
        PAGE: '#activeusers',
        COHORT: ".cohort-select",
        LENGTH: ".length-select",
        SEARCH: ".table-search-input input",
        TABLE: "#activeusers .table",
        DOWNLOADCOHORTID: ".download-links input[name='cohortid']",
        DOWNLOADFILTER: ".download-links input[name='filter']",
        MODALTRIGGER: "#activeusers .table a",
        DATE: '.edwiserreports-calendar',
        DATEMENU: '.edwiserreports-calendar + .dropdown-menu',
        DATEITEM: '.edwiserreports-calendar + .dropdown-menu .dropdown-item',
        DATEPICKER: '.edwiserreports-calendar + .dropdown-menu .dropdown-calendar',
        DATEPICKERINPUT: '.edwiserreports-calendar + .dropdown-menu .flatpickr'
    };

    /**
     * Filter
     */
    var filter = 'last7days';

    /**
     * Datatable object.
     */
    var dataTable = null;

    /**
     * Modal table object.
     */
    var modalTable = null;

    /**
     * Flat picker object.
     */
    var flatpickr = null;

    /**
     * Create modal of Users list
     * @param {number} CONTEXTID Context id
     */
    function createModalOfUsersList(CONTEXTID) {
        $(document).on('click', SELECTOR.MODALTRIGGER, function() {
            var title = "";
            var action = $(this).data("action");
            var modalFilter = $(this).data("filter");
            var ModalRoot = null;

            // eslint-disable-next-line no-eval
            var titleDate = common.formatDate(new Date(eval(modalFilter * 86400 * 1000)), "d MMM yyyy");
            title = M.util.get_string(`${action}modaltitle`, V.component, {
                "date": titleDate
            });

            ModalFactory.create({
                body: Fragment.loadFragment(
                    'local_edwiserreports',
                    'userslist',
                    CONTEXTID, {
                        page: 'activeusers',
                        filter: modalFilter,
                        cohortid: $(SELECTOR.COHORT).val(),
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
                                previous: "&#9666",
                                next: "&#9656"
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
        let dateAlternate = $(SELECTOR.DATEPICKERINPUT).next().val().replace("to", "-"); // d M Y format
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
        createActiveUsersTable();
    }

    /**
     * Create Active Users Table.
     */
    function createActiveUsersTable() {
        $(SELECTOR.DOWNLOADFILTER).val(filter);
        $(SELECTOR.DOWNLOADCOHORTID).val($(SELECTOR.COHORT).val());

        // Show loader.
        common.loader.show("#activeusers");

        $.ajax({
            url: V.requestUrl,
            data: {
                action: 'get_activeusers_graph_data_ajax',
                secret: M.local_edwiserreports.secret,
                data: JSON.stringify({
                    filter: filter,
                    cohortid: $(SELECTOR.COHORT).val()
                })
            },
        }).done(function(response) {
            var ActiveUsers = [];
            response = JSON.parse(response);

            $.each(response.dates, function(idx, val) {
                ActiveUsers[idx] = {
                    date: common.formatDate(new Date(eval(val * 86400 * 1000)), "d MMM yyyy"),
                    filter: response.dates[idx],
                    activeusers: response.data.activeUsers[idx],
                    courseenrolment: response.data.enrolments[idx],
                    coursecompletion: response.data.completionRate[idx]
                };
            });

            var context = {
                activeusers: ActiveUsers,
                sesskey: M.cfg.sesskey
            };

            // eslint-disable-next-line promise/catch-or-return
            Templates.render('local_edwiserreports/activeuserstable', context)
                .then(function(html, js) {
                    /* If datatable is already created then destroy the table */
                    if (dataTable) {
                        dataTable.destroy();
                    }
                    Templates.replaceNode(SELECTOR.TABLE, html, js);
                    dataTable = $(SELECTOR.TABLE).DataTable({
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
                                previous: "&#9666",
                                next: "&#9656"
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
                    dataTable.page.len($(SELECTOR.LENGTH).val());
                    dataTable.column(0).search($(SELECTOR.SEARCH).val()).draw();

                    // Hide loader.
                    common.loader.hide("#activeusers");
                }).fail(function(ex) {
                    Notification.exception(ex);

                    // Hide loader.
                    common.loader.hide("#activeusers");
                });
        }).fail(function(error) {
            Notification.exception(error);
            // Hide loader.
            common.loader.hide("#activeusers");
        });
    }

    /**
     * Initialize
     * @param {integer} CONTEXTID Current page context id
     */
    function init(CONTEXTID) {

        // Initialize select2.
        $(SELECTOR.PAGE).find('.singleselect').select2();

        // Handle table search highlight.
        common.handleSearchInput();

        /* Select filter for active users block */
        $(SELECTOR.DATEITEM + ":not(.custom)").on('click', function() {
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
            createActiveUsersTable();
        });

        // Cohort filter.
        $(SELECTOR.COHORT).on('change', function() {
            createActiveUsersTable();
        });

        // Observer length change.
        $(SELECTOR.LENGTH).on('change', function() {
            dataTable.page.len(this.value).draw();
        });

        // Search in table.
        $(SELECTOR.SEARCH).on('input', function() {
            dataTable.column(0).search(this.value).draw();
        });

        createActiveUsersTable();
        createModalOfUsersList(CONTEXTID);
        createDropdownCalendar();
    }

    return {
        init: function(CONTEXTID) {
            $(document).ready(function() {
                init(CONTEXTID);
            });
        }
    };

});