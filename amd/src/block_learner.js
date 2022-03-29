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
     * List of graphs in this block.
     */
    var allGraphs = ['courseprogress', 'timespentonlms'];

    /**
     * Date picker.
     */
    var flatpickr = null;

    /**
     * Charts list.
     */
    var charts = {
        'courseprogress': null,
        'timespentonlms': null
    };

    /**
     * Filter for ajax.
     */
    var filter = {
        'courseprogress': {
            course: 0
        },
        'timespentonlms': {
            date: 'weekly'
        }
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
            floating: true
        },
        dataLabels: {
            enabled: false
        }
    };

    /**
     * Bar chart default config.
     */
    const barChartDefault = {
        series: [],
        chart: {
            type: 'bar',
            height: 350,
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
            marker: {
                show: true
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
        grid: {
            borderColor: '#e7e7e7'
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            categories: null,
            labels: {
                hideOverlappingLabels: true,
                trim: true,
                rotate: 300
            }
        },
        noData: {
            text: M.util.get_string('nographdata', 'local_edwiserreports')
        }
    };

    /**
     * Pie chart default config.
     */
    const pieChartDefault = {
        chart: {
            type: 'pie',
            height: 350
        },
        legend: {
            position: 'bottom',
            offsetY: 0
        },
        noData: {
            text: M.util.get_string('nographdata', 'local_edwiserreports')
        }
    };

    /**
     * Selectors list.
     */
    var SELECTOR = {
        PANEL: '#learnerblock',
        DATE: '.learner-calendar',
        DATEMENU: '.learner-calendar + .dropdown-menu',
        DATEITEM: '.learner-calendar + .dropdown-menu .dropdown-item',
        DATEPICKER: '.learner-calendar + .dropdown-menu .dropdown-calendar',
        DATEPICKERINPUT: '.learner-calendar + .dropdown-menu .flatpickr',
        COURSE: '.course-select',
        GRAPH: '.graph',
        GRAPHS: '.learner-graphs',
        FILTERS: '.filters',
        TIMESPENTONLMS: '.timespentonlms',
        COURSEPROGRESS: '.courseprogress'
    };

    /**
     * All promises.
     */
    var PROMISE = {
        /**
         * Get course progress using filters.
         * @param {Object} filter Filter data
         * @returns {PROMISE}
         */
        GET_COURSEPROGRESS: function(filter) {
            return $.ajax({
                url: CFG.requestUrl,
                type: CFG.requestType,
                dataType: CFG.requestDataType,
                data: {
                    action: 'get_learner_courseprogress_graph_data_ajax',
                    secret: M.local_edwiserreports.secret,
                    data: JSON.stringify({
                        filter: filter
                    })
                },
            });
        },
        /**
         * Get timespent on lms using filters.
         * @param {Object} filter Filter data
         * @returns {PROMISE}
         */
        GET_TIMESPENTONLMS: function(filter) {
            return $.ajax({
                url: CFG.requestUrl,
                type: CFG.requestType,
                dataType: CFG.requestDataType,
                data: {
                    action: 'get_learner_timespentonlms_graph_data_ajax',
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
     * @param {String} target Graph name
     */
    function loadGraph(target) {
        let data;
        Common.loader.show('#learnerblock .' + target);

        // Set export filter to download link.
        let exportFilter = Object.keys(filter[target]).map(key => filter[target][key]).join("-") + '-' + target;
        $(SELECTOR.PANEL).find(`.${target}`).find(SELECTOR.FORMFILTER).val(exportFilter);

        /**
         * Render graph.
         * @param {DOM} graph Graph element
         * @param {Object} data Graph data
         */
        function renderGraph(graph, data) {
            if (charts[target] !== null) {
                charts[target].destroy();
            }
            charts[target] = new ApexCharts(graph.get(0), data);
            charts[target].render();
            setTimeout(function() {
                Common.loader.hide('#learnerblock .' + target);
            }, 1000);
        }

        switch (target) {
            case 'courseprogress':
                PROMISE.GET_COURSEPROGRESS(filter[target])
                    .done(function(response) {
                        if (filter[target].course == 0) {
                            data = Object.assign({}, barChartDefault);
                            data.yaxis = {
                                max: 100,
                                labels: {
                                    formatter: (progress) => {
                                        return progress + '%';
                                    }
                                }
                            };
                            data.xaxis.categories = response.labels;
                            data.series = [{
                                name: M.util.get_string('courseprogress', 'local_edwiserreports'),
                                data: response.progress,
                            }];
                            data.chart.toolbar.show = response.labels.length > 30;
                            data.chart.zoom.enabled = response.labels.length > 30;
                            data.tooltip.y.title.formatter = (title) => {
                                return M.util.get_string('progress', 'local_edwiserreports') + ': ';
                            }
                        } else {
                            data = Object.assign({}, pieChartDefault);
                            data.colors = ['rgb(0, 227, 150)', 'rgb(254, 176, 25)'];
                            data.labels = response.labels;
                            data.series = response.progress;
                        }
                        renderGraph($(SELECTOR.PANEL).find(SELECTOR.COURSEPROGRESS).find(SELECTOR.GRAPH), data);
                    }).fail(function(exception) {
                        Common.loader.hide('#learnerblock .' + target);
                    });
                break;
            case 'timespentonlms':
                PROMISE.GET_TIMESPENTONLMS(filter[target])
                    .done(function(response) {
                        if (filter[target].date.includes("to") || ['weekly', 'monthly', 'yearly'].indexOf(filter[target].date) !== -1) {
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
                        } else {
                            data = Object.assign({}, pieChartDefault);
                            data.labels = response.labels;
                            data.series = response.timespent;
                            data.tooltip = {
                                y: {
                                    formatter: Common.timeFormatter
                                }
                            };
                            data.legend = {
                                show: false
                            };
                        }
                        renderGraph($(SELECTOR.PANEL).find(SELECTOR.TIMESPENTONLMS).find(SELECTOR.GRAPH), data);
                        Common.loader.hide('#learnerblock .' + target);
                    }).fail(function(exception) {
                        Common.loader.hide('#learnerblock .' + target);
                    });
                break;
            default:
                Common.loader.hide('#learnerblock .' + target);
                break;
        }
    }

    /**
     * After Select Custom date get active users details.
     * @param {String} target Targeted graph
     */
    function customDateSelected(target) {
        let date = $(SELECTOR.PANEL).find('.' + target).find(SELECTOR.DATEPICKERINPUT).val(); // Y-m-d format
        let dateAlternate = $(SELECTOR.PANEL).find('.' + target).find(SELECTOR.DATEPICKERINPUT).next().val(); // d/m/Y format

        // Set active class to custom date selector item.
        $(SELECTOR.PANEL).find('.' + target).find(SELECTOR.DATEITEM).removeClass('active');
        $(SELECTOR.PANEL).find('.' + target).find(SELECTOR.DATEITEM + '.custom').addClass('active');

        // Show custom date to dropdown button.
        $(SELECTOR.PANEL).find('.' + target).find(SELECTOR.DATE).html(dateAlternate);
        filter[target].date = date;
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
            $(SELECTOR.PANEL).find('.' + target).find(SELECTOR.DATEITEM).removeClass('active');
            $(this).addClass('active');

            // Show selected item on dropdown button.
            $(SELECTOR.PANEL).find('.' + target).find(SELECTOR.DATE).html($(this).text());

            // Clear custom date.
            flatpickr.clear();

            // Set date.
            filter[target].date = $(this).data('value');

            // Load graph data.
            loadGraph(target);
        });

        // Course selector listener.
        $('body').on('change', `${SELECTOR.PANEL} ${SELECTOR.COURSE}`, function() {
            let target = $(this).closest(SELECTOR.FILTERS).data('id');
            let courseid = parseInt($(this).val());
            filter[target].course = courseid;

            // Load graph data.
            loadGraph(target);
        });

        // Initialize date selector.
        let target = 'timespentonlms';
        flatpickr = $(SELECTOR.PANEL).find('.' + target).find(SELECTOR.DATEPICKERINPUT).flatpickr({
            mode: 'range',
            altInput: true,
            altFormat: "d/m/Y",
            dateFormat: "Y-m-d",
            maxDate: "today",
            appendTo: $(SELECTOR.PANEL).find('.' + target).find(SELECTOR.DATEPICKER).get(0),
            onOpen: function() {
                $(SELECTOR.PANEL).find('.' + target).find(SELECTOR.DATEMENU).addClass('withcalendar');
            },
            onClose: function() {
                $(SELECTOR.PANEL).find('.' + target).find(SELECTOR.DATEMENU).removeClass('withcalendar');
                customDateSelected(target);
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
        $(SELECTOR.PANEL).find('.singleselect').select2();
        initEvents();
        allGraphs.forEach(function(target) {
            loadGraph(target);
        });
    }
    return {
        init: init
    };
});
