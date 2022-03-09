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
    'core/templates',
    'core/fragment',
    'core/modal_factory',
    'core/modal_events',
    'core/str',
    'local_edwiserreports/variables',
    'local_edwiserreports/common'
], function(
    $,
    Templates,
    Fragment,
    ModalFactory,
    ModalEvents,
    str,
    V
) {
    /**
     * Initialize
     * @param {integer} CONTEXTID Current page context id
     */
    function init(CONTEXTID) {
        var PageId = "#wdm-lpstats-individual";
        var LpSelect = "#wdm-lp-select";
        var LpTable = PageId + " .table";
        var loader = PageId + " .loader";
        var filterSection = $("#wdm-userfilter .row .col-md-6:first-child");
        var LpDropdown = $(PageId).find("#wdm-lp-dropdown");
        var Table = null;

        /**
         * Learning program export detailed report button
         * @type {[type]}
         */
        var lpExportDetail = $("#wdm-export-detail-lpsreports");
        var lpListModal = null;

        /**
         * Plugin Component
         * @type {String}
         */
        var component = 'local_edwiserreports';

        /**
         * Get translation to use strings
         * @type {object}
         */
        var translation = str.get_strings([
            {key: 'lpdetailedreport', component: component}
        ]);

        // Varibales for cohort filter
        var cohortId = 0;

        $(document).ready(function() {
            filterSection.html(LpDropdown.html());
            $(document).find(LpSelect).select2();
            $(document).find(LpSelect).show();
            LpDropdown.remove();

            var lpid = $(document).find(LpSelect).val();
            addLpStatsTable(lpid, cohortId);

            /* Select cohort filter for active users block */
            $(V.cohortFilterItem).on('click', function() {
                cohortId = $(this).data('cohortid');
                $(V.cohortFilterBtn).html($(this).text());
                V.changeExportUrl(cohortId, V.exportUrlLink, V.cohortReplaceFlag);
                addLpStatsTable(lpid, cohortId);
            });

            $(document).find(LpSelect).on("change", function() {
                $(LpTable).hide();
                $(loader).show();

                lpid = $(document).find(LpSelect).val();
                V.changeExportUrl(lpid, V.exportUrlLink, V.filterReplaceFlag);
                addLpStatsTable(lpid, cohortId);
            });

            /* Export Detailed Report */
            lpExportDetail.on('click', function() {
                exportDetailedReport(lpExportDetail);
            });
        });

        /**
         * Export Detailed Report
         * @param {Objcet} trigger Trigger dom element
         */
        function exportDetailedReport(trigger) {
            // If modal already exist then show modal
            if (lpListModal) {
                lpListModal.show();
            } else {
                // When translation is redy then create modal
                // eslint-disable-next-line promise/catch-or-return
                translation.then(function() {
                    // Create Learning Program Modal
                    ModalFactory.create({
                        title: M.util.get_string(
                            'lpdetailedreport', component
                        )
                    }, trigger).done(function(modal) {
                        // Get modal root
                        var root = modal.getRoot();

                        // Set global Modal
                        lpListModal = modal;
                        root.on(ModalEvents.cancel, function() {
                            modal.hide();
                        });

                        // Set Modal Body
                        modal.setBody(Templates.render(
                            'local_edwiserreports/lpdetailedreport', {
                            sesskey: $(PageId).data('sesskey'),
                            formaction: M.cfg.wwwroot + "/local/edwiserreports/download.php"
                        }
                        ));

                        // Show learning program modal
                        modal.show();
                    });
                    return;
                });
            }
        }

        /**
         * Add Lp stats table in learning program page
         * @param {int} lpid     Learning Program ID
         * @param {int} cohortId Cohort ID
         */
        function addLpStatsTable(lpid, cohortId) {
            if (Table) {
                Table.destroy();
                $(LpTable).hide();
                $(loader).show();
            }

            var fragment = Fragment.loadFragment(
                'local_edwiserreports',
                'lpstats',
                CONTEXTID,
                {
                    lpid: lpid,
                    cohortid: cohortId
                }
            );

            fragment.done(function(response) {
                var context = JSON.parse(response);
                // eslint-disable-next-line promise/catch-or-return
                Templates.render('local_edwiserreports/lpstatsinfo', context)
                .then(function(html, js) {
                    Templates.replaceNode(LpTable, html, js);
                    return;
                }).fail(function(ex) {
                    console.log(ex);
                }).always(function() {
                    $(LpTable).show();
                    Table = $(LpTable).DataTable({
                        dom: "<'pull-left'f><t><p>",
                        oLanguage: {
                            sEmptyTable: "No Users are enrolled in any Learning Programs"
                        },
                        responsive: true
                    });
                    $(loader).hide();
                });
            });
        }
    }

    return {
        init: init
    };

});
