define([
    'jquery',
    'core/templates',
    'report_elucidsitereport/defaultconfig'
], function ($, templates, cfg) {
    var panel = cfg.getPanel("#siteaccesssblock");
    var panelBody = cfg.getPanel("#siteaccesssblock", "body");
    var table = cfg.getPanel("#siteaccesssblock", "table");
    var loader = cfg.getPanel("#siteaccesssblock", "loader");
    var accessDesc = "#accessinfodesctable";

    function init(notifyListner) {
        $(document).ready(function() {
            generateAccessInfoGraph();
        });

        function generateAccessInfoGraph() {
            $.ajax({
                url: cfg.requestUrl,
                type: cfg.requestType,
                dataType: cfg.requestDataType,
                data: {
                    action: 'get_siteaccess_data_ajax',
                    sesskey: $(panel).data("sesskey")
                },
            })
            .done(function(response) {
                templates.render(cfg.getTemplate("block_accessinfo"), response.data)
                .then(function(html, js) {
                    templates.replaceNode(panel, html, js);
                })
                .fail(function(ex) {
                    console.log(ex);
                })
                .always(function() {
                    $(accessDesc).show();
                    $(loader).remove();
                    $(table).removeClass("d-none");
                    $(panel + ' [data-toggle="tooltip"]').tooltip();
                });
            })
            .fail(function(error) {
                console.log(error);
            }).always(function() {
                notifyListner("accessInfo");
            });
        }
    }

    // Must return the init function
    return {
        init: init
    };
});