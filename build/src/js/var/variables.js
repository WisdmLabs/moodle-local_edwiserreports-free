define(function() {
    return {
        requestUrl : M.cfg.wwwroot + '/report/elucidsitereport/request_handler.php',
        requestType : 'GET',
        requestDataType : 'json',
        whiteColor : "rgba(255, 255, 255, 0.8)",
        todaysActivityBlock : "#todaysactivityblock .ct-chart",
        activeUsersBlock : "#activeusersblock .ct-chart",
        courseProgressBlock : "#courseprogressblock .ct-chart",
        lpStatsBlock : "#lpstatsblock .ct-chart",
        month_1 : "JAN",
        month_2 : "FEB",
        month_3 : "MAR",
        month_4 : "APR",
        month_5 : "MAY",
        month_6 : "JUN",
        month_7 : "JUL",
        month_8 : "AUG",
        month_9 : "SEP",
        month_10 : "OCT",
        month_11 : "NOV",
        month_12 : "DEC",
        clock12_0 : "12:00 AM",
        clock12_1 : "01:00 AM",
        clock12_2 : "02:00 AM",
        clock12_3 : "03:00 AM",
        clock12_4 : "04:00 AM",
        clock12_5 : "05:00 AM",
        clock12_6 : "06:00 AM",
        clock12_7 : "07:00 AM",
        clock12_8 : "08:00 AM",
        clock12_9 : "09:00 AM",
        clock12_10 : "10:00 AM",
        clock12_11 : "11:00 AM",
        clock12_12 : "12:00 PM",
        clock12_13 : "01:00 PM",
        clock12_14 : "02:00 PM",
        clock12_15 : "03:00 PM",
        clock12_16 : "04:00 PM",
        clock12_17 : "05:00 PM",
        clock12_18 : "06:00 PM",
        clock12_19 : "07:00 PM",
        clock12_20 : "08:00 PM",
        clock12_21 : "09:00 PM",
        clock12_22 : "10:00 PM",
        clock12_23 : "11:00 PM",

        changeExportUrl : function (filter, exportUrlLink) {
            $(exportUrlLink).each(function() {
                var oldUrl = $(this)[0].href;
                $(this)[0].href = oldUrl.replace(/filter=(.*)/, "filter="+filter);
            });
        }
    }
});