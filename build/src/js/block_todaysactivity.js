define(['jquery', 'core/chartjs', 'report_elucidsitereport/defaultconfig'], function ($, Chart, cfg) {
    function init() {
        // Global data got todays activity block
        var todaysVisits;
        var panel = cfg.getPanel("#todaysactivityblock");
        var panelBody = cfg.getPanel("#todaysactivityblock", "body");
        var flatpickrCalender = panel + " #flatpickrCalender";
        var time = null;

        /**
         * On document ready do the bellow stuff
         */
        $(document).ready(function() {
            getTodaysActivity();

            /** 
             * Generate flatpicker for
             * date filter in todays activity block
             */
            $(flatpickrCalender).flatpickr({
                dateFormat: "d M Y",
                maxDate: "today",
                defaultDate: ["today"],
                onChange: function(selectedDates, dateStr, instance) {
                    $(panelBody + " .random").addClass("random-counter");
                    getTodaysActivity(dateStr);
                }
            });

            var count = 0;
            function randomCounter(){
                count = Math.round(Math.random()*5);
                $(panelBody + ' .random-counter').text(count);
                setTimeout(randomCounter, 20);
            }
            randomCounter();
        });

        /**
         * Get Todays activity information
         * @param  {number} date Unix Date
         */
        function getTodaysActivity(date) {
            // Get session key
            var sesskey = $(panel).data("sesskey");

            // Send Ajax call to get todays activity information
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
                /** 
                 * After getting todays activity information
                 * update the value in todays activity block
                 */
                $.each(response.data, function(indx, el) {
                    var section = $(panelBody + " #todays-" + indx);
                    section.removeClass("random-counter");
                    section.html(el);
                });

                /**
                 * Added Counter for todays activity block
                 */
                /*$(panelBody + ' .count').each(function () {
                    $(this).prop('Counter',0).animate({
                        Counter: $(this).text()
                    }, {
                        duration: 400,
                        easing: 'swing',
                        step: function (now) {
                            $(this).text(Math.ceil(now));
                        }
                    });
                });*/

                /* Generate Todays Activity Graph */
                generateTodaysVisitsGraph(response.data.visitshour);
            })
            .fail(function(error) {
                console.log(error);
            });
        }

        /**
         * Generate Todays Activity Graph
         * @param  {object} data Todays activity object
         */
        function generateTodaysVisitsGraph(data) {
            // Prepare data for generating graph
            cfg.todaysActivityBlock.graph.data = data;
            var data = {
                labels: cfg.todaysActivityBlock.graph.labels,
                datasets: [{
                    label: cfg.todaysActivityBlock.graph.labelName,
                    data: cfg.todaysActivityBlock.graph.data,
                    backgroundColor: cfg.todaysActivityBlock.graph.backgroundColor
                }]
            };

            /**
             * If Todays Activity graph is already
             * created then destroy
             */
            if (todaysVisits) {
                todaysVisits.destroy();
            }

            // Generate Todays Activity Graph
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