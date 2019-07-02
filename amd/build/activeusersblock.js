define(['jquery', 'core/chartjs', 'report_elucidsitereport/defaultconfig'], function ($, Chart, defaultConfig) {
    function init() {
        function getActiveUsersBlockData(filter) {
            $.ajax({
                url: M.cfg.wwwroot + '/report/elucidsitereport/request_handler.php',
                data: {
                    action: 'get_activeusers_graph_data_ajax',
                    data: JSON.stringify({
                        filter : filter
                    })
                },
            }).done(function(response) {
                defaultConfig.activeUsersBlock.graph.data = response.data;
                defaultConfig.activeUsersBlock.graph.labels = response.labels;
            }).fail(function(error) {
                console.log(error);
            }).always(function() {
                activeUsersGraph = generateActiveUsersGraph();
                $(_panelTitle + " #updated-time").html("Updated at " + new Date());
                $(_panelBody + " .ct-chart").removeClass('d-none');
                $(_panelBody + " .loader").addClass('d-none');
            });
        }

        function generateActiveUsersGraph () {
            var graphData = defaultConfig.activeUsersBlock.graph.data;
            var data = {
                labels: defaultConfig.activeUsersBlock.graph.labels,
                datasets: [{
                    label: defaultConfig.activeUsersBlock.graph.labelName.activeUsers,
                    data: graphData.activeUsers,
                    backgroundColor: defaultConfig.activeUsersBlock.graph.backgroundColor.activeUsers,
                    borderColor: defaultConfig.activeUsersBlock.graph.borderColor.activeUsers,
                    pointBorderColor: defaultConfig.activeUsersBlock.graph.borderColor.activeUsers,
                    pointBackgroundColor: defaultConfig.activeUsersBlock.graph.borderColor.activeUsers
                },
                {
                    label: defaultConfig.activeUsersBlock.graph.labelName.enrolments,
                    data: graphData.enrolments,
                    backgroundColor: defaultConfig.activeUsersBlock.graph.backgroundColor.enrolments,
                    borderColor: defaultConfig.activeUsersBlock.graph.borderColor.enrolments,
                    pointBorderColor: defaultConfig.activeUsersBlock.graph.borderColor.enrolments,
                    pointBackgroundColor: defaultConfig.activeUsersBlock.graph.borderColor.enrolments
                },
                {
                    label: defaultConfig.activeUsersBlock.graph.labelName.completionRate,
                    data: graphData.completionRate,
                    backgroundColor: defaultConfig.activeUsersBlock.graph.backgroundColor.completionRate,
                    borderColor: defaultConfig.activeUsersBlock.graph.borderColor.completionRate,
                    pointBorderColor: defaultConfig.activeUsersBlock.graph.borderColor.completionRate,
                    pointBackgroundColor: defaultConfig.activeUsersBlock.graph.borderColor.completionRate
                }]
            };

            Chart.defaults.global.defaultFontFamily = defaultConfig.activeUsersBlock.graph.fontFamily;
            Chart.defaults.global.defaultFontStyle = defaultConfig.activeUsersBlock.graph.fontStyle;
            return activeUsersGraph = new Chart(defaultConfig.activeUsersBlock.ctx, {
                type: defaultConfig.activeUsersBlock.graph.type,
                data: data,
                options: defaultConfig.activeUsersBlock.graph.options
            });
        }

        /* Call function to initialize the active users block graph */

        var _panelBody = "#activeusersblock .panel .panel-body";
        var _panelTitle = "#activeusersblock .panel .panel-title";
        var activeUsersGraph = getActiveUsersBlockData();
        $('#activeusersblock .dropdown-menu .dropdown-item').on('click', function() {
            $(_panelBody + " .ct-chart").addClass('d-none');
            $(_panelBody + " .loader").removeClass('d-none');
            $(_panelTitle + " button").html($(this).text());
            activeUsersGraph.destroy();
            getActiveUsersBlockData($(this).attr('value'));
        });
    }

    // Must return the init function
    return {
        init: init
    };
});