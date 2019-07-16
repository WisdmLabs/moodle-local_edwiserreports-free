define(['jquery', 'core/templates', 'report_elucidsitereport/defaultconfig'], function ($, templates, defaultConfig) {
    var panelBody = "#wdm-elucidsitereport #accessinfograph .panel-body";
    var table = panelBody + " .table";
    var loader = panelBody + " .loader";

    function init() {
        $.ajax({
            url: defaultConfig.requestUrl,
            type: 'GET',
            dataType: 'json',
            data: {
                action: 'get_siteaccess_data_ajax'
            },
        })
        .done(function(response) {
            console.log(response.data);
            templates.render('report_elucidsitereport/accessinfograph', response.data)
            .then(function(html, js) {
                $("#accessinfograph").empty();
                templates.appendNodeContents("#accessinfograph", html, js);
            }).fail(function(ex) {
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