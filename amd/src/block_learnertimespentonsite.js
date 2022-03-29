define([
    'jquery',
    './chart/apexcharts',
    './common',
    './defaultconfig',
    './select2'
], function(
    $,
    ApexCharts,
    Common,
    CFG
) {
    /**
     * Date picker.
     */
    var flatpickr = null;

    /**
     * Charts list.
     */
    var chart = null;

    /**
     * Filter for ajax.
     */
    var filter = {
        date: 'weekly'
    };

    /**
     * Line chart default config.
     */
    const lineChartDefault = {
        series: [],
        chart: {
            type: 'line',
            height: 350,
            dropShadow: {
                enabled: true,
                color: '#000',
                top: 18,
                left: 7,
                blur: 10,
                opacity: 0.2
            },
            toolbar: {
                show: false,
                tools: {
                    download: false,
                    reset: '<i class="fa fa-refresh"></i>'
                }
            },
            zoom: {
                enabled: false
            }
        },
        markers: {
            size: 0
        },
        tooltip: {
            enabled: true,
            enabledOnSeries: undefined,
            shared: true,
            followCursor: false,
            intersect: false,
            inverseOrder: false,
            fillSeriesColor: false,
            onDatasetHover: {
                highlightDataSeries: false,
            },
            items: {
                display: 'flex'
            },
            fixed: {
                enabled: false,
                position: 'topRight',
                offsetX: 0,
                offsetY: 0,
            },
            y: {
                title: {}
            }
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        grid: {
            borderColor: '#e7e7e7'
        },
        xaxis: {
            categories: null,
            type: 'datetime',
            labels: {
                hideOverlappingLabels: true,
                datetimeFormatter: {
                    year: 'yyyy',
                    month: 'MMM \'yy',
                    day: 'dd MMM',
                    hour: ''
                }
            },
            tooltip: {
                enabled: false
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'left',
            offsetY: '-20',
            itemMargin: {
                horizontal: 10,
                vertical: 0
            },
        },
        dataLabels: {
            enabled: false
        },
        colors: [CFG.getColorTheme()[2]]
    };

    /**
     * Pie chart default config.
     */
    const pieChartDefault = {
        chart: {
            type: 'donut',
            height: 350
        },
        legend: {
            position: 'bottom',
            offsetY: 0
        },
        noData: {
            text: M.util.get_string('nographdata', 'local_edwiserreports')
        },
        theme: {
            monochrome: {
                enabled: true,
                color: CFG.getColorTheme()[2],
                shadeTo: 'light',
                shadeIntensity: 0.65
            },
        }
    };

    /**
     * Selectors list.
     */
    var SELECTOR = {
        PANEL: '#learnertimespentonsiteblock',
        DATE: '.learnertimespentonsite-calendar',
        DATEMENU: '.learnertimespentonsite-calendar + .dropdown-menu',
        DATEITEM: '.learnertimespentonsite-calendar + .dropdown-menu .dropdown-item',
        DATEPICKER: '.learnertimespentonsite-calendar + .dropdown-menu .dropdown-calendar',
        DATEPICKERINPUT: '.learnertimespentonsite-calendar + .dropdown-menu .flatpickr',
        GRAPH: '#apex-chart-learnertimespentonsite-block',
    };

    /**
     * All promises.
     */
    var PROMISE = {
        /**
         * Get timespent on site using filters.
         * @param {Object} filter Filter data
         * @returns {PROMISE}
         */
        GET_TIMESPENTONSITE: function(filter) {
            return $.ajax({
                url: CFG.requestUrl,
                type: CFG.requestType,
                dataType: CFG.requestDataType,
                data: {
                    action: 'get_learnertimespentonsite_graph_data_ajax',
                    secret: M.local_edwiserreports.secret,
                    data: JSON.stringify({
                        filter: filter
                    })
                },
            });
        },
    }

    /**
     * Load graph
     */
    function loadGraph() {
        let data;
        Common.loader.show(SELECTOR.PANEL);

        /**
         * Render graph.
         * @param {DOM} graph Graph element
         * @param {Object} data Graph data
         */
        function renderGraph(graph, data) {
            if (chart !== null) {
                chart.destroy();
            }
            chart = new ApexCharts(graph.get(0), data);
            chart.render();
            setTimeout(function() {
                Common.loader.hide(SELECTOR.PANEL);
            }, 1000);
        }
        PROMISE.GET_TIMESPENTONSITE(filter)
            .done(function(response) {
                if (filter.date.includes("to") || ['weekly', 'monthly', 'yearly'].indexOf(filter.date) !== -1) {
                    data = Object.assign({}, lineChartDefault);
                    data.yaxis = {
                        labels: {
                            formatter: Common.timeFormatter
                        }
                    };
                    data.xaxis.categories = response.labels;
                    data.series = [{
                        name: M.util.get_string('timespentonlms', 'local_edwiserreports'),
                        data: response.timespent,
                    }];
                    data.chart.toolbar.show = response.labels.length > 30;
                    data.chart.zoom.enabled = response.labels.length > 30;
                    data.tooltip.y.title.formatter = (title) => {
                        return M.util.get_string('time', 'local_edwiserreports') + ': ';
                    }
                    $(SELECTOR.PANEL).find('.panel-body').attr('data-charttype', 'line');
                } else {
                    data = Object.assign({}, pieChartDefault);
                    data.labels = response.labels;
                    data.series = response.timespent;
                    data.tooltip = {
                        custom: function({ series, seriesIndex, dataPointIndex, w }) {
                            let value = Common.timeFormatter(series[seriesIndex], {
                                dataPointIndex: dataPointIndex
                            });
                            let label = w.config.labels[seriesIndex];
                            return `<div class="custom-donut-tooltip theme-2-text">
                                    <span style="font-weight: 500;"> ${label}:</span>
                                    <span style="font-weight: 700;"> ${value} </span>
                                </div>`;
                        }
                    };
                    data.legend = {
                        show: false
                    };
                    $(SELECTOR.PANEL).find('.panel-body').attr('data-charttype', 'donut');
                }
                renderGraph($(SELECTOR.PANEL).find(SELECTOR.GRAPH), data);
                Common.loader.hide(SELECTOR.PANEL);
            }).fail(function(exception) {
                Common.loader.hide(SELECTOR.PANEL);
            });
    }

    /**
     * After Select Custom date get active users details.
     * @param {String} target Targeted graph
     */
    function customDateSelected(target) {
        let date = $(SELECTOR.PANEL).find(SELECTOR.DATEPICKERINPUT).val(); // Y-m-d format
        let dateAlternate = $(SELECTOR.PANEL).find(SELECTOR.DATEPICKERINPUT).next().val(); // d/m/Y format

        /* If correct date is not selected then return false */
        if (date == '') {
            return;
        }

        // Set active class to custom date selector item.
        $(SELECTOR.PANEL).find(SELECTOR.DATEITEM).removeClass('active');
        $(SELECTOR.PANEL).find(SELECTOR.DATEITEM + '.custom').addClass('active');

        // Show custom date to dropdown button.
        $(SELECTOR.PANEL).find(SELECTOR.DATE).html(dateAlternate);
        filter.date = date;
        loadGraph(target);
    }

    /**
     * Initialize event listeners.
     */
    function initEvents() {

        /* Date selector listener */
        $('body').on('click', SELECTOR.DATEITEM + ":not(.custom)", function() {
            let target = $(this).closest(SELECTOR.FILTERS).data('id');
            // Set custom selected item as active.
            $(SELECTOR.PANEL).find(SELECTOR.DATEITEM).removeClass('active');
            $(this).addClass('active');

            // Show selected item on dropdown button.
            $(SELECTOR.PANEL).find(SELECTOR.DATE).html($(this).text());

            // Clear custom date.
            flatpickr.clear();

            // Set date.
            filter.date = $(this).data('value');

            // Load graph data.
            loadGraph();
        });

        flatpickr = $(SELECTOR.PANEL).find(SELECTOR.DATEPICKERINPUT).flatpickr({
            mode: 'range',
            altInput: true,
            altFormat: "d/m/Y",
            dateFormat: "Y-m-d",
            maxDate: "today",
            appendTo: $(SELECTOR.PANEL).find(SELECTOR.DATEPICKER).get(0),
            onOpen: function() {
                $(SELECTOR.PANEL).find(SELECTOR.DATEMENU).addClass('withcalendar');
            },
            onClose: function() {
                $(SELECTOR.PANEL).find(SELECTOR.DATEMENU).removeClass('withcalendar');
                customDateSelected();
            }
        });
    }

    /**
     * Initialize
     * @param {function} invalidUser Callback function
     */
    function init(invalidUser) {
        if ($(SELECTOR.PANEL).length == 0) {
            return;
        }

        initEvents();

        loadGraph();
    }
    return {
        init: init
    };
});
