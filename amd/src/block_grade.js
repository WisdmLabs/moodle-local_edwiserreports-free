// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Grade table js.
 *
 * @package     local_edwiserreports
 * @copyright   2021 wisdmlabs <support@wisdmlabs.com>
 * @author      Yogesh Shirsath
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'core/ajax',
    'core/templates',
    './chart/apexcharts',
    './common',
    './defaultconfig',
    './select2'
], function(
    $,
    Ajax,
    Templates,
    ApexCharts,
    common,
    CFG
) {

    /**
     * Chart.
     */
    var chart = null;

    /**
     * Filter for ajax.
     */
    var filter = {
        course: 0,
        student: 0
    };

    /**
     * Pie chart default config.
     */
    const pieChartDefault = {
        chart: {
            type: 'pie',
            height: 350
        },
        fill: {
            type: 'solid',
        },
        legend: {
            position: 'bottom',
            offsetY: 0
        },
        colors: CFG.getColorPalette(),
        noData: {
            text: M.util.get_string('nographdata', 'local_edwiserreports')
        }
    };

    /**
     * Selectors list.
     */
    var SELECTOR = {
        PANEL: '#gradeblock',
        COURSE: '.course-select',
        STUDENT: '.student-select',
        GRAPH: '.graph',
        GRAPHLABEL: '.graph-label',
        FORMFILTER: '.download-links [name="filter"]',
        FILTERS: '.filters'
    };

    /**
     * All promises.
     */
    var PROMISE = {
        /**
         * Get students using course id.
         *
         * @param {Integer} courseid Course id
         * @returns {PROMISE}
         */
        GET_STUDENTS: function(courseid) {
            return Ajax.call([{
                methodname: 'local_edwiserreports_get_students_of_course',
                args: {
                    courseid: courseid
                }
            }], false)[0];
        },

        /**
         * Get graph data using filters.
         * @returns {PROMISE}
         */
        GET_GRAPH_DATA: function() {
            return $.ajax({
                url: CFG.requestUrl,
                type: CFG.requestType,
                dataType: CFG.requestDataType,
                data: {
                    action: 'get_grade_graph_data_ajax',
                    secret: M.local_edwiserreports.secret,
                    data: JSON.stringify({
                        filter: filter
                    })
                },
            });
        }
    };

    /**
     * Load graph
     */
    function loadGraph() {
        let data;
        common.loader.show(SELECTOR.PANEL);

        // Set export filter to download link.
        let exportFilter = Object.keys(filter).map(key => filter[key]).join("-");
        $(SELECTOR.PANEL).find(SELECTOR.FORMFILTER).val(exportFilter);

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
                common.loader.hide(SELECTOR.PANEL);
            }, 1000);
        }
        PROMISE.GET_GRAPH_DATA()
            .done(function(response) {
                data = Object.assign({}, pieChartDefault);
                data.labels = response.labels;
                data.series = response.grades;
                $(SELECTOR.PANEL).find(SELECTOR.GRAPHLABEL).text(response.header);
                common.insight('#gradeblock .insight', {
                    'insight': {
                        'value': '??',
                        'title': M.util.get_string('averagegrade', 'local_edwiserreports')
                    }
                });
                renderGraph($(SELECTOR.PANEL).find(SELECTOR.GRAPH), data);
            });
        common.loader.hide(SELECTOR.PANEL);
    }

    /**
     * Initialize event listeners.
     */
    function initEvents() {

        // Course selector listener.
        $('body').on('change', `${SELECTOR.PANEL} ${SELECTOR.COURSE}`, function() {
            courseid = parseInt($(this).val());
            filter.course = courseid;
            filter.student = 0;

            PROMISE.GET_STUDENTS(courseid)
                .done(function(response) {
                    // Destroy student selector select2 instance.
                    $(SELECTOR.PANEL).find(SELECTOR.STUDENT).select2('destroy');
                    Templates.render('local_edwiserreports/studentengagement/students_filter', { 'students': response })
                        .done(function(html, js) {
                            Templates.replaceNode($(SELECTOR.PANEL).find(SELECTOR.STUDENT), html, js);

                            // Reinitialize student selector select2 instance.
                            $(SELECTOR.PANEL).find(SELECTOR.STUDENT).select2();
                        });
                });
            // Load graph data.
            loadGraph();
        });

        // Student selector listener.
        $('body').on('change', `${SELECTOR.PANEL} ${SELECTOR.STUDENT}`, function() {
            filter.student = parseInt($(this).val());
            // Load graph data.
            loadGraph();
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
        loadGraph();
    }
    return {
        init: init
    };
});
