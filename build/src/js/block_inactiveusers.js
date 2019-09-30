define(['jquery', 'core/chartjs', 'report_elucidsitereport/defaultconfig', 'report_elucidsitereport/jquery.dataTables', 'report_elucidsitereport/dataTables.bootstrap4'], function ($, Chart, cfg) {
    function init(notifyListner) {
        var activeUsersTable;
        var panel = cfg.getPanel("#inactiveusersblock");
        var panelBody = cfg.getPanel("#inactiveusersblock", "body");
        var panelTitle = cfg.getPanel("#inactiveusersblock", "title");
        var table = panelBody + " #inactiveuserstable";
        var loader = panelBody + " .loader";
        var dropdown = panelTitle + " .dropdown-menu .dropdown-item";
        var dropdownToggle = panelTitle + " button.dropdown-toggle";
        var inActiveUsersTable = null;

        getInactiveUsersData($(dropdown).data("value"));
        $(dropdown).on("click", function() {
            if (activeUsersTable) {
                activeUsersTable.destroy();
            }

            $(loader).removeClass('d-none');
            $(table).addClass('d-none');
            $(dropdownToggle).html($(this).html());
            getInactiveUsersData($(this).data("value"));
        });

        function getInactiveUsersData(filter) {
            $.ajax({
                url: cfg.requestUrl,
                type: 'GET',
                dataType: 'json',
                data: {
                    action: 'get_inactiveusers_data_ajax',
                    sesskey: $(panel).data("sesskey"),
                    data: JSON.stringify({
                        filter: filter
                    })
                },
            })
            .done(function(response) {
                createInactiveUsersTable(response.data);
            })
            .fail(function(error) {
                console.log(error);
            }).always(function() {
                notifyListner("inActiveUsers");
            });
        }

        function createInactiveUsersTable(data) {
            $(loader).addClass('d-none');
            $(table).removeClass('d-none');

            if (inActiveUsersTable) {
                inActiveUsersTable.destroy();
            }

            inActiveUsersTable = $(table)
            .DataTable( {
                data : data,
                dom : '<"pull-left"f><t>',
                aaSorting: [[2, 'desc']],
                oLanguage: {
                    sEmptyTable: "No users are available"
                },
                language: {
                    searchPlaceholder: "Search Users"
                },
                columnDefs: [
                    {
                        "targets": 2,
                        "className": "text-center",
                    }
                ],
                responsive : true,
                scrollY : "320px",
                scrollCollapse : true,
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