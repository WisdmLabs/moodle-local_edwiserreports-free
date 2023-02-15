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
 * Learner course progress report page.
 *
 * @package     local_edwiserreports
 * @author      Yogesh Shirsath
 * @copyright   2022 Wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('local_edwiserreports/reports/learnercourseactivities', [
    'jquery',
    'core/notification',
    'core/ajax',
    'local_edwiserreports/common',
    'local_edwiserreports/defaultconfig',
    'local_edwiserreports/select2',
    'local_edwiserreports/flatpickr'
], function(
    $,
    Notification,
    Ajax,
    common,
    CFG
) {

    /**
     * Selector
     */
    var SELECTOR = {
        PAGE: '#learnercourseactivities',
        SEARCH: '#learnercourseactivities .table-search-input input',
        COURSE: '#learnercourseactivities .course-select',
        STUDENT: '#learnercourseactivities .student-select',
        SECTION: '#learnercourseactivities .section-select',
        MODULE: '#learnercourseactivities .module-select',
        SUMMARY: '#learnercourseactivities .summary-card',
        COMPLETION: '#learnercourseactivities .completion-select',
        LENGTH: '#learnercourseactivities .length-select',
        LEARNER: '#learnercourseactivities .student-select',
        TABLE: '#learnercourseactivities table',
        FORMFILTER: '#learnercourseactivities .download-links [name="filter"]'
    };

    /**
     * Datatable object.
     */
    var dataTable = null;

    /**
     * Filter object.
     */
    var filter = {
        learner: null,
        course: null,
        section: 0,
        module: 'all',
        completion: 'all'
    };

    /**
     * All promises.
     */
    var PROMISE = {
        /**
         * Get learner course activities table data based on filters.
         * @param {Object} filter Filter data
         * @returns {PROMISE}
         */
        GET_DATA: function(filter) {
            return $.ajax({
                url: CFG.requestUrl,
                type: CFG.requestType,
                dataType: CFG.requestDataType,
                data: {
                    action: 'get_learnercourseactivities_data',
                    secret: M.local_edwiserreports.secret,
                    lang: $('html').attr('lang'),
                    data: JSON.stringify(filter)
                },
            });
        },
        /**
         * Get filter data.
         *
         * @param   {Array}     types       Type of filters to get
         * @param   {Integer}   courseid    Course id
         * @param   {String}    sectionid   Section id all/id
         * @returns {PROMISE}
         */
        GET_FILTER_DATA: function(types, courseid, sectionid) {
            return Ajax.call([{
                methodname: 'local_edwiserreports_get_filter_data',
                args: {
                    types: types,
                    course: courseid,
                    section: sectionid
                }
            }], false)[0];
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
            return Ajax.call([{
                methodname: 'local_edwiserreports_get_summary_card_data',
                args: {
                    report: "\\local_edwiserreports\\reports\\learnercourseactivities",
                    filters: JSON.stringify(filter)
                }
            }], false)[0];
        }
    }

    /**
     * Initialize datable.
     */
    function initializeDatatable() {
        common.loader.show(SELECTOR.PAGE);

        // Updated export filter values.
        $(SELECTOR.FORMFILTER).val(JSON.stringify(filter));
        let statuses = [
            `<span class="warning-tag">${M.util.get_string('notyetstarted', 'core_completion')}</span>`,
            `<span class="success-tag">${M.util.get_string('completed', 'core_completion')}</span>`,
            `<span class="danger-tag">${M.util.get_string('inprogress', 'core_completion')}</span>`
        ];
        PROMISE.GET_DATA(filter)
            .done(function(response) {
                if (dataTable !== null) {
                    dataTable.destroy();
                    dataTable = null;
                }
                let never = M.util.get_string('never', 'local_edwiserreports');
                dataTable = $(SELECTOR.TABLE).DataTable({
                    data: response,
                    paging: true,
                    deferRendering: true,
                    columnDefs: [
                        { className: "fixed-column", targets: 0 },
                        { className: "text-left", targets: [0, 1] },
                        { className: "text-right", targets: "_all" }
                    ],
                    columns: [
                        { data: 'activity', width: "14rem" },
                        { data: 'type' },
                        {
                            data: 'status',
                            render: function(data) {
                                return statuses[data];
                            },
                            width: "4rem"
                        },
                        {
                            data: 'completedon',
                            render: function(data) {
                                return `<span class="d-none">${data}</span>` +
                                    (data == 0 ? '-' : common.formatDate(new Date(data * 1000), "d MMM yyyy"));
                            }
                        },
                        { data: 'grade' },
                        {
                            data: 'gradedon',
                            render: function(data) {
                                return `<span class="d-none">${data}</span>` +
                                    (data == 0 ? '-' : common.formatDate(new Date(data * 1000), "d MMM yyyy"));
                            }
                        },
                        { data: 'attempts' },
                        { data: 'highestgrade', width: "4rem" },
                        { data: 'lowestgrade', width: "4rem" },
                        {
                            data: 'firstaccess',
                            render: function(data) {
                                return `<span class="d-none">${data}</span>` +
                                    (data == 0 ? never : common.formatDate(new Date(data * 1000), "d MMM yyyy hh:mm TT"));
                            },
                            width: "6rem"
                        },
                        {
                            data: 'lastaccess',
                            render: function(data) {
                                return `<span class="d-none">${data}</span>` +
                                    (data == 0 ? never : common.formatDate(new Date(data * 1000), "d MMM yyyy hh:mm TT"));
                            },
                            width: "6rem"
                        },
                        { data: 'visits' },
                        {
                            data: 'timespent',
                            render: function(data) {
                                return common.timeFormatter(data);
                            }
                        },
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
     * Initialize
     */
    function init() {

        filter = JSON.parse($(SELECTOR.FORMFILTER).val());

        // Initialize select2.
        $(SELECTOR.COURSE).select2({
            templateResult: function(state) {
                if (!state.id) {
                    return state.text;
                }
                var $state = $(
                    '<span class="pl-3 d-block">' + state.text + '</span>'
                );
                return $state;
            }
        });

        $(SELECTOR.PAGE).find('.singleselect').not(SELECTOR.COURSE).select2();

        // Observer course change.
        $('body').on('change', SELECTOR.COURSE, function() {
            filter.course = $(this).val();
            filter.section = 0;
            filter.module = 'all';

            // Fetching summary card data here
            PROMISE.GET_SUMMARY_CARD_DATA(filter)
            .done(function(response) {
                response = JSON.parse(response);
                common.refreshSummarycard('group', response, SELECTOR.SUMMARY, function() {
                //     initializeDatatable();
                });
            });

            filter.learner = null;


            // common.loader.show(SELECTOR.PAGE);
            common.reloadFilter(
                SELECTOR.PAGE, ['student', 'section', 'module', 'noallusers'],
                0,
                filter.course,
                0,
                function() {
                    if ($(SELECTOR.LEARNER).find('option:first-child').length) {
                        filter.learner = $(SELECTOR.LEARNER).find('option:first-child').attr('value');
                    }
                    initializeDatatable();
                });
        });

        // Observer learner change.
        $('body').on('change', SELECTOR.LEARNER, function() {
            filter.learner = $(this).val();

            // Fetching summary card data here
            PROMISE.GET_SUMMARY_CARD_DATA(filter)
            .done(function(response) {
                response = JSON.parse(response);
                common.refreshSummarycard('group', response, SELECTOR.SUMMARY, function() {
                //     initializeDatatable();
                });
            });


            initializeDatatable();
        });

        // Observer section change.
        $('body').on('change', SELECTOR.SECTION, function() {
            filter.section = $(this).val();
            filter.module = 'all';

            // Fetching summary card data here
            PROMISE.GET_SUMMARY_CARD_DATA(filter)
            .done(function(response) {
                response = JSON.parse(response);
                common.refreshSummarycard('group', response, SELECTOR.SUMMARY, function() {
                //     initializeDatatable();
                });
            });

            PROMISE.GET_FILTER_DATA(['module'], filter.course, filter.section)
                .done(function(response) {
                    response = JSON.parse(response);
                    common.refreshFilter('module', response.module, SELECTOR.PAGE, function() {
                        initializeDatatable();
                    });
                });
            initializeDatatable();
        });

        // Observer module change.
        $('body').on('change', SELECTOR.MODULE, function() {
            filter.module = $(this).val();
            initializeDatatable();
        });

        // Observer completion change.
        $('body').on('change', SELECTOR.COMPLETION, function() {
            filter.completion = $(this).val();
            initializeDatatable();
        });

        // Search in table.
        $('body').on('input', SELECTOR.SEARCH, function() {
            dataTable.column(0).search(this.value).draw();
        });

        // Observer length change.
        $('body').on('change', SELECTOR.LENGTH, function() {
            dataTable.page.len(this.value).draw();
        });

        initializeDatatable();
        common.handleSearchInput();

        // Handle report page capability manager.
        common.handleReportCapability();
    }

    return {
        init: function() {
            $(document).ready(function() {
                init();
            });
        }
    };

});