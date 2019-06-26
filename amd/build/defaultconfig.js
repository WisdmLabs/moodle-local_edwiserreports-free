define(["jquery", "report_elucidsitereport/variables"], function($, v) {
    return defaultConfig = {
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
                            display: false
                        }]
                    },
                    legend: {
                        display: false
                    }
                },
                backgroundColor : [
                    v.whiteColor, v.whiteColor, v.whiteColor,
                    v.whiteColor, v.whiteColor, v.whiteColor,
                    v.whiteColor, v.whiteColor, v.whiteColor,
                    v.whiteColor, v.whiteColor, v.whiteColor,
                    v.whiteColor, v.whiteColor, v.whiteColor,
                    v.whiteColor, v.whiteColor, v.whiteColor,
                    v.whiteColor, v.whiteColor, v.whiteColor,
                    v.whiteColor, v.whiteColor, v.whiteColor,
                ],
                labels : [
                    v.clock12_0, v.clock12_1, v.clock12_2,
                    v.clock12_3, v.clock12_4, v.clock12_5,
                    v.clock12_6, v.clock12_7, v.clock12_8,
                    v.clock12_9, v.clock12_10, v.clock12_11,
                    v.clock12_12, v.clock12_13, v.clock12_14,
                    v.clock12_15, v.clock12_16, v.clock12_0,
                    v.clock12_18, v.clock12_19, v.clock12_20,
                    v.clock12_21, v.clock12_22, v.clock12_23,
                ]
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
                options : {
                    elements: {
                        point: {
                            radius: 0
                        },
                        borderWidth: 4
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    aspectRatio: 1
                },
                labels : [
                    v.month_1, v.month_2, v.month_3, v.month_4,
                    v.month_5, v.month_6, v.month_7, v.month_8,
                    v.month_9, v.month_10, v.month_11, v.month_12,
                ],
                labelName : {
                    activeUsers : "Active Users",
                    enrolments : "Course Enrolment",
                    completionRate : "Course Completion Rate"
                },
                backgroundColor : {
                    activeUsers : ["rgba(0, 0, 0, 0)"],
                    enrolments : ["rgba(0, 0, 0, 0)"],
                    completionRate : ["rgba(0, 0, 0, 0)"]
                },
                borderColor : {
                    activeUsers : ["rgba(255, 99, 132, 1)"],
                    enrolments : ["rgba(73, 222, 148, 1)"],
                    completionRate : ["rgba(62, 142, 247, 1)"]
                }
            }
        },

        // Active Users Block
        courseProgressBlock : {
            ctx : $(v.courseProgressBlock)[0].getContext("2d"),
            graph : {
                type : "pie",
                options : {
                    responsive: true,
                    maintainAspectRatio: false,
                    aspectRatio: 1,
                },
                labelName : "Active Users",
                labels : ['0%', '20%', '40%', '60%', '80%', '100%'],
                backgroundColor : ["#fe6384", "#36a2eb", "#fdce56", "#cacbd0", "#4ac0c0", "#FF851B"]
            }
        },

        // Active Users Block
        lpStatsBlock : {
            ctx : $(v.lpStatsBlock)[0].getContext("2d"),
            graph : {
                type : "pie",
                options : {
                    responsive: true,
                    maintainAspectRatio: false,
                    aspectRatio: 1,
                },
                labelName : "Active Users",
                labels : ['Completed', 'Incompleted'],
                backgroundColor : ["#fe6384", "#36a2eb"]
            }
        },
    };
});