define(["jquery", "report_elucidsitereport/variables", "report_elucidsitereport/select2"], function($, v) {
    return defaultConfig = {
        // Default Config
        requestUrl : v.requestUrl,
        requestType : v.requestType,
        requestDataType : v.requestDataType,
        component : v.component,

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
                    v.clock12_15, v.clock12_16, v.clock12_17,
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

        /**
         * Get course progress block object
         * @return {object} Course progress graph object
         */
        getCourseProgressBlock: function () {
            cpBlockData = $(v.courseProgressBlock);
            if (cpBlockData.length == 0) {
                return false;
            }

            // Return course progress graph object
            return {
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
                                title : function(tooltipItem, data) {
                                    return [
                                        M.util.get_string('cpblocktooltip1',
                                        v.component,
                                        {
                                            "per" : data.labels[tooltipItem[0].index],
                                        }),
                                        M.util.get_string('cpblocktooltip2',
                                        v.component,
                                        {
                                            "val" : data.datasets[0].data[tooltipItem[0].index]
                                        })
                                    ];
                                },
                                label : function() {
                                    return '';
                                }
                            }
                        }
                    },
                    labels : [
                        M.util.get_string('per20-0', v.component),
                        M.util.get_string('per40-20', v.component),
                        M.util.get_string('per60-40', v.component),
                        M.util.get_string('per80-60', v.component),
                        M.util.get_string('per100-80', v.component),
                        M.util.get_string('per100', v.component)
                    ],
                    backgroundColor : ["#fe6384", "#36a2eb", "#fdce56", "#cacbd0", "#4ac0c0", "#ff851b"]
                }
            }
        },

        changeExportUrl: v.changeExportUrl,

        // Get learning program blocks
        getLpStatsBlock: function () {
            lpStatsBlock = $(v.lpStatsBlock);
            if (lpStatsBlock.length == 0) {
                return false;
            }

            return {
                ctx : lpStatsBlock[0].getContext("2d"),
                graph : {
                    type : "pie",
                    options : {
                        responsive: true,
                        legend: {position: 'bottom'},
                        maintainAspectRatio: false,
                        aspectRatio: 1,
                        tooltips: {
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    return M.util.get_string('lpstatstooltip', v.component, {
                                        label: data.labels[tooltipItem.index],
                                        data: data.datasets[0].data[tooltipItem.index]
                                    });
                                }
                            }
                        }
                    },
                    labels : ['No Users/Courses are available'],
                    data : [0],
                    backgroundColor : [
                        "#fe6384", "#36a2eb", "#fdce56", "#cacbd0", "#4ac0c0", "#ff851b",
                        "#14b8bc", "#39514a", "#059143", "#435722", "#99c979", "#04d4e3",
                        "#666b54", "#e029a1", "#808040", "#d0926a", "#54271a", "#e9afdc",
                        "#3b4a7d", "#79e741", "#d4c3b0", "#d9c400", "#f46e27", "#15190c"
                    ]
                }
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
        },

        // Get URL Params
        getUrlParams: v.getUrlParams
    };
});