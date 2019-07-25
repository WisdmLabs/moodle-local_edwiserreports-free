define(['jquery', 'core/templates', 'report_elucidsitereport/defaultconfig', 'report_elucidsitereport/jquery.dataTables', 'report_elucidsitereport/dataTables.bootstrap4'], function ($, templates, cfg) {
    var panelBody = cfg.getPanel("#f2fsessionsblock", "body");
    var table = panelBody + " #f2fsessionstable";

    function init() {
        $.ajax({
            url: cfg.requestUrl,
            type: cfg.requestType,
            dataType: cfg.requestDataType,
            data: {
                action: 'get_f2fsession_data_ajax'
            },
        })
        .done(function(response) {
            templates.render(cfg.getTemplate('f2fsessiontable'), response.data)
            .then(function(html, js) {
                $(panelBody).empty();
                templates.appendNodeContents(panelBody, html, js);
            }).fail(function(ex) {
                console.log(ex);
            });
        })
        .fail(function(error) {
            console.log(error);
        })
    }

    // Must return the init function
    return {
        init: init
    };
});