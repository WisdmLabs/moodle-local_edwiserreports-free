define([
    'jquery',
    'core/modal_factory',
    'core/modal_events',
    'core/fragment',
    'core/templates',
    'report_elucidsitereport/variables',
    'report_elucidsitereport/select2',
    'report_elucidsitereport/jquery.dataTables',
    'report_elucidsitereport/dataTables.bootstrap4',
    'report_elucidsitereport/jquery-asPieProgress'
], function($, ModalFactory, ModalEvents, Fragment, Templates, V) {
    function init(CONTEXTID) {
        var PageId = "#wdm-certificates-individual";
        var CertTable = PageId + " .table";
        var loader = PageId + " .loader";
        var CertSelect = "#wdm-certificates-select";
        var Table = null;

        function getCertificateDetail(certificateid) {
            $.ajax({
                url: V.requestUrl,
                data: {
                    action: 'get_certificates_data_ajax',
                    data: JSON.stringify({
                        certificateid : certificateid
                    })
                },
            }).done(function(response) {
                Table = $(CertTable).DataTable({
                    data : response,
                    columnDefs: [
                        {
                            "targets": 0,
                            "className": "align-middle"
                        },
                        {
                            "targets": 1,
                            "className": "align-middle"
                        },
                        {
                            "targets": 2,
                            "className": "align-middle text-center"
                        },
                        {
                            "targets": 3,
                            "className": "align-middle text-center"
                        },
                        {
                            "targets": 4,
                            "className": "align-middle text-center"
                        },
                        {
                            "targets": 5,
                            "className": "align-middle text-center"
                        },
                    ],
                    initComplete: function(settings, json) {
                        $('.pie-progress').asPieProgress({
                            namespace: 'pie_progress'
                        });

                        $(loader).hide();
                        $(CertTable).show();
                    }
                });
                console.log(response);
            }).fail(function(error) {
                console.log(error);
            });

        }

        $(document).ready(function() {
            $(CertSelect).select2();

            var certificateid = $(CertSelect).val();
            getCertificateDetail(certificateid);

            $(CertSelect).on("change", function() {
                if (Table) {
                    Table.destroy();
                }

                $(loader).show();
                $(CertTable).hide();
                getCertificateDetail($(this).val());
            });
        });
    }

    return {
        init : init
    };
	
});