define(["jquery", "core/templates", "report_elucidsitereport/defaultconfig"], function($, templates, defaultConfig) {
    var panelBody = "#wdm-elucidsitereport #certificatestatsblock .panel-body"

    function init () {
        $.ajax({
            url: defaultConfig.requestUrl,
            type: 'GET',
            dataType: 'json',
            data: {
                action: 'get_certificates_data_ajax'
            },
        })
        .done(function(response) {
            templates.render('report_elucidsitereport/certificatestable', response.data)
            .then(function(html, js) {
                $(panelBody).empty();
                templates.appendNodeContents(panelBody, html, js);
                createActiveCourseTable(response.data);
            }).fail(function(ex) {
                console.log(ex);
            });
        })
        .fail(function(error) {
            console.log(error);
        });
    }

    function createActiveCourseTable() {
        certificatesTable = $("#wdm-elucidsitereport #certificatestatsblock table")
        .DataTable({
            language: {
                searchPlaceholder: "Search Certificates"
            },
            scrollY : "200px",
            scrollCollapse : true,
            fixedHeader: {
                header: true,
                headerOffset: 45
            },
            scrollX: true,
            paging: false,
            bInfo : false
        });
    }

    // Must return the init function
    return {
        init: init
    };
});