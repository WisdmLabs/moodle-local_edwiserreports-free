define(['jquery', 'core/chartjs', 'report_elucidsitereport/defaultconfig', 'report_elucidsitereport/jquery.dataTables', 'report_elucidsitereport/dataTables.bootstrap4'], function ($, Chart, defaultConfig) {
    function init() {
        $.ajax({
            url: defaultConfig.requestUrl,
            type: 'GET',
            dataType: 'json',
            data: {
                action: 'get_activecourses_data_ajax'
            },
        })
        .done(function(response) {
            createActiveCourseTable(response.data);
        })
        .fail(function(error) {
            console.log(error);
        })
        .always(function() {
            $(_panelBody + " .loader").addClass('d-none');
            $(_panelBody + " .table").removeClass('d-none');
            _activeCourseTable.on( 'order.dt search.dt', function () {
                _activeCourseTable.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                });
            }).draw();
        });

        var _activeCourseTable;

        var _panelBody = "#mostactivecourses .panel .panel-body";
        var _panelTitle = "#mostactivecourses .panel .panel-title";
        var _panelFooter = "#mostactivecourses .panel .panel-footer";
        function createActiveCourseTable(data) {
            _activeCourseTable = $("#wdm-elucidsitereport #mostactivecourses table")
            .DataTable( {
                data : data,
                aaSorting: [[2, 'desc']],
                columnDefs: [
                    {
                        "targets": 0,
                        "className": "text-center",
                    },
                    {
                        "targets": 1,
                        "className": "text-left",
                    },
                    {
                        "targets": 2,
                        "className": "text-center",
                    },
                    {
                        "targets": 3,
                        "className": "text-center",
                    },
                    {
                        "targets": 4,
                        "className": "text-center",
                    }
                ],
                scrollY : "250px",
                scrollCollapse : true,
                paging: false,
                bInfo : false
            });
        }
    }

    // Must return the init function
    return {
        init: init
    };
});