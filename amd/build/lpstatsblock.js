define(['jquery', 'core/chartjs', 'report_elucidsitereport/defaultconfig'], function ($, Chart, defaultconfig) {
    function init() {
        var graphData = [12, 19];

        var data = {
            labels: defaultConfig.lpStatsBlock.graph.labels,
            datasets: [{
                data: graphData,
                label: defaultConfig.lpStatsBlock.graph.label,
                backgroundColor: defaultConfig.lpStatsBlock.graph.backgroundColor,
            }]
        };

        var myPieChart = new Chart(defaultConfig.lpStatsBlock.ctx, {
            data: data,
            type: defaultConfig.lpStatsBlock.graph.type,
            options: defaultConfig.lpStatsBlock.graph.options
        });
    }

    // Must return the init function
    return {
        init: init
    };
});