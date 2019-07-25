define(['jquery', 'core/chartjs', 'report_elucidsitereport/defaultconfig'], function ($, Chart, defaultConfig) {
    function init() {
        var todaysVisits;
        var panelBody = defaultConfig.getPanel("#todaysactivityblock", "body");

        $.ajax({
            url: defaultConfig.requestUrl,
            type: defaultConfig.requestType,
            dataType: defaultConfig.requestDataType,
            data: {
                action: 'get_todaysactivity_data_ajax'
            },
        })
        .done(function(response) {
            $.each(response.data, function(indx, el) {
                if (indx != "totalusers") {
                    $(panelBody + " #todays-" + indx).html(el);
                }
            });
            generateTodaysVisitsGraph(response.data.visitshour);
        })
        .fail(function(error) {
            console.log(error);
        });

        function generateTodaysVisitsGraph(data) {
            defaultConfig.todaysActivityBlock.graph.data = data;
            var data = {
                labels: defaultConfig.todaysActivityBlock.graph.labels,
                datasets: [{
                    label: defaultConfig.todaysActivityBlock.graph.labelName,
                    data: defaultConfig.todaysActivityBlock.graph.data,
                    backgroundColor: defaultConfig.todaysActivityBlock.graph.backgroundColor
                }]
            };

            todaysVisits = new Chart(defaultConfig.todaysActivityBlock.ctx, {
                type: defaultConfig.todaysActivityBlock.graph.type,
                options: defaultConfig.todaysActivityBlock.graph.options,
                data: data
            });
        }
    }

    // Must return the init function
    return {
        init: init
    };
});