define([
    'jquery',
    'core/modal_factory',
    'core/modal_events',
    'core/fragment',
    'core/templates',
    'local_sitereport/variables',
    'local_sitereport/select2',
    'local_sitereport/jquery.dataTables',
    'local_sitereport/dataTables.bootstrap4',
    'local_sitereport/jquery-asPieProgress',
    'local_sitereport/common'
], function($, ModalFactory, ModalEvents, Fragment, Templates, V) {
    function init(CONTEXTID) {
        var PageId = $("#wdm-certificates-individual");
        var CertTable = PageId.find(".table");
        var loader = PageId.find(".loader");
        var CertDropdown = $("#wdm-certificates-dropdown");
        var CertSelect = "#wdm-certificates-select";
        var exportUrlLink = ".dropdown-menu[aria-labelledby='export-dropdown'] .dropdown-item";
        var filterSection = $("#wdm-userfilter .row .col-md-6:first-child");
        var dataTable = null;
        var certificateid = null;

        // Varibales for cohort filter
        var cohortId = 0;
        var cohortFilterBtn   = "#cohortfilter";
        var cohortFilterItem  = cohortFilterBtn + " ~ .dropdown-menu .dropdown-item";

        function getCertificateDetail(certificateid, cohortId) {
            var params = {
                action: 'get_certificates_data_ajax',
                sesskey: $(PageId).data("sesskey"),
                data: JSON.stringify({
                    certificateid : certificateid,
                    cohortid : cohortId
                })
            };

            if (dataTable) {
                dataTable.destroy();
            }

            dataTable = CertTable.DataTable({
                ajax : V.generateUrl(V.requestUrl, params),
                // dom : "<'pull-left'f><t><p>",
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
                        "targets": "_all",
                        "className": "align-middle text-center"
                    }
                ],
                columns : [
                    { "data": "username" },
                    { "data": "email" },
                    { "data": "issuedate" },
                    { "data": "dateenrolled" },
                    { "data": "grade" },
                    { "data": "courseprogress" }
                ],
                language : {
                    searchPlaceholder: "Search User",
                    emptyTable : "No certificates are awarded"
                },
                initComplete: function(settings, json) {
                    $('.pie-progress').asPieProgress();
                    CertTable.show();
                },
                drawCallback: function () {
                    $('.dataTables_paginate > .pagination').addClass('pagination-sm pull-right');
                    $('.dataTables_filter').addClass('pagination-sm pull-right');
                    createPieProgress('');
                },
                bInfo : false,
                // lengthChange : false,
                // paginate : false,
                responsive : true
                // scrollY : "350px",
                // scrollX : true,
                // sScrollX : "100%",
                // bScrollCollapse : true
            });
        }

        /**
     * Create pie progress where div with .pie-progress class is present
     */
    function createPieProgress(target) {
        var element = PageId;
        if (target != '') {
            element = element.find(target);
        }
        element.find('.pie-progress').asPieProgress({
            namespace: 'pie-progress',
            speed: 30,
            classes: {
                svg: 'pie-progress-svg',
                element: 'pie-progress',
                number: 'pie-progress-number',
                content: 'pie-progress-content'
            }
        });
    }

        $(document).ready(function() {
            // filterSection.html(CertDropdown.html());
            // CertDropdown.remove();
            // $(document).find(CertSelect).show();
            $(document).find(CertSelect).select2();

            certificateid = $(CertSelect).val();
            getCertificateDetail(certificateid);

            /* Select cohort filter for active users block */
            $(cohortFilterItem).on('click', function() {
                cohortId = $(this).data('cohortid');
                V.changeExportUrl(cohortId, exportUrlLink, V.cohortReplaceFlag);
                $(cohortFilterBtn).html($(this).text());
                getCertificateDetail(certificateid, cohortId);
            });

            $(document).on("change", CertSelect, function() {
                certificateid = $(this).val()
                getCertificateDetail(certificateid);
                $('.download-links input[name="filter"]').val(certificateid);
                // V.changeExportUrl(certificateid, exportUrlLink, V.filterReplaceFlag);
            });
        });
    }

    return {
        init : init
    };

});
