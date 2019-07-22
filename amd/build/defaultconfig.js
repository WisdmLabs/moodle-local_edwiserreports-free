define(["jquery", "report_elucidsitereport/variables"], function($, v) {
    var toggleMenuAndPin = "#toggleMenubar [data-toggle='menubar'], .page-aside-pin";

    $(document).ready(function() {
        var pageContent = $("#page-admin-report-elucidsitereport-index .page-content");
        rearrangeBlocks(pageContent.width());

        $(document).on("click", toggleMenuAndPin, function() {
            var pageWidth = pageContent.width();
            var isNavlink = $(this).hasClass("nav-link");

            rearrangeBlocks(pageWidth, isNavlink);
        });
    });

    function rearrangeBlocks(pageWidth, isNavlink) {
        if (isNavlink) {
            var menubarFolded = $("body").hasClass("site-menubar-fold");
            if (menubarFolded) {
                $("#wdm-elucidsitereport > div").addClass("col-lg-12");
            } else {
                $("#wdm-elucidsitereport > div").removeClass("col-lg-12");
            }
        } else {
            if (pageWidth < 768 ) {
                $("#wdm-elucidsitereport > div").addClass("col-lg-12");
            } else {
                $("#wdm-elucidsitereport > div").removeClass("col-lg-12");
            }
        }

        $(document).find('.table.dataTable').DataTable().draw();
    }

    return defaultConfig = {
        // Default Config
        requestUrl : M.cfg.wwwroot + '/report/elucidsitereport/request_handler.php',
        requestType : 'GET',
        requestDataType : 'json',

        // Todays Activity Block
        todaysActivityBlock : {
            ctx : $(v.todaysActivityBlock)[0].getContext("2d"),
            labelName : "Page Access",
            graph : {
                type : "bar",
                options : {
                    responsive: true,
                    maintainAspectRatio: false,
                    aspectRatio: 1,
                    scales:{
                        xAxes: [{
                            display: false
                        }],
                        yAxes: [{
                            display: false,
                        }]
                    },
                    legend: {
                        display: false
                    }
                },
                borderColor : v.whiteColor,
                backgroundColor : v.whiteColor,
                borderWidth: 2,
                labels : [
                    v.clock12_0, v.clock12_1, v.clock12_2,
                    v.clock12_3, v.clock12_4, v.clock12_5,
                    v.clock12_6, v.clock12_7, v.clock12_8,
                    v.clock12_9, v.clock12_10, v.clock12_11,
                    v.clock12_12, v.clock12_13, v.clock12_14,
                    v.clock12_15, v.clock12_16, v.clock12_0,
                    v.clock12_18, v.clock12_19, v.clock12_20,
                    v.clock12_21, v.clock12_22, v.clock12_23,
                ],
                data : [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
            },
        },

        // Active Users Block
        activeUsersBlock : {
            ctx : $(v.activeUsersBlock)[0].getContext("2d"),
            labelName : "Page Access",
            graph : {
                type : "line",
                fontFamily : "Open Sans",
                fontStyle : "bold",
                pointStyle: "line",
                showInLegend: true,
                options : {
                    legend: {
                        labels: {
                            usePointStyle: true
                        }
                    },
                    elements: {
                        borderWidth: 4
                    },
                    scales:{
                        yAxes: [{
                            ticks : {
                                beginAtZero: true,
                                min : 0,
                                callback: function (value) {
                                    if (Number.isInteger(value)) {
                                        return value;
                                    }
                                }
                            },
                        }]
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    aspectRatio: 1
                },
                labelName : {
                    activeUsers : "Active Users",
                    enrolments : "Course Enrolment",
                    completionRate : "Course Completion Rate"
                },
                backgroundColor : {
                    activeUsers : "rgba(0, 0, 0, 0)",
                    enrolments : "rgba(0, 0, 0, 0)",
                    completionRate : "rgba(0, 0, 0, 0)"
                },
                borderColor : {
                    activeUsers : "rgba(255, 99, 132, 1)",
                    enrolments : "rgba(73, 222, 148, 1)",
                    completionRate : "rgba(62, 142, 247, 1)"
                },
                data : {
                    activeUsers : [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    enrolments : [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    completionRate : [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
                }
            }
        },

        // Course Progress Block
        courseProgressBlock : {
            ctx : $(v.courseProgressBlock)[0].getContext("2d"),
            graph : {
                type : "pie",
                data : [0, 0, 0, 0, 0, 0],
                options : {
                    responsive: true,
                    legend: {position: 'bottom'},
                    maintainAspectRatio: false,
                    aspectRatio: 1,
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                return M.util.get_string('courseprogresstooltip', 'report_elucidsitereport', {
                                    label: data.labels[tooltipItem.index],
                                    data: data.datasets[0].data[tooltipItem.index]
                                });
                            }
                        }
                    }
                },
                labels : ['0%', '20%', '40%', '60%', '80%', '100%'],
                backgroundColor : ["#fe6384", "#36a2eb", "#fdce56", "#cacbd0", "#4ac0c0", "#ff851b"]
            }
        },

        // LP Progress Block
        lpStatsBlock : {
            ctx : $(v.lpStatsBlock)[0].getContext("2d"),
            graph : {
                type : "pie",
                options : {
                    responsive: true,
                    legend: {position: 'bottom'},
                    maintainAspectRatio: false,
                    aspectRatio: 1,
                },
                labels : ['No Users/Courses are available'],
                data : [0],
                backgroundColor : ["#fe6384", "#36a2eb", "#fdce56", "#cacbd0", "#4ac0c0", "#ff851b"]
            }
        },

        // Function to get panelbody, paneltitle and panelfooter
        getPanel: function (blockid, type) {
            var panel = "#wdm-elucidsitereport " + blockid;

            switch(type) {
                case "body":
                    panel += " .panel-body";
                    break;
                case "title":
                    panel += " .panel-title";
                    break;
                case "footer":
                    panel += " .panel-footer";
                    break;
                case "table":
                    panel += " .table";
                    break;
                case "loader":
                    panel += " .loader";
                    break;
            }
            return panel;
        },

        // function to get Template
        getTemplate: function(template) {
            return "report_elucidsitereport/" + template;
        }
    };
});