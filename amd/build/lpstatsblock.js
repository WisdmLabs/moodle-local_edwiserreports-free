define(['jquery', 'core/chartjs', 'report_elucidsitereport/defaultconfig'], function ($, Chart, defaultconfig) {
    function init() {
        var lpChart = null;
        var panelBody = defaultconfig.getPanel("#lpstatsblock", "body");
        var panelTitle = defaultconfig.getPanel("#lpstatsblock", "title");
        var selectedLp = panelTitle + " #id_lp";
        var chart = panelBody + " .ct-chart";
        var loader = panelBody + " .loader";

        $(document).ready(function() {
            var lpId = $(selectedLp).val();
            getLpStatsData(lpId);

            $(selectedLp).on("change", function () {                
                $(chart).addClass("d-none");
                $(loader).removeClass("d-none");
                lpChart.destroy();
                getLpStatsData($(this).val());
            });
        });

        function getLpStatsData(lpId) {
            $.ajax({
                url: defaultConfig.requestUrl,
                type: defaultConfig.requestType,
                dataType: defaultConfig.requestDataType,
                data: {
                    action: 'get_lpstats_data_ajax',
                    data: JSON.stringify({
                        lpid : lpId
                    })
                },
            })
            .done(function(response) {
                generateLpChart(response.data);
            })
            .fail(function(error) {
                console.log(error);
            })
            .always(function() {
                $(loader).addClass("d-none");
                $(chart).removeClass("d-none");
            });
        }

        function generateLpChart(responsedata) {
            defaultConfig.lpStatsBlock.graph.labels = responsedata.labels;
            defaultConfig.lpStatsBlock.graph.data = responsedata.data;

            var data = {
                labels: defaultConfig.lpStatsBlock.graph.labels,
                datasets: [{
                    data: defaultConfig.lpStatsBlock.graph.data,
                    backgroundColor: defaultConfig.lpStatsBlock.graph.backgroundColor,
                }]
            };

            lpChart = new Chart(defaultConfig.lpStatsBlock.ctx, {
                data: data,
                type: defaultConfig.lpStatsBlock.graph.type,
                options: defaultConfig.lpStatsBlock.graph.options
            });
        }
    }

    // Must return the init function
    return {
        init: init
    };
});