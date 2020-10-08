define([
    'jquery',
    'core/templates',
    'local_edwiserreports/defaultconfig',
    './common'
], function ($, templates, cfg, common) {
    var panel = cfg.getPanel("#siteaccessblock");
    var panelBody = cfg.getPanel("#siteaccessblock", "body");
    var table = cfg.getPanel("#siteaccessblock", "table");
    var loader = cfg.getPanel("#siteaccessblock", "loader");
    var accessDesc = "#accessinfodesctable";

    function init(notifyListner) {
        $(document).ready(function() {
            generateAccessInfoGraph();
        });

        function generateAccessInfoGraph() {

            // Show loader.
            common.loader.show('#siteaccessblock');
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
                templates.render(cfg.getTemplate("siteaccessblock"), response.data)
                .then(function(html, js) {
                    templates.replaceNodeContents(panelBody, html, js);
                })
                .fail(function(ex) {
                    console.log(ex);
                })
                .always(function() {
                    $(accessDesc).show();
                    $(loader).remove();
                    $(table).removeClass("d-none");
                    $(panel + ' [data-toggle="tooltip"]').tooltip();

                    // Hide loader.
                    common.loader.hide('#siteaccessblock');
                });
            })
            .fail(function(error) {
                console.log(error);
            }).always(function() {
                notifyListner("accessInfo");

                // Hide loader.
                common.loader.hide('#siteaccessblock');
            });
        }
    }

    // Must return the init function
    return {
        init: init
    };
});
