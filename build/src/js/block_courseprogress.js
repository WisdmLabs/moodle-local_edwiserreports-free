define(['jquery', 'core/chartjs', 'report_elucidsitereport/defaultconfig'], function ($, Chart, cfg) {
    function init() {
        var courseProgress = null;
        var panel = cfg.getPanel("#courseprogressblock");
        var panelBody = cfg.getPanel("#courseprogressblock", "body")
        var panelTitle = cfg.getPanel("#courseprogressblock", "header");
        var panelFooter = cfg.getPanel("#courseprogressblock", "footer");
        var selectedCourse = panelBody + " #wdm-courseprogress-select";
        var chart = panelBody + " .ct-chart";
        var loader = panelBody + " .loader";

        $(selectedCourse).on("change", function () {
            $(chart).addClass("d-none");
            $(loader).removeClass("d-none");

            if (courseProgress) {
                courseProgress.destroy();
            }
            getCourseProgressData();
        });

        getCourseProgressData();
        function getCourseProgressData() {
            $(document).ready(function($) {
                var courseId = $(selectedCourse).val();
                $.ajax({
                    url: cfg.requestUrl,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        action: 'get_courseprogress_graph_data_ajax',
                        sesskey: $(panel).data("sesskey"),
                        data: JSON.stringify({
                            courseid: courseId
                        })
                    },
                })
                .done(function(response) {
                    cfg.courseProgressBlock.graph.data = response.data;
                })
                .fail(function(error) {
                    console.log(error);
                })
                .always(function() {
                    generateCourseProgressGraph();
                    $(loader).addClass("d-none");
                    $(chart).removeClass("d-none");
                });
            });
        }

        function generateCourseProgressGraph() {
            var data = {
                labels: cfg.courseProgressBlock.graph.labels,
                datasets: [{
                    label: cfg.courseProgressBlock.graph.label,
                    data: cfg.courseProgressBlock.graph.data,
                    backgroundColor: cfg.courseProgressBlock.graph.backgroundColor
                }]
            };

            myPieChart = new Chart(cfg.courseProgressBlock.ctx, {
                data: data,
                type: cfg.courseProgressBlock.graph.type,
                options: cfg.courseProgressBlock.graph.options
            });
        }
    }

    // Must return the init function
    return {
        init: init
    };
});