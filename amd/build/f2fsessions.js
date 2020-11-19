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
    /**
     * Initialize
     * @param {integer} CONTEXTID Current page context id
     */
    function init(CONTEXTID) {
        // eslint-disable-next-line no-unused-vars
        CONTEXTID = null;
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
