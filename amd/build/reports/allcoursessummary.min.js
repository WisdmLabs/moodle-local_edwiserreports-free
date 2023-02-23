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
 * All courses summary report page.
 *
 * @package     local_edwiserreports
 * @author      Yogesh Shirsath
 * @copyright   2022 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('local_edwiserreports/reports/allcoursessummary', [
    'jquery',
    'core/ajax',
    'core/notification',
    'core/modal_factory',
    'core/modal_events',
    'core/fragment',
    'local_edwiserreports/defaultconfig',
    'local_edwiserreports/common',
    'local_edwiserreports/jquery.dataTables',
    'local_edwiserreports/dataTables.bootstrap4',
    'local_edwiserreports/select2',
    'local_edwiserreports/flatpickr'

], function($, Ajax, Notification, ModalFactory, ModalEvents, Fragment, CFG, common) {

    /**
     * Selector
     */
    var SELECTOR = {
        PAGE: '#allcoursessummary',
        TABLE: '#allcoursessummary table',
        FORMFILTER: '#allcoursessummary .download-links [name="filter"]',
        COHORT: '#allcoursessummary .cohort-select',
        EXCLUDE: '#allcoursessummary .exclude-select',
        GROUP: '#allcoursessummary .group-select',
        SEARCH: '#allcoursessummary .table-search-input input',
        LENGTH: '#allcoursessummary .length-select',
        MODALTRIGGER: '#allcoursessummary table a.modal-trigger',
        MODALSEARCH: '.allcoursessummary-modal .table-search-input input',
        DATE: '.edwiserreports-calendar',
        DATEMENU: '.edwiserreports-calendar + .dropdown-menu',
        DATEITEM: '.edwiserreports-calendar + .dropdown-menu .dropdown-item',
        DATEPICKER: '.edwiserreports-calendar + .dropdown-menu .dropdown-calendar',
        DATEPICKERINPUT: '.edwiserreports-calendar + .dropdown-menu .flatpickr'
    };

    /**
     * Flat picker object.
     */
    let flatpickr = null;


    /**
     * Datatable object.
     */
    var dataTable = null;

    /**
     * Filters
     */
    var filter = {
        cohort: 0,
        coursegroup: 0,
        exclude: [],
        enrolment: 'all'
    };

    /**
     * All promises.
     */
    var PROMISE = {
        /**
         * Get all courses summary table data based on filters.
         * @returns {PROMISE}
         */
        GET_DATA: function() {
            return $.ajax({
                url: CFG.requestUrl,
                type: CFG.requestType,
                dataType: CFG.requestDataType,
                data: {
                    action: 'get_allcoursessummary_data',
                    secret: M.local_edwiserreports.secret,
                    lang: $('html').attr('lang'),
                    data: JSON.stringify(filter)
                },
            });
        },

        /**
         * Get filter data.
         *
         * @param {Array}   types       Type of filters to get
         * @param {Integer} cohortid    Cohort id
         * @returns {PROMISE}
         */
        GET_FILTER_DATA: function(types, cohortid) {
            return Ajax.call([{
                methodname: 'local_edwiserreports_get_filter_data',
                args: {
                    types: types,
                    cohort: cohortid
                }
            }], false)[0];
        }
    }

    /**
     * After Select Custom date get active users details.
     */
    function customDateSelected() {
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

        filter.enrolment = date;
        initializeDatatable();
        // common.updateTimeLabel(date);
    }

    /**
     * ----------
     *  END - Functions added for date selector
     * -----------
     */



    /**
     * Generate course progress table
     */
    function initializeDatatable() {
        $(SELECTOR.FORMFILTER).val(JSON.stringify(filter));
        common.loader.show(SELECTOR.PAGE);
        if (dataTable !== null) {
            dataTable.destroy();
        }

        // Fetch data using ajax.
        PROMISE.GET_DATA(filter).then(function(response) {
                dataTable = $(SELECTOR.TABLE).DataTable({
                    data: response,
                    dom: '<"edwiserreports-table"<"p-2"i><t><"table-pagination"p>>',
                    columnDefs: [
                        { className: "fixed-column", targets: 0 },
                        { className: "text-left", targets: [0, 1, 2] },
                        { className: "text-right", targets: "_all" }
                    ],
                    columns: [
                        { "data": "coursename", width: "14rem" },
                        { "data": "category", width: "14rem" },
                        { "data": "enrolments" },
                        { "data": "completed" },
                        { "data": "notstarted" },
                        { "data": "inprogress" },
                        { "data": "atleastoneactivitystarted", width: "8rem" },
                        { "data": "totalactivities", width: "5rem" },
                        { "data": "avgprogress", width: "6rem" },
                        { "data": "avggrade", width: "4rem" },
                        { "data": "highestgrade", width: "4rem" },
                        { "data": "lowestgrade", width: "4rem" },
                        {
                            data: "totaltimespent",
                            render: function(data) {
                                return common.timeFormatter(data);
                            },
                            width: "5rem"
                        },
                        {
                            data: "avgtimespent",
                            render: function(data) {
                                return common.timeFormatter(data);
                            },
                            width: "5rem"
                        }
                    ],
                    columnDefs: [
                        { className: "fixed-column", targets: 0 },
                        { className: "text-left", targets: [0, 1] },
                        { className: "text-right", targets: "_all" }
                    ],
                    language: {
                        info: M.util.get_string('tableinfo', 'local_edwiserreports'),
                        infoEmpty: M.util.get_string('infoempty', 'local_edwiserreports'),
                        emptyTable: M.util.get_string('nocourses', 'local_edwiserreports'),
                        zeroRecords: M.util.get_string('zerorecords', 'local_edwiserreports'),
                        paginate: {
                            previous: "&#9666",
                            next: "&#9656"
                        }
                    },
                    drawCallback: function() {
                        common.stylePaginationButton(this);
                    },
                    initComplete: function() {
                        common.loader.hide(SELECTOR.PAGE);
                    }
                });
                dataTable.columns(0).search($(SELECTOR.SEARCH).val());
                dataTable.page.len($(SELECTOR.LENGTH).val()).draw();
            })
            .fail(function(ex) {
                Notification.exception(ex);
                common.loader.hide(SELECTOR.PAGE);
            });
    }

    /**
     * Modal table.
     */
    var modalTable = null;

    /**
     * Initialize
     */
    function init() {

        // Show time period in table info.
        // common.updateTimeLabel('all');

        // Initialize select2.
        $(SELECTOR.PAGE).find('.singleselect').not(SELECTOR.EXCLUDE).select2();
        $(SELECTOR.EXCLUDE).select2({
            placeholder: M.util.get_string('exclude', 'local_edwiserreports'),
            allowClear: true
        });

        // Handle search input highlight.
        common.handleSearchInput();

        // Generate table.
        initializeDatatable();

        // Observer length change.
        $('body').on('change', SELECTOR.LENGTH, function() {
            dataTable.page.len(this.value).draw();
        });

        // Search in table.
        $('body').on('input', SELECTOR.SEARCH, function() {
            dataTable.column(0).search(this.value).draw();
        });

        // Search in modal table.
        $('body').on('input', SELECTOR.MODALSEARCH, function() {
            modalTable.search(this.value).draw();
        });

        // Observer cohort change.
        $('body').on('change', SELECTOR.COHORT, function() {
            filter.cohort = $(this).val();
            filter.coursegroup = 0;
            PROMISE.GET_FILTER_DATA(['coursegroup'], filter.cohort)
                .done(function(response) {
                    response = JSON.parse(response);
                    common.refreshFilter('group', response, SELECTOR.PAGE, function() {
                        initializeDatatable();
                    });
                });
        });

        // Observer exclude change.
        $('body').on('change', SELECTOR.EXCLUDE, function() {
            filter.exclude = $(this).val();
            $(this).toggleClass('notselected', filter.exclude.length == 0);
            $(this).toggleClass('selected', filter.exclude.length != 0);
            initializeDatatable();
        });

        flatpickr = $(SELECTOR.DATEPICKERINPUT).flatpickr({
            mode: 'range',
            altInput: true,
            altFormat: "d M Y",
            dateFormat: "Y-m-d",
            maxDate: "today",
            appendTo: $(SELECTOR.DATEPICKER).get(0),
            onOpen: function() {
                $(SELECTOR.DATEMENU).addClass('withcalendar');
                setTimeout(function() {
                    if ($(SELECTOR.DATEMENU).offset().left < $(SELECTOR.PAGE).parent().offset().left) {
                        $(SELECTOR.DATEMENU).css('left', $(SELECTOR.DATEMENU).closest('.filter-selector').css('padding-left'));
                    }
                }, 500);
            },
            onChange: function() {
                if ($(SELECTOR.DATEMENU).offset().left < $(SELECTOR.PAGE).parent().offset().left) {
                    $(SELECTOR.DATEMENU).css('left', $(SELECTOR.DATEMENU).closest('.filter-selector').css('padding-left'));
                }
            },
            onClose: function() {
                $(SELECTOR.DATEMENU).removeClass('withcalendar');
                customDateSelected();
            }
        });


        /* Date selector listener */
        $('body').on('click', SELECTOR.DATEITEM + ":not(.custom)", function() {
            // Set custom selected item as active.
            $(SELECTOR.DATEITEM).removeClass('active');
            $(this).addClass('active');

            // Show selected item on dropdown button.
            $(SELECTOR.DATE).html($(this).text());

            // Clear custom date.
            flatpickr.clear();

            filter.enrolment = $(this).data('value');
            initializeDatatable();
            // common.updateTimeLabel(filter.enrolment);
        });

        // Observer group change.
        $('body').on('change', SELECTOR.GROUP, function() {
            filter.coursegroup = $(this).val();
            initializeDatatable();
        });

        // Open user list modal on number click.
        $('body').on('click', SELECTOR.MODALTRIGGER, function() {
            let _this = $(this);
            let ModalRoot = null;
            let coursegroup = $(SELECTOR.GROUP).length != 0 ? $(SELECTOR.GROUP).val() : '0,0';
            let group = 0;
            if (coursegroup == 0) {
                group = 0;
            } else {
                coursegroup = coursegroup.split(',');
                group = coursegroup[1];
            }

            // eslint-disable-next-line promise/catch-or-return
            ModalFactory.create({
                body: Fragment.loadFragment(
                    'local_edwiserreports',
                    'userslist',
                    1, {
                        page: 'allcoursessummary',
                        minval: _this.data("minvalue"),
                        maxval: _this.data("maxvalue"),
                        course: _this.data("courseid"),
                        cohortid: filter.cohort,
                        group: group
                    }
                )
            }).then(function(modal) {
                ModalRoot = modal.getRoot();
                modal.getBody().addClass('allcoursessummary-modal');
                ModalRoot.find('.modal-dialog').addClass('modal-lg');
                modal.setTitle(_this.data("coursename"));
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
                    modalTable = ModalTable.DataTable({
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
                        dom: '<"edwiserreports-table"i<t><"table-pagination"p>>',
                        drawCallback: function() {
                            common.stylePaginationButton(this);
                        },
                        lengthChange: false
                    });
                });
                return;
            });
        });

        // Handle report page capability manager.
        // common.handleReportCapability();
    }

    return {
        init: function() {
            $(document).ready(function() {
                init();
            });
        }
    };

});
