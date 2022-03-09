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
    'local_edwiserreports/variables'
], function(
    $,
    str,
    v
) {
    /**
     * Learning Program filter
     * @type {[type]}
     */
    var filter = JSON.stringify({
        type: 'lps'
    });

    /**
     * Plugin Component
     * @type {String}
     */
    var component = 'local_edwiserreports';

    /**
     * Checkbox selector
     * @type {String}
     */
    var checkboxeSelector = '.lp-table [name^="customReportSelect-"]';

    /**
     * Download Learning program detailed reports btn selector
     * @type {String}
     */
    var downloadBtn = '#wdm-lpdetailedreport';

    /**
     * Learning Program Detailed Report form
     * @type {Object}
     */
    var reportForm = $('#wdm-lpdetailedreport-form');

    /**
     * Get translation to use strings
     * @type {object}
     */
    var translation = str.get_strings([
        {key: 'searchlps', component: component},
        {key: 'nolearningprograms', component: component},
    ]);

    // Prepare url to get selector related data
    var url = v.requestUrl + '?action=get_customreport_selectors_ajax&sesskey=' + M.cfg.sesskey + '&filter=' + filter;

    // When translation is ready
    // eslint-disable-next-line promise/catch-or-return
    translation.then(function() {
        // Datatable configurations
        var dtConfig = {
            "columns": [
                {"data": "select"},
                {"data": "fullname"},
                {"data": "shortname"},
                {"data": "startdate"},
                {"data": "enddate"},
                {"data": "duration"},
            ],
            "language": {
                "searchPlaceholder": M.util.get_string('searchlps', component),
                "emptyTable": M.util.get_string('nolearningprograms', component)
            }
        };

        // Create Datatable for modal
        $('#wdm-lplist-table').DataTable({
            ajax: url,
            columns: dtConfig.columns,
            language: dtConfig.language,
            responsive: true,
            scrollY: "250px",
            scroller: {
                loadingIndicator: true
            },
            scrollCollapse: true,
            scrollX: true,
            paging: false,
            bInfo: false,
            bSort: false
        }).columns.adjust();

        /**
         * Select all for learning program
         */
        $(document).on('change', 'input[name="selectAllCustom"]', function(event) {
            // Checked/unchecked checkboxes
            if ($(event.target).is(':checked')) {
                $(checkboxeSelector).prop("checked", true);
            } else {
                $(checkboxeSelector).prop("checked", false);
            }
        });

        /**
         * Operations on checkbox select
         */
        $(document).on('change', '.modal-body input[type="checkbox"]', function() {
            if (!$(checkboxeSelector + ":checked").length) {
                $('input[name="selectAllCustom"]').prop("checked", false);
                $(downloadBtn).hide();
            } else {
                $(downloadBtn).show();
            }
        });

        /**
         * Download reports in csv format
         */
        $(document).on('click', downloadBtn, function() {
            // If no data selected
            if (!$(checkboxeSelector + ":checked").length) {
                // eslint-disable-next-line no-undef
                $(panel).find('#errorMsg').addClass('show').removeClass('hide');
                setTimeout(function() {
                    // eslint-disable-next-line no-undef
                    $(panel).find('#errorMsg').addClass('hide').removeClass('show');
                }, 5000);
                return false;
            }

            var filters = [];
            $(checkboxeSelector + ":checked").each(function(idx, ele) {
                filters.push($(ele).data('id'));
            });

            // Set form value
            reportForm.find('input[name=filters]').val(filters.join(","));

            // Submit form on download button
            reportForm.submit();
            return true;
        });
        return;
    });
});
