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
     * Selectors list.
     */
    var SELECTOR = {
        PANEL: '#learnertimespentonsiteblock',
        GRAPH: '#apex-chart-learnertimespentonsite-block'
    };

    /**
     * All promises.
     */
    var PROMISE = {
        /**
         * Get timespent on site using filters.
         * @returns {PROMISE}
         */
        GET_TIMESPENTONSITE: function() {
            return $.ajax({
                url: CFG.requestUrl,
                type: CFG.requestType,
                dataType: CFG.requestDataType,
                data: {
                    action: 'get_learnertimespentonsite_graph_data_ajax',
                    secret: M.local_edwiserreports.secret
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
                renderGraph($(SELECTOR.PANEL).find(SELECTOR.GRAPH), data);
                Common.loader.hide(SELECTOR.PANEL);
            }).fail(function(exception) {
                Common.loader.hide(SELECTOR.PANEL);
            });
    }

    /**
     * Initialize event listeners.
     */
    function initEvents() {

        flatpickr = $(SELECTOR.PANEL).find(SELECTOR.DATEPICKERINPUT).flatpickr({
            mode: 'range',
            altInput: true,
            altFormat: "d/m/Y",
            dateFormat: "Y-m-d",
            maxDate: "today",
            appendTo: $(SELECTOR.PANEL).find(SELECTOR.DATEPICKER).get(0)
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
