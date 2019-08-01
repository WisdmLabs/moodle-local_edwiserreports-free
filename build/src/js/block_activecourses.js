define(['jquery', 'core/chartjs', 'report_elucidsitereport/defaultconfig', 'report_elucidsitereport/jquery.dataTables', 'report_elucidsitereport/dataTables.bootstrap4'], function ($, Chart, cfg) {
    function init() {
        var activeCourseTable;

        var panelBody = cfg.getPanel("#mostactivecourses", "body");
        var panelTitle = cfg.getPanel("#mostactivecourses", "title");
        var panelFooter = cfg.getPanel("#mostactivecourses", "footer");
        var dropdownBody = panelBody + " .table-dropdown";
        var dropdownTable = panelBody + " .dataTables_wrapper .row:first-child > div:first-child";
        var loader = panelBody + " .loader";
        var table = panelBody + " .table";

        /* Ajax request to get data for active courses table */
        $.ajax({
            url: cfg.requestUrl,
            type: cfg.requestType,
            dataType: cfg.requestDataType,
            data: {
                action: 'get_activecourses_data_ajax'
            },
        })
        .done(function(response) {
            /* Create active course table */
            createActiveCourseTable(response.data);
        })
        .fail(function(error) {
            console.log(error);
        })
        .always(function() {
            /* Added fixed column rank in datatable */
            activeCourseTable.on('order.dt search.dt', function () {
                activeCourseTable.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                });
            }).draw();


            /* Remove laoder and display table after table is created */
            $(loader).addClass('d-none');
            $(table).removeClass('d-none');
        });

        function createActiveCourseTable(data) {
            /* If datable already created the destroy the table*/
            if (activeCourseTable) {
                activeCourseTable.destroy();
            }

            /* Create datatable for active courses */
            activeCourseTable = $(table).DataTable({
                responsive: true,
                data : data,
                aaSorting: [[2, 'desc']],
                language: {
                    searchPlaceholder: "Search Courses"
                },
                initComplete: function(settings, json) {
                    $(dropdownTable).html($(dropdownBody).html());
                    $(dropdownBody).remove();
                    $(dropdownTable + " .dropdown").show();
                },
                columnDefs: [
                    {
                        "targets": 0,
                        "className": "text-center",
                        "orderable": false
                    },
                    {
                        "targets": 1,
                        "className": "text-left",
                        "orderable": false
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
                scrollY : "300px",
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
    }

    // Must return the init function
    return {
        init: init
    };
});