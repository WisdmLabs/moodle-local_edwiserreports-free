define(['jquery', 'core/templates', 'report_elucidsitereport/defaultconfig'], function ($, templates, defaultConfig) {
    var panelBody = "#f2fsessionsblock .panel-body"

    function init() {
        $.ajax({
            url: defaultConfig.requestUrl,
            type: 'GET',
            dataType: 'json',
            data: {
                action: 'get_f2fsession_data_ajax'
            },
        })
        .done(function(response) {
            templates.render('report_elucidsitereport/f2fsessiontable', response.data)
            .then(function(html, js) {
                $(panelBody).empty();
                templates.appendNodeContents(panelBody, html, js);
            }).fail(function(ex) {
                console.log(ex);
            });
            console.log(response.data);
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
    }

    function renderF2fsessions(data) {

    }

    // Must return the init function
    return {
        init: init
    };
});