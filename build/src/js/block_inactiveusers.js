define(['jquery', 'core/chartjs', 'report_elucidsitereport/defaultconfig', 'report_elucidsitereport/jquery.dataTables', 'report_elucidsitereport/dataTables.bootstrap4'], function ($, Chart, cfg) {
    function init(notifyListner) {
        var activeUsersTable;
        var panel = cfg.getPanel("#inactiveusersblock");
        var panelBody = cfg.getPanel("#inactiveusersblock", "body");
        var panelTitle = cfg.getPanel("#inactiveusersblock", "title");
        var table = panelBody + " #inactiveuserstable";
        var tableWrapper = panelBody + " #inactiveuserstable_wrapper";
        var loader = panelBody + " .loader";
        var dropdown = panelTitle + " .dropdown-menu .dropdown-item";
        var dropdownToggle = panelTitle + " button.dropdown-toggle";
        var inActiveUsersTable = null;

        // Get inactive users data on load
        getInactiveUsersData($(dropdown).data("value"));

        /**
         * On click of dropdown get inactive user list based on filter
         */
        $(dropdown).on("click", function() {
            if (activeUsersTable) {
                activeUsersTable.destroy();
            }

            $(loader).show();
            $(table).hide();
            $(tableWrapper).hide();
            $(dropdownToggle).html($(this).html());
            getInactiveUsersData($(this).data("value"));
        });

        /**
         * Get inactive users list based on filter
         * @param  {string} filter Filter
         * @return {boolean}
         */
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

        /**
         * Create inactive users table
         * @param  {filter} filter Filter
         */
        function createInactiveUsersTable(data) {
            // If table is creted then destroy table
            if (inActiveUsersTable) {
                // Remove table data first
                $("#inactiveuserstable tbody").remove();
                inActiveUsersTable.destroy();
            }

            // Display loader
            $(loader).hide();
            $(table).show();

            // Create inactive users table
            inActiveUsersTable = $(table).DataTable( {
                data : data,
                dom : '<"pull-left"f><t>',
                aaSorting: [[2, 'desc']],
                oLanguage: {
                    sEmptyTable: "No inactive users are available."
                },
                language: {
                    searchPlaceholder: "Search Users"
                },
                columnDefs: [
                    {
                        "targets": 2,
                        "className": "text-center"
                    }
                ],
                responsive : true,
                scrollY : "320px",
                scroller: {
                    loadingIndicator: true
                },
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