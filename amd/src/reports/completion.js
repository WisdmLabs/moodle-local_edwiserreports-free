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
 * Course Completion report page.
 *
 * @package     local_edwiserreports
 * @author      Yogesh Shirsath
 * @copyright   2022 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('local_edwiserreports/reports/completion', [
    'jquery',
    'core/ajax',
    'core/notification',
    'local_edwiserreports/common',
    'local_edwiserreports/defaultconfig',
    'local_edwiserreports/select2',
    'local_edwiserreports/flatpickr'
], function($, Ajax, Notification, common, CFG) {

    /**
     * Selectors list.
     */
    var SELECTOR = {
        PAGE: ".report-content",
        EXPORT: ".report-export",
        COHORT: ".report-content .cohort-select",
        COURSE: ".report-content .course-select",
        GROUP: ".report-content .group-select",
        EXCLUDE: '.report-content .exclude-select',
        INACTIVE: '.report-content .inactive-select',
        PROGRESS: '.report-content .progress-select',
        GRADE: '.report-content .grade-select',
        LENGTH: ".report-content .length-select",
        SEARCH: ".report-content .table-search-input input",
        PAGETITLE: ".report-header .page-title h2",
        FORMFILTER: '.report-content .download-links [name="filter"]',
        DATE: '.edwiserreports-calendar',
        DATEMENU: '.edwiserreports-calendar + .dropdown-menu',
        DATEITEM: '.edwiserreports-calendar + .dropdown-menu .dropdown-item',
        DATEPICKER: '.edwiserreports-calendar + .dropdown-menu .dropdown-calendar',
        DATEPICKERINPUT: '.edwiserreports-calendar + .dropdown-menu .flatpickr',
        SUMMARY: '#wdm-completion-individual .summary-card'
    };

    /**
     * Filter object.
     */
    var filter = {
        cohort: 0,
        course: null,
        group: 0,
        exclude: [],
        enrolment: 'all',
        inactive: 'all',
        progress: 'all',
        grade: 'all',
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
     * Promise lists.
     */
    let PROMISES = {
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
                    action: 'get_completion_data_ajax',
                    sesskey: M.local_edwiserreports.secret,
                    lang: $('html').attr('lang'),
                    data: JSON.stringify(filter)
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
        GET_SUMMARY_CARD_DATA: function(courseid, cohortid, groupid) {
            return Ajax.call([{
                methodname: 'local_edwiserreports_get_summary_card_data',
                args: {
                    report: 'completionblock',
                    course: courseid,
                    cohort: cohortid,
                    group: groupid
                }
            }], false)[0];
        }
    };

    /**
     * After Select Custom date get active users details.
     */
    function customDateSelected() {
        let date = $(SELECTOR.DATEPICKERINPUT).val(); // Y-m-d format
        let dateAlternate = $(SELECTOR.DATEPICKERINPUT).next().val().replace("to", "-"); // D M Y format
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
        common.updateTimeLabel(date);
    }

    /**
     * Get Course Completion
     */
    function initializeDatatable() {
        common.loader.show(SELECTOR.PAGE);

        $(SELECTOR.PAGE).find('.download-links input[name="filter"]').val(JSON.stringify(filter));
        PROMISES.GET_DATA(filter).done(function(response) {
            if (dataTable !== null) {
                dataTable.destroy();
            }
            $(SELECTOR.PAGETITLE).text(response.name);
            dataTable = $(SELECTOR.PAGE).find(".table").DataTable({
                dom: '<"edwiserreports-table"<"table-filter d-flex"i><t><"table-pagination"p>>',
                data: response.data,
                pageLength: $(SELECTOR.LENGTH).val(),
                deferRendering: true,
                language: {
                    info: M.util.get_string('tableinfo', 'local_edwiserreports'),
                    infoEmpty: M.util.get_string('infoempty', 'local_edwiserreports'),
                    emptyTable: M.util.get_string('nostudentsenrolled', 'local_edwiserreports'),
                    zeroRecords: M.util.get_string('zerorecords', 'local_edwiserreports'),
                    paginate: {
                        previous: "&#9666",
                        next: "&#9656"
                    }
                },
                columnDefs: [
                    {className: "fixed-column", targets: 0},
                    {className: "text-left", targets: [0, 1, 2]},
                    {className: "text-right", targets: "_all"}
                ],
                columns: [
                    {data: "username"},
                    {data: "enrolledon"},
                    {data: "enrolltype"},
                    {data: "noofvisits"},
                    {data: "completion"},
                    {data: "compleiontime"},
                    {data: "grade"},
                    {data: "lastaccess"}
                ],
                drawCallback: function() {
                    common.stylePaginationButton(this);
                },
                initComplete: function() {
                    common.loader.hide(SELECTOR.PAGE);
                }
            });
            dataTable.columns(0).search($(SELECTOR.SEARCH).val());
            dataTable.page.len($(SELECTOR.LENGTH).val()).draw();
        }).fail(function(ex) {
            Notification.exception(ex);
            common.loader.hide(SELECTOR.PAGE);
        });
    }

    /**
     * Reload filters.
     * @param {array} types Types
     * @param {number} cohort Cohort id
     * @param {number} course Course id
     * @param {function} callback Callback function
     */
    function reloadFilter(types, cohort, course, callback) {
        common.loader.show(SELECTOR.PAGE);
        common.reloadFilter(
            SELECTOR.PAGE,
            types,
            cohort,
            course,
            0,
            callback
        );
    }

    /* eslint-disable no-unused-vars */
    /**
     * Initialize
     * @param {integer} CONTEXTID Current page context id
     */
    function init(CONTEXTID) {

        // Show time period in table info.
        common.updateTimeLabel('all');


        common.handleSearchInput();

        // Initialize select2.
        $(SELECTOR.PAGE).find('.singleselect').not(SELECTOR.EXCLUDE).select2();
        $(SELECTOR.EXCLUDE).select2({
            placeholder: M.util.get_string('exclude', 'local_edwiserreports'),
            allowClear: true
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
            common.updateTimeLabel(filter.enrolment);
        });

        // Get course id
        filter.course = $(SELECTOR.COURSE).val();

        // Select cohort filter for completion table.
        $('body').on('change', SELECTOR.COHORT, function() {
            filter.cohort = $(this).val();
            filter.group = 0;
            reloadFilter(['course', 'noallcourses'], filter.cohort, filter.course, function() {
                if ($(SELECTOR.COURSE).find(`option[value="${filter.course}"]`).length == 0) {
                    filter.course = $(SELECTOR.COURSE).find(`option:first`).attr('value');
                }
                reloadFilter(['group'], filter.cohort, filter.course, function() {
                    initializeDatatable();
                });
            });
        });

        // Select course filter for completion table.
        $('body').on('change', SELECTOR.COURSE, function() {
            filter.course = $(this).val();
            filter.group = 0;

            PROMISES.GET_SUMMARY_CARD_DATA(filter.course, filter.cohort, filter.group)
            .done(function(response) {
                response = JSON.parse(response);
                common.refreshSummarycard('group', response, SELECTOR.SUMMARY, function() {
                //     InitializeDatatable();
                });
            });


            reloadFilter(['group'], filter.cohort, filter.course, function() {
                initializeDatatable();
            });
        });

        // Select group filter for completion table.
        $('body').on('change', SELECTOR.GROUP, function() {
            filter.group = $(this).val();
            initializeDatatable();

        });

        // Observer length change.
        $('body').on('change', SELECTOR.LENGTH, function() {
            dataTable.page.len(this.value).draw();
        });

        // Search in table.
        $('body').on('input', SELECTOR.SEARCH, function() {
            dataTable.column(0).search(this.value).draw();
        });

        // Observer exclude change.
        $('body').on('change', SELECTOR.EXCLUDE, function() {
            filter.exclude = $(this).val();
            $(this).toggleClass('notselected', filter.exclude.length == 0);
            $(this).toggleClass('selected', filter.exclude.length != 0);
            // Display none to inactive users if exclude is selected.
            $(SELECTOR.INACTIVE).closest('.filter-selector')
                .toggle(filter.exclude.indexOf('2') == -1 && filter.exclude.indexOf('3') == -1);
            initializeDatatable();
        });

        // Observer progress change.
        $('body').on('change', SELECTOR.PROGRESS, function() {
            filter.progress = $(this).val();
            initializeDatatable();
        });

        // Observer inactive users change.
        $('body').on('change', SELECTOR.INACTIVE, function() {
            filter.inactive = $(this).val();
            initializeDatatable();
        });

        // Observer grade change.
        $('body').on('change', SELECTOR.GRADE, function() {
            filter.grade = $(this).val();
            initializeDatatable();
        });

        initializeDatatable();
    }

    return {
        init: function(CONTEXTID) {
            $(document).ready(function() {
                init(CONTEXTID);
            });
        }
    };

});
