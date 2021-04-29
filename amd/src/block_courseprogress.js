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
 * Plugin administration pages are defined here.
 *
 * @package     local_edwiserreports
 * @copyright   2021 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/* eslint-disable no-console */
define([
    'jquery',
    'core/chartjs',
    'local_edwiserreports/defaultconfig',
    'local_edwiserreports/variables',
    './common',
    'local_edwiserreports/select2'
], function($, Chart, cfg, V, common) {

    /**
     * Initialize
     * @param {function} notifyListner Callback function
     */
    function init(notifyListner) {
        var cpGraph = null;
        var panel = cfg.getPanel("#courseprogressblock");
        var panelBody = cfg.getPanel("#courseprogressblock", "body");
        var selectedCourse = panelBody + " #wdm-courseprogress-select";
        var chart = panelBody + " .ct-chart";
        var loader = panelBody + " .loader";
        var cpBlockData = false;
        var form = $(panel + ' form.download-links');

        /**
         * On document ready generate course progress block
         */
        $(document).ready(function($) {
            cpBlockData = cfg.getCourseProgressBlock();

            // If course progress block is there
            if (cpBlockData) {
                getCourseProgressData();
                $(panelBody + ' .singleselect').select2();

                $(selectedCourse).on("change", function() {
                    $(chart).hide();
                    $(loader).show();

                    getCourseProgressData();
                });
            } else {
                /* Notify that this event is completed */
                notifyListner("courseProgress");
            }
        });

        /**
         * Get progress data through ajax
         */
        function getCourseProgressData() {
            var courseId = $(selectedCourse).val();
            form.find('input[name="filter"]').val(courseId);

            // If progress graph already exist then destroy
            if (cpGraph) {
                cpGraph.destroy();
            }

            // Show loader.
            common.loader.show('#courseprogressblock');

            $.ajax({
                url: cfg.requestUrl,
                type: cfg.requestType,
                dataType: cfg.requestDataType,
                data: {
                    action: 'get_courseprogress_graph_data_ajax',
                    sesskey: $(panel).data("sesskey"),
                    data: JSON.stringify({
                        courseid: courseId
                    })
                },
            })
                .done(function(response) {
                    cpBlockData.graph.data = response.data;
                })
                .fail(function(error) {
                    console.log(error);
                })
                .always(function() {
                    cpGraph = generateCourseProgressGraph();
                    $(loader).hide();
                    $(chart).fadeIn("slow");

                    /* Notify that this event is completed */
                    notifyListner("courseProgress");

                    // Hide loader.
                    common.loader.hide('#courseprogressblock');
                });
        }

        /**
         * Generate course progress graph.
         * @returns {Object} chart object
         */
        function generateCourseProgressGraph() {
            // Create configuration data for course progress block
            var data = {
                labels: cpBlockData.graph.labels,
                datasets: [{
                    label: cpBlockData.graph.label,
                    data: cpBlockData.graph.data,
                    backgroundColor: cpBlockData.graph.backgroundColor
                }]
            };

            // Return chart object
            return new Chart(cpBlockData.ctx, {
                data: data,
                type: cpBlockData.graph.type,
                options: cpBlockData.graph.options
            });
        }
    }

    // Must return the init function
    return {
        init: init
    };
});
