define(["jquery", "report_elucidsitereport/defaultconfig", "report_elucidsitereport/jquery.dataTables", "report_elucidsitereport/dataTables.bootstrap4"], function($, cfg) {
    var liveUsersTable = null;
    var panelBody = cfg.getPanel("#liveusersblock", "body");
    var loader = panelBody + " .loader";
    var table = panelBody + " .table";

    function init() {
        getOnlineUsersData(); // Call first time
        setInterval(getOnlineUsersData, 1000 * 30);
    }

    /* Get online users data */
    function getOnlineUsersData() {
        $.ajax({
            url: cfg.requestUrl,
            type: cfg.requestType,
            dataType: cfg.requestDataType,
            data: {
                action: 'get_liveusers_data_ajax',
            },
        })
        .done(function(response) {
            createRealtimeUsersBlock(response.data);
        })
        .fail(function(error) {
            console.log(error);
        });
    }

    /* Create Datatable of the table */
    function createRealtimeUsersBlock(data) {
        if (liveUsersTable) {
            liveUsersTable.destroy();
        } else {
            $(loader).remove();
            $(table).removeClass("d-none");
        }

        liveUsersTable = $(table).DataTable({
            data: data,
            language: {
                searchPlaceholder: "Search Users"
            },
            aaSorting: [[1, 'asc']],
            columnDefs: [
                {
                    "targets": 0,
                    "className": "text-left"
                },
                {
                    "targets": 1,
                    "className": "text-center"
                },
                {
                    "targets": 2,
                    "className": "text-center",
                    "orderable": false
                }
            ],
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