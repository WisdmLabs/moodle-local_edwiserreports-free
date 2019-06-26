define(['jquery', 'core/chartjs', 'report_elucidsitereport/defaultconfig'], function ($, Chart, defaultconfig) {
    function init() {
        var graphData = [12, 19, 3, 5, 2, 6];

        var data = {
            labels: defaultConfig.courseProgressBlock.graph.labels,
            datasets: [{
                label: defaultConfig.courseProgressBlock.graph.label,
                data: graphData,
                backgroundColor: defaultConfig.courseProgressBlock.graph.backgroundColor
            }]
        };

        var myPieChart = new Chart(defaultConfig.courseProgressBlock.ctx, {
            data: data,
            type: defaultConfig.courseProgressBlock.graph.type,
            options: defaultConfig.courseProgressBlock.graph.options
        });
    }

    // Must return the init function
    return {
        init: init
    };
});