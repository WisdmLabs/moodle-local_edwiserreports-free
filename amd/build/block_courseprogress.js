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
        var exportUrlLink = panel + " .dropdown-menu[aria-labelledby='export-dropdown'] .dropdown-item";
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
