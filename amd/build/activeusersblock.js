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
                increamentDate();
                // $(_panelTitle + " #updated-time").html("Updated at ");
                $(_panelBody + " .ct-chart").removeClass('d-none');
                $(_panelBody + " .loader").addClass('d-none');
            });
        }

        function increamentDate() {
            for (i = 1; i < 60; i++) {
                setTimeout(increamentCounter, i * 1000 * 60);
            }
        }

        function increamentCounter() {
            $(_panelTitle + " #updated-time > span.minute").html(parseInt($(_panelTitle + " #updated-time > span.minute").text()) + 1);
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
                    pointBackgroundColor: defaultConfig.activeUsersBlock.graph.borderColor.activeUsers,
                    pointStyle: defaultConfig.activeUsersBlock.graph.pointStyle
                },
                {
                    label: defaultConfig.activeUsersBlock.graph.labelName.enrolments,
                    data: graphData.enrolments,
                    backgroundColor: defaultConfig.activeUsersBlock.graph.backgroundColor.enrolments,
                    borderColor: defaultConfig.activeUsersBlock.graph.borderColor.enrolments,
                    pointBorderColor: defaultConfig.activeUsersBlock.graph.borderColor.enrolments,
                    pointBackgroundColor: defaultConfig.activeUsersBlock.graph.borderColor.enrolments,
                    pointStyle: defaultConfig.activeUsersBlock.graph.pointStyle
                },
                {
                    label: defaultConfig.activeUsersBlock.graph.labelName.completionRate,
                    data: graphData.completionRate,
                    backgroundColor: defaultConfig.activeUsersBlock.graph.backgroundColor.completionRate,
                    borderColor: defaultConfig.activeUsersBlock.graph.borderColor.completionRate,
                    pointBorderColor: defaultConfig.activeUsersBlock.graph.borderColor.completionRate,
                    pointBackgroundColor: defaultConfig.activeUsersBlock.graph.borderColor.completionRate,
                    pointStyle: defaultConfig.activeUsersBlock.graph.pointStyle
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
        var _panelFooter = "#activeusersblock .panel .panel-footer";
        var _dropdownItem = "#activeusersblock .dropdown-menu .dropdown-item";
        var activeUsersGraph = getActiveUsersBlockData();
        $(_dropdownItem + ":not(.custom)").on('click', function() {
            if (!$(_panelTitle + " .filters .date-picker").hasClass("d-none")) {
                $(_panelTitle + " .filters .date-picker").addClass("d-none");
            }
            $(_panelBody + " .ct-chart").addClass('d-none');
            $(_panelBody + " .loader").removeClass('d-none');
            $(_panelTitle + " button[data-toggle='dropdown']").html($(this).text());
            activeUsersGraph.destroy();
            getActiveUsersBlockData($(this).attr('value'));
        });

        $(_dropdownItem + ".custom").on('click', function() {
            $(_panelTitle + " .filters .date-picker").removeClass("d-none");
        });

        // Validate date selector
        var _startDate = _panelTitle + " .filters .date-picker #startdate";
        var _endDate = _panelTitle + " .filters .date-picker #enddate";
        $(_startDate).on("change", function() {
            $(_endDate).attr("min", $(this)[0].value);
            $(_startDate).removeClass("border-danger");
            $(_endDate).removeClass("border-danger");
        });
        $(_endDate).on("change", function() {
            $(_startDate).attr("max", $(this)[0].value);
        });

        $(_panelTitle + " form").submit(function( event ) {
            event.preventDefault();

            if ($(_startDate)[0].value == false || $(_endDate)[0].value == false) {
                if ($(_startDate)[0].value == false) {
                    $(_startDate).addClass("border-danger");
                }

                if ($(_endDate)[0].value == false) {
                    $(_endDate).addClass("border-danger");
                }
                return false;
            }

            var dates = $(this).serializeArray();
            var startdate = new Date(dates[0].value);
            var enddate = new Date(dates[1].value);

            $(_panelTitle + " button[data-toggle='dropdown']").html(
                ("0" + startdate.getDate()).slice(-2) + "/"
                + ("0" + (startdate.getMonth() + 1)).slice(-2) + "/"
                + startdate.getFullYear() + " to "
                + ("0" + enddate.getDate()).slice(-2) + "/"
                + ("0" + (enddate.getMonth() + 1)).slice(-2) + "/"
                + startdate.getFullYear()
            );

            $(_panelBody + " .ct-chart").addClass('d-none');
            $(_panelBody + " .loader").removeClass('d-none');
            activeUsersGraph.destroy();
            getActiveUsersBlockData($(this).serialize());
        });
    }

    // Must return the init function
    return {
        init: init
    };
});