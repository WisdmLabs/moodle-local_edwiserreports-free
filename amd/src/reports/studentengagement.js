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
 * All learner summary report page.
 *
 * @package     local_edwiserreports
 * @author      Yogesh Shirsath
 * @copyright   2022 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('local_edwiserreports/reports/studentengagement', [
    'jquery',
    'core/ajax',
    'core/notification',
    'local_edwiserreports/common',
    'local_edwiserreports/defaultconfig',
    'local_edwiserreports/select2',
    'local_edwiserreports/flatpickr'
], function(
    $,
    ajax,
    Notification,
    common,
    CFG
) {

    /**
     * Selector
     */
    var SELECTOR = {
        PAGE: '#studentengagement',
        TABLE: '#studentengagement table',
        FORMFILTER: '#studentengagement .download-links [name="filter"]',
        COHORT: '#studentengagement .cohort-select',
        COURSE: '#studentengagement .course-select',
        INACTIVE: '#studentengagement .inactive-select',
        GROUP: '#studentengagement .group-select',
        SUMMARY: '#studentengagement .summary-card',
        SEARCH: '#studentengagement .table-search-input input',
        LENGTH: '#studentengagement .length-select',
        DATE: '.edwiserreports-calendar',
        DATEMENU: '.edwiserreports-calendar + .dropdown-menu',
        DATEITEM: '.edwiserreports-calendar + .dropdown-menu .dropdown-item',
        DATEPICKER: '.edwiserreports-calendar + .dropdown-menu .dropdown-calendar',
        DATEPICKERINPUT: '.edwiserreports-calendar + .dropdown-menu .flatpickr',
    };

    /**
     * Datatable object.
     */
    var dataTable = null;

    /**
     * Filter object.
     */
    var filter = {
        inactive: 'all',
        enrolment: 'all',
    };

    let flatpickr = null;

    /**
     * All promises.
     */
    var PROMISES = {
        /**
         * Get student engagement table data based on filters.
         * @param {Object} filter Filter data
         * @returns {PROMISE}
         */
        GET_DATA: function(filter) {
            return $.ajax({
                url: CFG.requestUrl,
                type: CFG.requestType,
                dataType: CFG.requestDataType,
                data: {
                    action: 'get_studentengagement_table_data_ajax',
                    secret: M.local_edwiserreports.secret,
                    lang: $('html').attr('lang'),
                    data: JSON.stringify({
                        filter: filter
                    })
                },
            });
        },
        /**
         * Get summary card data.
         *
         * @param   {Integer}   courseid    Course id
         * @param   {String}    cohortid   Cohort id all/id
         * @param   {String}    groupid   group id all/id
         * @returns {PROMISE}
         */
        GET_SUMMARY_CARD_DATA: function(filter) {
            return ajax.call([{
                methodname: 'local_edwiserreports_get_summary_card_data',
                args: {
                    report: "\\local_edwiserreports\\blocks\\studentengagement",
                    filters: JSON.stringify(filter)
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
        //initializeDatatable();
        if (dataTable === null) {
            initializeDatatable();
            return;
        }
        dataTable.ajax.reload();
        common.updateTimeLabel(date);
    }

    /**
     * Initialize datable.
     */
    function initializeDatatable() {
        let statuses = [
            `<span class="success-tag">${M.util.get_string('active', 'local_edwiserreports')}</span>`,
            `<span class="warning-tag">${M.util.get_string('suspended', 'local_edwiserreports')}</span>`
        ];

        dataTable = $(SELECTOR.TABLE).DataTable({
            paging: true,
            processing: true,
            serverSide: true,
            rowId: 'DT_RowId',
            scrollCollapse: true,
            columnDefs: [

                { className: "fixed-column", targets: 0 },
                { className: "text-left", targets: [0, 1, 2] },
                { orderable: true, targets: [0, 1, 2, 3] },
                { className: "text-right", orderable: false, targets: "_all" }
            ],
            columns: [
                { data: "student", width: "10rem" },
                { data: "email", width: "14rem" },
                {
                    data: "status",
                    render: function(data) {
                        return statuses[data];
                    },
                    width: "4rem"
                },
                { data: "lastaccesson", width: "12rem" },
                { data: "enrolledcourses", width: "4rem" },
                { data: "inprogresscourses", width: "6rem" },
                { data: "completedcourses", width: "5rem" },
                { data: "completionprogress", width: "6rem" },
                { data: "totalgrade", width: "3rem" },
                {
                    data: "timespentonlms",
                    render: function(data) {
                        return common.timeFormatter(data);
                    },
                    width: "8rem"
                },
                {
                    data: "timespentoncourse",
                    render: function(data) {
                        return common.timeFormatter(data);
                    },
                    width: "6rem"
                },
                { data: "activitiescompleted", width: "5rem" },
                { data: "visitsoncourse", width: "5rem" },
                { data: "completedassignments", width: "6rem" },
                { data: "completedquizzes", width: "6rem" },
                { data: "completedscorms", width: "6rem" }
            ],
            dom: '<"edwiserreports-table"i<t><"table-pagination"p>>',
            language: {
                info: M.util.get_string('tableinfo', 'local_edwiserreports'),
                infoEmpty: M.util.get_string('infoempty', 'local_edwiserreports'),
                emptyTable: M.util.get_string('emptytable', 'local_edwiserreports'),
                zeroRecords: M.util.get_string('zerorecords', 'local_edwiserreports'),
                paginate: {
                    previous: "&#9666",
                    next: "&#9656"
                }
            },
            drawCallback: function() {
                common.stylePaginationButton(this);
            },
            // eslint-disable-next-line no-unused-vars
            ajax: function(data, callback, settings) {
                common.loader.show(SELECTOR.PAGE);

                // Updated export filter values.
                $(SELECTOR.FORMFILTER).val(
                    JSON.stringify({
                        'cohort': $(SELECTOR.COHORT).val(),
                        'course': $(SELECTOR.COURSE).val(),
                        'group': $(SELECTOR.GROUP).val(),
                        'inactive': $(SELECTOR.INACTIVE).val(),
                        'enrolment': filter.enrolment
                    })
                );

                // Fetching data for table.
                PROMISES.GET_DATA({
                    'cohort': $(SELECTOR.COHORT).val(),
                    'course': $(SELECTOR.COURSE).val(),
                    'group': $(SELECTOR.GROUP).val(),
                    'length': $(SELECTOR.LENGTH).val(),
                    'inactive': $(SELECTOR.INACTIVE).val(),
                    'enrolment': filter.enrolment,
                    'search': $(SELECTOR.SEARCH).val(),
                    'start': data.start,
                    'order': data.order[0]
                }).done(function(response) {
                    common.loader.hide(SELECTOR.PAGE);
                    callback(response);
                }).fail(function(ex) {
                    Notification.exception(ex);
                }).always(function() {
                    common.loader.hide(SELECTOR.PAGE);
                });
            }
        });
    }

    /**
     * Reload filters.
     */
    function reloadFilter(types, cohort, course, group) {
        common.loader.show(SELECTOR.PAGE);
        common.reloadFilter(
            SELECTOR.PAGE,
            types,
            cohort,
            course,
            group,
            function() {
                if (dataTable === null) {
                    initializeDatatable();
                    return;
                }
                dataTable.ajax.reload();
            });
    }

    /**
     * Initialize
     */
    function init() {

        // Show time period in table info.
        common.updateTimeLabel('all');

        // Initialize select2.
        $(SELECTOR.PAGE).find('.singleselect').select2();

        // Observer cohort change.
        $('body').on('change', SELECTOR.COHORT, function() {
            reloadFilter(['course', 'group'], $(SELECTOR.COHORT).val(), $(SELECTOR.COURSE).val(), $(SELECTOR.GROUP).val());
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
            //initializeDatatable();
            if (dataTable === null) {
                initializeDatatable();
                return;
            }
            dataTable.ajax.reload();
            common.updateTimeLabel(filter.enrolment);
        });

        // Observer course change event.
        $('body').on('change', SELECTOR.COURSE, function() {

            filter.course = $(SELECTOR.COURSE).val();

             // Fetching summary card data here
            PROMISES.GET_SUMMARY_CARD_DATA(filter)
            .done(function(response) {
                response = JSON.parse(response);
                common.refreshSummarycard('group', response, SELECTOR.SUMMARY, function() {
                //     initializeDatatable();
                });
            });


            // Check if all courses is selected.
            var allCourses = $(this).val() == 0;

            // Hide enrolled courses.
            dataTable.column(4).visible(allCourses);

            // Hide In-progress courses
            dataTable.column(5).visible(allCourses);

            // Completed courses.
            dataTable.column(6).visible(allCourses);
            reloadFilter(['group'], $(SELECTOR.COHORT).val(), $(SELECTOR.COURSE).val(), $(SELECTOR.GROUP).val());
        });

        // Observer group change.
        $('body').on('change', SELECTOR.GROUP, function() {
            filter.group = $(SELECTOR.GROUP).val();
             // Fetching summary card data here
             PROMISES.GET_SUMMARY_CARD_DATA(filter)
             .done(function(response) {
                 response = JSON.parse(response);
                 common.refreshSummarycard('group', response, SELECTOR.SUMMARY, function() {
                 //     initializeDatatable();
                 });
             });
            if (dataTable === null) {
                initializeDatatable();
                return;
            }
            dataTable.ajax.reload();
        });

        // Search in table.
        $('body').on('input', SELECTOR.SEARCH, function() {
            dataTable.search(this.value).draw();
        });

        // Observer INACTIVE change.
        $('body').on('change', SELECTOR.INACTIVE, function() {
            filter.inactive = $(this).val();

            if (dataTable === null) {
                initializeDatatable();
                return;
            }
            dataTable.ajax.reload();
        });

        // Observer length change.
        $('body').on('change', SELECTOR.LENGTH, function() {
            if (dataTable === null) {
                initializeDatatable();
                return;
            }
            dataTable.page.len(this.value);
            dataTable.ajax.reload();
        });

        initializeDatatable();

        common.handleSearchInput();
    }

    return {
        init: function() {
            $(document).ready(function() {
                init();
            });
        }
    };

});
