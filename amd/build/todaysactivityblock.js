define(['jquery', 'core/chartjs', 'report_elucidsitereport/defaultconfig'], function ($, Chart, defaultConfig) {
    function init() {
        var graphData = [12, 19, 3, 5, 2, 3, 12, 12, 19, 3, 5, 6, 12, 19, 3, 5, 2, 3, 12, 12, 19, 3, 5, 6];

        var data = {
            labels: defaultConfig.todaysActivityBlock.graph.labels,
            datasets: [{
                label: defaultConfig.todaysActivityBlock.graph.labelName,
                data: graphData,
                backgroundColor: defaultConfig.todaysActivityBlock.graph.backgroundColor
            }],
        };

        var myPieChart = new Chart(defaultConfig.todaysActivityBlock.ctx, {
            type: defaultConfig.todaysActivityBlock.graph.type,
            options: defaultConfig.todaysActivityBlock.graph.options,
            data: data
        });
    }

    // Must return the init function
    return {
        init: init
    };
});