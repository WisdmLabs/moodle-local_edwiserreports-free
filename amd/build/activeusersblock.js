define(['jquery', 'core/chartjs', 'report_elucidsitereport/defaultconfig'], function ($, Chart, defaultConfig) {
    function init() {
        var graphData = {
            activeUsers : [12, 19, 3, 5, 2, 3, 12, 12, 19, 3, 5, 6],
            enrolment : [2, 9, 13, 15, 6, 15, 13, 9, 4, 11, 6, 3],
            completionRate : [11, 15, 12, 5, 9, 10, 4, 11, 15, 12, 5, 10]
        };

        var data = {
            labels: defaultConfig.activeUsersBlock.graph.labels,
            datasets: [{
                label: defaultConfig.activeUsersBlock.graph.labelName.activeUsers,
                data: graphData.activeUsers,
                backgroundColor: defaultConfig.activeUsersBlock.graph.backgroundColor.activeUsers,
                borderColor: defaultConfig.activeUsersBlock.graph.borderColor.activeUsers
            },
            {
                label: defaultConfig.activeUsersBlock.graph.labelName.enrolments,
                data: graphData.enrolment,
                backgroundColor: defaultConfig.activeUsersBlock.graph.backgroundColor.enrolments,
                borderColor: defaultConfig.activeUsersBlock.graph.borderColor.enrolments
            },
            {
                label: defaultConfig.activeUsersBlock.graph.labelName.completionRate,
                data: graphData.completionRate,
                backgroundColor: defaultConfig.activeUsersBlock.graph.backgroundColor.completionRate,
                borderColor: defaultConfig.activeUsersBlock.graph.borderColor.completionRate
            }]
        };

        Chart.defaults.global.defaultFontFamily = defaultConfig.activeUsersBlock.graph.fontFamily;
        Chart.defaults.global.defaultFontStyle = defaultConfig.activeUsersBlock.graph.fontStyle;
        var myLineChart = new Chart(defaultConfig.activeUsersBlock.ctx, {
            type: defaultConfig.activeUsersBlock.graph.type,
            data: data,
            options: defaultConfig.activeUsersBlock.graph.options
        });
    }

    // Must return the init function
    return {
        init: init
    };
});