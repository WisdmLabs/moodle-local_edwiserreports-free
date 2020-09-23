define(["jquery", "core/templates", "local_sitereport/defaultconfig"], function($, templates, cfg) {
    var panel = cfg.getPanel("#certificatesblock");
    var panelBody = cfg.getPanel("#certificatesblock", "body");
    var table = panel + " .table";
    var dropdownBody = panel + " .table-dropdown";

    function init () {
        $.ajax({
            url: cfg.requestUrl,
            type: cfg.requestType,
            dataType: cfg.requestDataType,
            data: {
                action: 'get_certificates_data_ajax',
                sesskey: $(panel).data("sesskey")
            },
        })
        .done(function(response) {
            templates.render('local_sitereport/certificatestable', response.data)
            .then(function(html, js) {
                $(panelBody).empty();
                templates.appendNodeContents(panelBody, html, js);
                createCertificatesTable(response.data);
            }).fail(function(ex) {
                console.log(ex);
            });
        })
        .fail(function(error) {
            console.log(error);
        });
    }

    function createCertificatesTable() {
        certificatesTable = $(table).DataTable({
            // dom : '<"pull-left"f><t>',
            language: {
                searchPlaceholder: "Search Certificates"
            },
            oLanguage : {
                sEmptyTable : "There is no certificate created"
            },
            initComplete: function() {
                $(dropdownBody).show();
            },
            drawCallback: function () {
                $('.dataTables_paginate > .pagination').addClass('pagination-sm pull-right');
                $('.dataTables_filter').addClass('pagination-sm pull-right');
            },
            // scrollY : "200px",
            // scrollCollapse : true,
            // fixedHeader: {
            //     header: true,
            //     headerOffset: 45
            // },
            // scrollX: true,
            // paging: false,
            lengthChange: false,
            bInfo : false
        });
    }

    // Must return the init function
    return {
        init: init
    };
});