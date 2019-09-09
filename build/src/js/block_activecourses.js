define(['jquery', 'core/chartjs', 'report_elucidsitereport/defaultconfig', 'report_elucidsitereport/jquery.dataTables', 'report_elucidsitereport/dataTables.bootstrap4'], function ($, Chart, cfg) {
    function init(notifyListner) {
        var activeCourseTable;

        var panel = cfg.getPanel("#mostactivecourses");
        var panelBody = cfg.getPanel("#mostactivecourses", "body");
        var panelTitle = cfg.getPanel("#mostactivecourses", "title");
        var panelFooter = cfg.getPanel("#mostactivecourses", "footer");
        var dropdownBody = panel + " .table-dropdown";
        var dropdownTable = panelBody + " .dataTables_wrapper .row:first-child > div:first-child";
        var loader = panelBody + " .loader";
        var table = panelBody + " .table";

        /* Ajax request to get data for active courses table */
        $.ajax({
            url: cfg.requestUrl,
            type: cfg.requestType,
            dataType: cfg.requestDataType,
            data: {
                action: 'get_activecourses_data_ajax',
                sesskey: $(panel).data("sesskey")
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
                    if (i == 0) {
                        cell.innerHTML = "<i class='fa fa-trophy text-gold font-size-24'></i>";
                    } else if (i == 1) {
                        cell.innerHTML = "<i class='fa fa-trophy text-silver font-size-20'></i>";
                    } else if (i == 2) {
                        cell.innerHTML = "<i class='fa fa-trophy text-bronze font-size-16'></i>";
                    } else {
                        cell.innerHTML = i+1;
                    }
                });
                $(table + " td:not(.bg-secondary)").addClass("bg-white");
            }).draw();


            /* Remove laoder and display table after table is created */
            $(loader).addClass('d-none');
            $(table).removeClass('d-none');

            /* Notify that this event is completed */
            notifyListner("activeCourses");
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
                dom : '<"pull-left"f><t>',
                aaSorting: [[2, 'desc']],
                aoColumns: [
                    null,
                    null,
                    { "orderSequence": [ "desc" ] },
                    { "orderSequence": [ "desc" ] },
                    { "orderSequence": [ "desc" ] }
                ],
                language: {
                    searchPlaceholder: "Search Courses"
                },
                initComplete: function() {
                    $(dropdownBody).show();
                },
                columnDefs: [
                    {
                        "targets": 0,
                        "className": "text-center bg-secondary font-weight-bold",
                        "orderable": false
                    },
                    {
                        "targets": 1,
                        "className": "text-left",
                        "orderable": false
                    },
                    {
                        "targets": "_all",
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