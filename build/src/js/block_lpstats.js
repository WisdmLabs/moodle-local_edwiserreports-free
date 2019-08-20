define(['jquery', 'core/chartjs', 'report_elucidsitereport/defaultconfig'], function ($, Chart, cfg) {
    function init() {
        var lpChart = null;
        var panel = cfg.getPanel("#lpstatsblock");
        var panelBody = cfg.getPanel("#lpstatsblock", "body");
        var panelTitle = cfg.getPanel("#lpstatsblock", "title");
        var selectedLp = panelBody + " #wdm-lpstats-select";
        var exportUrlLink = panel + " .dropdown-menu[aria-labelledby='export-dropdown'] .dropdown-item";
        var chart = panelBody + " .ct-chart";
        var loader = panelBody + " .loader";

        $(document).ready(function() {
            var lpId = $(selectedLp).val();
            getLpStatsData(lpId);

            $(selectedLp).on("change", function () {  
                lpId = $(this).val();              
                $(chart).addClass("d-none");
                $(loader).removeClass("d-none");
                cfg.changeExportUrl(lpId, exportUrlLink);
                lpChart.destroy();
                getLpStatsData(lpId);
            });
        });

        function getLpStatsData(lpId) {
            $.ajax({
                url: cfg.requestUrl,
                type: cfg.requestType,
                dataType: cfg.requestDataType,
                sesskey: $(panel).data("sesskey"),
                data: {
                    action: 'get_lpstats_data_ajax',
                sesskey: $(panel).data("sesskey"),
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
            cfg.lpStatsBlock.graph.labels = responsedata.labels;
            cfg.lpStatsBlock.graph.data = responsedata.data;

            var data = {
                labels: cfg.lpStatsBlock.graph.labels,
                datasets: [{
                    data: cfg.lpStatsBlock.graph.data,
                    backgroundColor: cfg.lpStatsBlock.graph.backgroundColor,
                }]
            };

            lpChart = new Chart(cfg.lpStatsBlock.ctx, {
                data: data,
                type: cfg.lpStatsBlock.graph.type,
                options: cfg.lpStatsBlock.graph.options
            });
        }
    }

    // Must return the init function
    return {
        init: init
    };
});