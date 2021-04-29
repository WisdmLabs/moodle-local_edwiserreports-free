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
define([
    'jquery',
    'core/str',
    'local_edwiserreports/defaultconfig',
    'local_edwiserreports/variables',
    'local_edwiserreports/flatpickr',
    'local_edwiserreports/jquery.dataTables',
    'local_edwiserreports/dataTables.bootstrap4'
], function($, str, config, v) {
    /**
     * Selector datable variable
     * @type {object | null}
     */
    var selectorTable = null;

    /**
     * Get panel of custom reports block
     * @type {string}
     */
    var panel = config.getPanel("#customReportBlock");

    /**
     * Report type selector
     * @type {string}
     */
    var reportSelector = panel + ' .dropdown-menu .dropdown-item';

    /**
     * Report type selector
     * @type {string}
     */
    var selectorType = 'courses';

    /**
     * Custom report download button
     * @type {string}
     */
    var downloadBtn = panel + ' #customReportDownload';

    /**
     * Custom Report Download Dropdown
     * @type {object}
     */
    var customDropdown = $(panel).find('#customReportDrodown');

    /**
     * Custom Reports form
     * @type {object}
     */
    var reportForm = $(panel).find('#customReportsForm');

    /**
     * Plugin component
     * @type {String}
     */
    var component = 'local_edwiserreports';

    /**
     * Get checkbox selector for table.
     * @param {String} tableSelector Table id
     * @returns {String} Checkbox selector
     */
    var getCheckboxesSelector = function(tableSelector) {
        return panel + ' ' + tableSelector + ' ' + 'input[name^=customReportSelect-]';
    };

    /*
     * Get custom report selectors
     * It may be courses of learning program
     * Courses | Learning Programs
     */
    var getCustomReportSelector = function(selectedDates, dateStr, instance) {
        // If date not selected then return from here
        if (!selectedDates[0]) {
            return;
        }

        // Select date range
        if ($('#wdmCustomReportEnrolStart').is(instance.element)) {
            // Get starttime
            var startTime = selectedDates[0].getTime();

            // Set form value startdate
            reportForm.find('input[name=enrolstartdate]').val(startTime / 1000);
        } else if ($('#wdmCustomReportEnrolEnd').is(instance.element)) {
            var endTime = selectedDates[0].getTime();

            // Set form value enddate
            reportForm.find('input[name=enrolenddate]').val(endTime / 1000);
        }
    };

    /**
     * Get datatable config for report
     * @param  {string} selectorType Report Type
     * @return {object}            Datatable config
     */
    var getDatatableConfig = function(selectorType) {
        if (selectorType == 'lps') {
            return {
                "columns": [
                    {"data": "select"},
                    {"data": "fullname"},
                    {"data": "shortname"},
                    {"data": "startdate"},
                    {"data": "enddate"},
                    {"data": "duration"},
                ],
                "language": {
                    "searchPlaceholder": "Search Learning Programs",
                    "emptyTable": "There are no learning programs"
                }
            };
        } else {
            return {
                "columns": [
                    {"data": "select"},
                    {"data": "fullname"},
                    {"data": "shortname"},
                    {"data": "category"},
                    {"data": "startdate"},
                    {"data": "enddate"},
                ],
                "language": {
                    "searchPlaceholder": "Search Course",
                    "emptyTable": "There are no courses"
                }
            };
        }
    };

    /**
     * Creat Selector Table to select courses/lps
     * @param  {String} type Selector type courses/lps
     * @param  {String} tableSelectorClass Table selector class
     */
    var createSelectorTable = function(type, tableSelectorClass) {
        // If table is already there then remove table
        if (selectorTable) {
            selectorTable.destroy();
        }

        // Show enrolment date selector
        $(panel + " .enrol-selector").show();

        $(panel + ' ' + 'input[name="selectAllCustom"]').prop("checked", false);

        // Custom Report Selector Body
        var customReportSelectors = $(panel).find('.customReportSelectors');
        var rootContainer = $(panel).find('.rootContainer');

        // Prepare filter to get selectors data
        var filter = JSON.stringify({
            type: type
        });

        // Prepare url to get selector related data
        var url = v.requestUrl + '?action=get_customreport_selectors_ajax&sesskey=' + M.cfg.sesskey + '&filter=' + filter;

        // Show custom report selectors
        rootContainer.show();

        // Get dataTable config
        var dtConfig = getDatatableConfig(type);

        // Get all courses/learningprogram
        selectorTable = customReportSelectors.find(tableSelectorClass).show().DataTable({
            ajax: url,
            columns: dtConfig.columns,
            language: dtConfig.language,
            responsive: true,
            scrollY: "380px",
            scroller: {
                loadingIndicator: true
            },
            scrollCollapse: true,
            scrollX: true,
            paging: false,
            bInfo: false,
            bSort: false
        }).columns.adjust();
    };

    /**
     * Validate form inputs
     * @return {bool} status
     */
    var validateFormInputs = function() {
        var startTime = reportForm.find('input[name="enrolstartdate"]').val();
        var endTime = reportForm.find('input[name="enrolenddate"]').val();

        // If enrolment date not selected
        if (endTime == "" && startTime == "") {
            return true;
        }

        // If end time greater the start time
        if (endTime < startTime) {
            return false;
        }

        return true;
    };

    /**
     *  Create flatpicker to select custom date range
     */
    $(panel).find('.custom-flatpicker').flatpickr({
        altInput: true,
        altFormat: "d/m/Y",
        dateFormat: "Y-m-d",
        maxDate: "today",
        onClose: getCustomReportSelector
    });

    // Clear search input text
    $(document).on('click', '.custom-flatpicker ~ button.input-search-close', function() {
        $('.custom-flatpicker ~ input.form-control').val("");

        // Set form value startdate and enddate
        reportForm.find('input[name=enrolstartdate]').val("");
        reportForm.find('input[name=enrolenddate]').val("");
    });

    /**
     * Check all courses/lps
     * @param  {String} document).on('change', `${panel}   input[name [description]
     * @return {[type]}                        [description]
     */
    $(document).on('change', panel + ' input[name="selectAllCustom"]', function(event) {
        // Get table selector
        var tableSelectorClass = '.course-table';
        if (selectorType == 'lps') {
            tableSelectorClass = '.lp-table';
        }

        // Get checkboxes
        var checkboxes = getCheckboxesSelector(tableSelectorClass);
        if ($(event.target).is(':checked')) {
            $(checkboxes).prop("checked", true);
        } else {
            $(checkboxes).prop("checked", false);
        }
    });

    /**
     * Get courses/lps when dropdown selected.
     */
    $(document).on('click', reportSelector, function(event) {
        selectorType = $(event.target).data('val');

        // Set dropdown value
        customDropdown.html($(event.target).html());
        customDropdown.data('val', selectorType);

        // Get table selector
        var tableSelectorClass = '.course-table';
        if (selectorType == 'lps') {
            tableSelectorClass = '.lp-table';
        }

        // Hide table in panel body
        $(panel).find('.table').hide();

        // Set form value
        reportForm.find('input[name=reporttype]').val(selectorType);

        // Create table for selectors table
        createSelectorTable(selectorType, tableSelectorClass);
    });

    /**
     * Download reports in csv format
     */
    $(document).on('click', downloadBtn, function() {
        /**
         * Validate forms input
         */
        if (!validateFormInputs()) {
            $(panel).find('#errorMsg').html(M.util.get_string('customreportdatefailed', component))
                .addClass('show').removeClass('hide');
            setTimeout(function() {
                $(panel).find('#errorMsg').addClass('hide').removeClass('show');
            }, 5000);
            return false;
        }

        // Get table selector
        var tableSelectorClass = '.course-table';
        if (selectorType == 'lps') {
            tableSelectorClass = '.lp-table';
        }
        // Get checkboxes
        var checkboxes = getCheckboxesSelector(tableSelectorClass);
        // If no data selected
        if (!$(checkboxes + ':checked').length) {
            $(panel).find('#errorMsg').html(M.util.get_string('customreportselectfailed', component))
                .addClass('show').removeClass('hide');
            setTimeout(function() {
                $(panel).find('#errorMsg').addClass('hide').removeClass('show');
            }, 5000);
            return false;
        }

        var filters = [];
        $(checkboxes + ':checked').each(function(idx, ele) {
            filters.push($(ele).data('id'));
        });

        // Set form value
        reportForm.find('input[name=filters]').val(filters.join(","));
        return true;
    });
});
