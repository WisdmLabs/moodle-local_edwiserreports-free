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
    'core/fragment',
    'core/templates',
    'local_edwiserreports/variables',
    'local_edwiserreports/common'
], function(
    $,
    ModalFactory,
    ModalEvents,
    Fragment,
    Templates,
    V
) {
    /* eslint-disable no-unused-vars */
    /**
     * Initialize
     * @param {integer} CONTEXTID Current page context id
     */
    function init(CONTEXTID) {
    /* eslint-enable no-unused-vars */
        var PageId = "#wdm-f2fsessions-individual";
        var F2fTable = PageId + " .table";
        var loader = PageId + " .loader";
        var sesskey = $(PageId).data("sesskey");

        // Varibales for cohort filter
        var cohortId = 0;
        var cohortFilterBtn = "#cohortfilter";
        var cohortFilterItem = cohortFilterBtn + " ~ .dropdown-menu .dropdown-item";

        /**
         * Get face 2 faqce sessions
         * @param {Number} cohortId Cohort id
         */
        function getF2fSessions(cohortId) {
            $.ajax({
                url: V.requestUrl,
                type: V.requestType,
                dataType: V.requestDataType,
                data: {
                    action: 'get_f2fsession_data_ajax',
                    sesskey: sesskey,
                    data: JSON.stringify({
                        cohortid: cohortId
                    })
                },
            })
                .done(function(response) {
                    var context = response.data;
                    context.sesskey = sesskey;

                    // eslint-disable-next-line promise/catch-or-return
                    Templates.render('local_edwiserreports/f2fsessions', context)
                    .then(function(html, js) {
                        Templates.replaceNode(PageId, html, js);
                        return;
                    }).fail(function(ex) {
                        console.log(ex);
                    }).always(function() {
                        $(F2fTable).show();
                        $(loader).hide();
                    });
                })
                .fail(function(error) {
                    console.log(error);
                });
        }

        $(document).ready(function() {
            getF2fSessions(cohortId);

            /* Select cohort filter for active users block */
            $(cohortFilterItem).on('click', function() {
                $(F2fTable).hide();
                $(loader).show();

                cohortId = $(this).data('cohortid');
                V.changeExportUrl(cohortId, V.exportUrlLink, V.cohortReplaceFlag);
                $(cohortFilterBtn).html($(this).text());
                getF2fSessions(cohortId);
            });
        });
    }

    return {
        init: init
    };

});
