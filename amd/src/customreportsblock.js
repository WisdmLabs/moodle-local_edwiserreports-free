define([
    'jquery',
    'core/ajax',
    'local_edwiserreports/jquery.dataTables',
    'local_edwiserreports/dataTables.bootstrap4'
], function (
    $,
    ajax
) {
    'use strict';

    // Field group checkbox
    var cfCheckbox = '.field .custom-field-checkbox';
    var cfSelect = '.reports-filter-body .custom-field-select';
    var cfCohort = '#wdm-cohort-select';
    var cfCourse = '#wdm-course-select';
    var cfPreviewTable = null;

    var cfPreviewLoader = {
        selector : '.reports-preview-body .reports-preview-content.loader',

        show : function() {
            $(this.selector).removeClass('d-none').addClass('d-flex');
        },

        hide : function() {
            $(this.selector).removeClass('d-flex').addClass('d-none');
        }
    }

    var cfPreview = {
        selector : '.reports-preview-body .reports-preview-content',

        show : function() {
            $(this.selector + ':not(.loader)').removeClass('d-none').addClass('d-flex');
            cfPreviewLoader.hide();
        },

        hide : function() {
            $(this.selector + ':not(.loader)').removeClass('d-flex').addClass('d-none');
            cfPreviewLoader.show();
        },

        empty : function() {
            $(this.selector + '.empty').removeClass('d-none').addClass('d-flex');
            $(this.selector + ':not(.empty)').removeClass('d-flex').addClass('d-none');
            cfPreviewLoader.hide();
        }
    }

    function getCustomReportsData() {
        var selectedFields = [];
        $(cfCheckbox + ":checked").each(function() {
            selectedFields.push($(this).val());
        });

        var getCustomReportsData = ajax.call([{
            methodname: 'local_edwiserreports_get_customreports_data',
            args: {
                params: JSON.stringify({
                    fields : selectedFields,
                    cohort : $(cfCohort).val(),
                    courses : [$(cfCourse).val()]
                })
            }
        }]);

        if (selectedFields.length == 0) {
            cfPreview.empty();
        } else {
            cfPreview.hide();
            getCustomReportsData[0].done(function(response) {
                if (response.success) {
                    if (cfPreviewTable) {
                        cfPreviewTable.clear().destroy();
                        $('#cr-preview-table').html('');
                    }

                    var data = JSON.parse(response.data);
                    console.log(data);
                    cfPreviewTable = $('#cr-preview-table').DataTable({
                        columns: data.columns,
                        data: data.reportsdata,
                        bInfo: false,
                        bFilter: false,
                        searching: false,
                        lengthChange: false,
                        drawCallback: function() {
                            $('.dataTables_paginate > .pagination').addClass('pagination-sm pull-right');
                            $('.dataTables_filter').addClass('pagination-sm pull-right');
                        }
                    });
                }
            }).always(function() {
                cfPreview.show();
            });
        }
    }

    function customReportServiceInit() {
        $(cfCheckbox).on('change', function () {
            getCustomReportsData();
        });
        $(cfSelect).on('change', function () {
            getCustomReportsData();
        });
    }

    return {
        init : function () {
            $(document).ready(function () {
                customReportServiceInit();
            });
        }
    }
});
