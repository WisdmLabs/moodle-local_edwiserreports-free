define(['jquery', 'core/templates', 'report_elucidsitereport/defaultconfig'], function ($, templates, defaultConfig) {
    var panel = defaultConfig.getPanel("#accessinfograph");
    var panelBody = defaultConfig.getPanel("#accessinfograph", "body");
    var table = defaultConfig.getPanel("#accessinfograph", "table");
    var loader = defaultConfig.getPanel("#accessinfograph", "loader");;

    function init() {
        $.ajax({
            url: defaultConfig.requestUrl,
            type: defaultConfig.requestType,
            dataType: defaultConfig.requestDataType,
            data: {
                action: 'get_siteaccess_data_ajax'
            },
        })
        .done(function(response) {
            templates.render(defaultConfig.getTemplate("block_accessinfo"), response.data)
            .then(function(html, js) {
                templates.replaceNodeContents(panel, html, js);
            })
            .fail(function(ex) {
                console.log(ex);
            })
            .always(function() {
                $(loader).remove();
                $(table).removeClass("d-none");
            });
        })
        .fail(function(error) {
            console.log(error);
        });
    }

    // Must return the init function
    return {
        init: init
    };
});