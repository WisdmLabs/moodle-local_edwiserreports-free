define(["jquery", "local_sitereport/defaultconfig", "local_sitereport/jquery.dataTables", "local_sitereport/dataTables.bootstrap4"], function($, cfg) {
    var liveUsersTable = null;
    var panel = cfg.getPanel("#liveusersblock");
    var panelBody = cfg.getPanel("#liveusersblock", "body");
    var loader = $(panelBody + " .loader");
    var table = $(panelBody + " .table");
    var listner = null;

    function init(notifyListner) {
        listner = notifyListner;

        getOnlineUsersData(); // Call first time
    }

    /* Get online users data */
    function getOnlineUsersData() {
        $.ajax({
            url: cfg.requestUrl,
            type: cfg.requestType,
            dataType: cfg.requestDataType,
            data: {
                action: 'get_liveusers_data_ajax',
                sesskey: $(panel).data("sesskey")
            },
        })
        .done(function(response) {
            createRealtimeUsersBlock(response.data);
        })
        .fail(function(error) {
            console.log(error);
        }).always(function(){
            listner("realTimeUsers");
            setTimeout(getOnlineUsersData, 2 * 60 * 1000);
        });
    }

    /* Create Datatable of the table */
    function createRealtimeUsersBlock(data) {
        if (liveUsersTable) {
            liveUsersTable.destroy();
        } else {
            loader.hide();
            table.show();
        }

        liveUsersTable = table.DataTable({
            data: data,
            // dom : '<"row rtblock-filter"<"pull-left"f>><t>',
            language: {
                searchPlaceholder: "Search User"
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
            bInfo : false,
            lengthChange: false,
            initComplete : function() {
                var usersCount = '<small class="ml-auto my-auto font-weight-bold">LoggedIn Users : ' + data.length + '</small>';
                $(document).find(".rtblock-filter").append(usersCount);
            }
        });
    }

    // Must return the init function
    return {
        init: init
    };
});
