/* eslint-disable no-console */
define([
    'jquery',
    'core/chartjs',
    'local_edwiserreports/defaultconfig',
    'local_edwiserreports/variables',
    'local_edwiserreports/select2'
], function($, Chart, cfg, V) {
    /**
     * Initialize
     * @param {function} notifyListner Callback function
     */
    function init(notifyListner) {
        var lpChart = null;
        var panel = cfg.getPanel("#lpstatsblock");
        var panelBody = cfg.getPanel("#lpstatsblock", "body");
        var selectedLp = panelBody + " #wdm-lpstats-select";
        var exportUrlLink = panel + " .dropdown-menu[aria-labelledby='export-dropdown'] .dropdown-item";
        var chart = panelBody + " .ct-chart";
        var loader = panelBody + " .loader";
        var lpStatsBlock = false;

        $(document).ready(function() {
            var lpId = $(selectedLp).val();
            lpStatsBlock = cfg.getLpStatsBlock();

            if (lpStatsBlock) {
                getLpStatsData(lpId);
                $(panelBody + ' .singleselect').select2();

                $(selectedLp).on("change", function() {
                    lpId = $(this).val();
                    $(chart).addClass("d-none");
                    $(loader).removeClass("d-none");
                    cfg.changeExportUrl(lpId, exportUrlLink, V.filterReplaceFlag);
                    lpChart.destroy();
                    getLpStatsData(lpId);
                });
            } else {
                notifyListner("lpStatsBlock");
            }
        });

        /**
         * Get learning plan stats
         * @param {Integer} lpId Learning plan id
         */
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
                        lpid: lpId
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
                    notifyListner("lpStatsBlock");
                });
        }

        /**
         * Generate learning plan chart.
         * @param {Object} responsedata Generate chart response data.
         */
        function generateLpChart(responsedata) {
            if (lpStatsBlock) {
                lpStatsBlock.graph.labels = responsedata.labels;
                lpStatsBlock.graph.data = responsedata.data;

                var data = {
                    labels: lpStatsBlock.graph.labels,
                    datasets: [{
                        data: lpStatsBlock.graph.data,
                        backgroundColor: lpStatsBlock.graph.backgroundColor,
                    }]
                };

                lpChart = new Chart(lpStatsBlock.ctx, {
                    data: data,
                    type: lpStatsBlock.graph.type,
                    options: lpStatsBlock.graph.options
                });
            }
        }
    }

    // Must return the init function
    return {
        init: init
    };
});
