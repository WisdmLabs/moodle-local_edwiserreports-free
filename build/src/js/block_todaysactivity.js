define(['jquery', 'core/chartjs', 'report_elucidsitereport/defaultconfig'], function ($, Chart, cfg) {
    function init() {
        var todaysVisits;
        var panel = cfg.getPanel("#todaysactivityblock");
        var panelBody = cfg.getPanel("#todaysactivityblock", "body");
        var flatpickrCalender = panel + " #flatpickrCalender";
        var time = null;

        $(document).ready(function() {
            getTodaysActivity();

            $(flatpickrCalender).flatpickr({
                dateFormat: "d M Y",
                maxDate: "today",
                defaultDate: ["today"],
                onChange: function(selectedDates, dateStr, instance) {
                    getTodaysActivity(dateStr);
                }
            });
        });

        function getTodaysActivity(date) {
            var sesskey = $(panel).data("sesskey");
            $.ajax({
                url: cfg.requestUrl,
                type: cfg.requestType,
                dataType: cfg.requestDataType,
                data: {
                    action: 'get_todaysactivity_data_ajax',
                    sesskey: sesskey,
                    data: JSON.stringify({
                        date: date
                    })
                }
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
        }

        function generateTodaysVisitsGraph(data) {
            cfg.todaysActivityBlock.graph.data = data;
            var data = {
                labels: cfg.todaysActivityBlock.graph.labels,
                datasets: [{
                    label: cfg.todaysActivityBlock.graph.labelName,
                    data: cfg.todaysActivityBlock.graph.data,
                    backgroundColor: cfg.todaysActivityBlock.graph.backgroundColor
                }]
            };

            if (todaysVisits) {
                todaysVisits.destroy();
            }

            todaysVisits = new Chart(cfg.todaysActivityBlock.ctx, {
                type: cfg.todaysActivityBlock.graph.type,
                options: cfg.todaysActivityBlock.graph.options,
                data: data
            });
        }
    }

    // Must return the init function
    return {
        init: init
    };
});