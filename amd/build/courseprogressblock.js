define(['jquery', 'core/chartjs', 'report_elucidsitereport/defaultconfig'], function ($, Chart, defaultconfig) {
    function init() {
        var myPieChart;
        var panelBody = "#courseprogressblock .panel .panel-body";
        var panelTitle = "#courseprogressblock .panel .panel-header";
        var panelFooter = "#courseprogressblock .panel .panel-footer";
        var selectedCourse = panelTitle + " #id_courses";

        $(selectedCourse).on("change", function () {
            $(panelBody + " .ct-chart").addClass("d-none");
            $(panelBody + " .loader").removeClass("d-none");
            myPieChart.destroy();
            getCourseProgressData();
        });

        getCourseProgressData();
        function getCourseProgressData() {
            $(document).ready(function($) {
                var courseId = $(selectedCourse).val();
                $.ajax({
                    url: defaultconfig.requestUrl,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        action: 'get_courseprogress_graph_data_ajax',
                        data: JSON.stringify({
                            courseid: courseId
                        })
                    },
                })
                .done(function(response) {
                    defaultConfig.courseProgressBlock.graph.data = response.data;
                })
                .fail(function(error) {
                    console.log(error);
                })
                .always(function() {
                    generateCourseProgressGraph();
                    $(panelBody + " .ct-chart").removeClass("d-none");
                    $(panelBody + " .loader").addClass("d-none");
                });
            });
        }

        function generateCourseProgressGraph() {
            var data = {
                labels: defaultConfig.courseProgressBlock.graph.labels,
                datasets: [{
                    label: defaultConfig.courseProgressBlock.graph.label,
                    data: defaultConfig.courseProgressBlock.graph.data,
                    backgroundColor: defaultConfig.courseProgressBlock.graph.backgroundColor
                }]
            };

            myPieChart = new Chart(defaultConfig.courseProgressBlock.ctx, {
                data: data,
                type: defaultConfig.courseProgressBlock.graph.type,
                options: defaultConfig.courseProgressBlock.graph.options
            });
        }
    }

    // Must return the init function
    return {
        init: init
    };
});