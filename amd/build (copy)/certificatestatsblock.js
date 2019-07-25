define(["jquery", "core/templates", "report_elucidsitereport/defaultconfig"], function($, templates, cfg) {
    var panel = cfg.getPanel("#certificatestatsblock");
    var panelBody = cfg.getPanel("#certificatestatsblock", "body");
    var table = panel + " .table";

    function init () {
        $.ajax({
            url: cfg.requestUrl,
            type: cfg.requestType,
            dataType: cfg.requestDataType,
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
        certificatesTable = $(table).DataTable({
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