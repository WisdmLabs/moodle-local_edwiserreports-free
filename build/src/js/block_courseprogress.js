define(['jquery', 'core/chartjs', 'report_elucidsitereport/defaultconfig', "report_elucidsitereport/select2"], function ($, Chart, cfg) {
    function init() {
        var courseProgress = null;
        var panel = cfg.getPanel("#courseprogressblock");
        var panelBody = cfg.getPanel("#courseprogressblock", "body")
        var panelTitle = cfg.getPanel("#courseprogressblock", "header");
        var panelFooter = cfg.getPanel("#courseprogressblock", "footer");
        var selectedCourse = panelBody + " #wdm-courseprogress-select";
        var chart = panelBody + " .ct-chart";
        var loader = panelBody + " .loader";
        var exportUrlLink = panel + " .dropdown-menu[aria-labelledby='export-dropdown'] .dropdown-item";

        $(document).ready(function($) {
            getCourseProgressData();
            $(panelBody + ' .singleselect').select2();

            $(selectedCourse).on("change", function () {
                $(chart).addClass("d-none");
                $(loader).removeClass("d-none");

                if (courseProgress) {
                    courseProgress.destroy();
                }
                getCourseProgressData();
            });
        });

        function getCourseProgressData() {
            var courseId = $(selectedCourse).val();
            cfg.changeExportUrl(courseId, exportUrlLink, V.filterReplaceFlag);

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

            courseProgress = new Chart(cfg.courseProgressBlock.ctx, {
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