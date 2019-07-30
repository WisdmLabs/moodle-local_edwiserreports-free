define([
    'jquery',
    'core/modal_factory',
    'core/modal_events',
    'core/fragment',
    'core/templates',
    'report_elucidsitereport/variables'
], function($,
    ModalFactory,
    ModalEvents,
    Fragment,
    Templates,
    V
) {
    function init(CONTEXTID) {
        var PageId = "#wdm-f2fsessions-individual";
        var F2fTable = PageId + " .table";
        var loader = PageId + " .loader";

        function getF2fSessions() {
            $.ajax({
                url: V.requestUrl,
                type: V.requestType,
                dataType: V.requestDataType,
                data: {
                    action: 'get_f2fsession_data_ajax'
                },
            })
            .done(function(response) {
                Templates.render('report_elucidsitereport/f2fsessions', response.data)
                .then(function(html, js) {
                    Templates.replaceNode(PageId, html, js);
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
            getF2fSessions();
        });
    }

    return {
        init : init
    };
	
});