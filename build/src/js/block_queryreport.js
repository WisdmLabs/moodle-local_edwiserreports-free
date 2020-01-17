define([
    'jquery',
    'report_elucidsitereport/defaultconfig',
    'core/templates',
    'report_elucidsitereport/select2'
 ], function (
    $, cfg,Templates
) {
    $(document).ready(function() {
        // Add select2 for the dropdowns
        $('#ed_rpm').select2({
            multiple:true
        });
        $('#ed_lps').select2({
            multiple:true
        });
        $('#ed_courses').select2({
            multiple:true
        });


        // Change Learning Programs and accordignly get courses
        $('#ed_lps').on('change', function(event){
            $( "div[class^='lp']" ).show();
            var values = [];
            // copy all option values from selected
            $(event.currentTarget).find("option:selected").each(function(i, selected){
               values[i] = $(selected).val();
            });
            $.ajax({
                url: cfg.requestUrl,
                type: cfg.requestType,
                dataType: cfg.requestDataType,
                data: {
                    action: 'get_customqueryreport_data_ajax',
                    sesskey: M.cfg.sesskey,
                    data: JSON.stringify({
                        lpids: values
                    })
                },
            })
            .done(function(response) {
                $("#ed_courses").html('');
                var template = "report_elucidsitereport/customquery_options";
                var context = {courses:response};
                Templates.render(template, context).then(function(html, js) {
                        Templates.appendNodeContents($("#ed_courses"), html , js);
                    }
                );
            })
            .fail(function(error) {
                console.log(error);
            });
            // hide checkboxes of Learning programs if LP is not selected
            if (!values.length) {
                $( "div[class^='lp']" ).hide();
            }
        });
        // Reporting manager dropdown change
        $('#ed_rpm').on('change', function(event){
            $( "div[class^='rpm']" ).show();
            var values = [];
            // copy all option values from selected
            $(event.currentTarget).find("option:selected").each(function(i, selected){
               values[i] = $(selected).val();
            });
            $.ajax({
                url: cfg.requestUrl,
                type: cfg.requestType,
                dataType: cfg.requestDataType,
                data: {
                    action: 'get_customqueryreport_rpm_data_ajax',
                    sesskey: M.cfg.sesskey,
                    data: JSON.stringify({
                        rpmids: values
                    })
                },
            })
            .done(function(response) {
                $("#ed_lps").html('');
                var template = "report_elucidsitereport/customquery_lpoptions";
                var context = {lps:response.lps};
                Templates.render(template, context).then(function(html, js) {
                        Templates.appendNodeContents($("#ed_lps"), html , js);
                    }
                );
                $("#ed_courses").html('');
                var template = "report_elucidsitereport/customquery_options";
                var context = {courses:response.courses};
                Templates.render(template, context).then(function(html, js) {
                        Templates.appendNodeContents($("#ed_courses"), html , js);
                    }
                );
            })
            .fail(function(error) {
                console.log(error);
            });
            // hide checkboxes of Learning programs if LP is not selected
            if (!values.length) {
                $( "div[class^='rpm']" ).hide();
                $( "div[class^='lp']" ).hide();
            }
        });

        /**
         * Get panel of custom reports block
         * @type {string}
         */
        var panel = cfg.getPanel("#customQueryReportBlock");

        let reportForm = $(panel).find('#customQueryReportsForm');
        /*
         * Get custom report selectors
         * It may be courses of learning program
         * Courses | Learning Programs
         */
        const getCustomEnrollSelector = function(selectedDates, dateStr, instance) {
            if (selectedDates.length == 2) {
                // Get starttime and end time
                let startTime = selectedDates[0].getTime()
                let endTime = selectedDates[1].getTime()
                // Set form value startdate and enddate
                reportForm.find('input[name=enrolstartdate]').val(startTime / 1000)
                reportForm.find('input[name=enrolenddate]').val(endTime / 1000)
            } else {
                // Set form value startdate and enddate
                reportForm.find('input[name=enrolstartdate]').val("")
                reportForm.find('input[name=enrolenddate]').val("")
            }
        }

        /**
         *  Create flatpicker to select custom date range
         */
        $(panel).find('#customqueryenroll').flatpickr({
            mode: 'range',
            altInput: true,
            altFormat: "d/m/Y",
            dateFormat: "Y-m-d",
            maxDate: "today",
            onClose: getCustomEnrollSelector
        });

        /*
         * Get custom report selectors
         * It may be courses of learning program
         * Courses | Learning Programs
         */
        const getCustomCompletionSelector = function(selectedDates, dateStr, instance) {
            if (selectedDates.length == 2) {
                // Get starttime and end time
                let startTime = selectedDates[0].getTime()
                let endTime = selectedDates[1].getTime()
                // Set form value startdate and enddate
                reportForm.find('input[name=completionstartdate]').val(startTime / 1000)
                reportForm.find('input[name=completionenddate]').val(endTime / 1000)
            } else {
                // Set form value startdate and enddate
                reportForm.find('input[name=completionstartdate]').val("")
                reportForm.find('input[name=completionenddate]').val("")
            }
        }
        /**
         *  Create flatpicker to select custom date range
         */
        $(panel).find('#customquerycompletion').flatpickr({
            mode: 'range',
            altInput: true,
            altFormat: "d/m/Y",
            dateFormat: "Y-m-d",
            maxDate: "today",
            onClose: getCustomCompletionSelector
        });

         // Clear search input text
        $(document).on('click', '#customqueryenroll ~ button.input-search-close', function() {
            $('#customqueryenroll ~ input.form-control').val("")

            // Set form value startdate and enddate
            reportForm.find('input[name=enrolstartdate]').val("")
            reportForm.find('input[name=enrolenddate]').val("")
        })

         // Clear search input text
        $(document).on('click', '#customquerycompletion ~ button.input-search-close', function() {
            $('#customquerycompletion ~ input.form-control').val("")

            // Set form value startdate and enddate
            reportForm.find('input[name=completionstartdate]').val("")
            reportForm.find('input[name=completionenddate]').val("")
        })

        // function ti get the selected fileds from checkboxes
        function getSelectedFields() {
            var checkedFields = [];
            $(panel).find("input[type=checkbox]:checked").each(function(key, value){
                checkedFields.push( value.id );
            });
            reportForm.find('input[name=reporttype]').val("queryReport");
            reportForm.find('input[name=checkedFields]').val(checkedFields);
        }
        // function to get filtered values
        function getFilters() {
            // Get Selected Reporting Managers
            var rpms = [];
            rpms = $(panel).find("#ed_rpm").val();
            reportForm.find('input[name=reportingmanagers]').val(rpms);
            // Get selected Learning Programs
            var lps = [];
            lps = $(panel).find("#ed_lps").val();
            reportForm.find('input[name=lps]').val(lps);
            // Get selected Courses
            var courses = [];
            courses = $(panel).find("#ed_courses").val();
            reportForm.find('input[name=courses]').val(courses);
        }
        // get selected fields and filter values on click of download reports
        $("#customQueryReportDownload").click(function(e){
            var courses = jQuery("#ed_courses > option:selected").length;
            if (courses >= 1) {
                getSelectedFields();
                getFilters();
            } else {
                $(".coursealert").show();
                setTimeout(function(){
                    $(".coursealert").hide();
                }, 3000);
                e.preventDefault();
            }
        });
        // handle alert for course selection
        $("[data-hide]").on("click", function(){
            $(this).closest("." + $(this).attr("data-hide")).hide();
        });
    });
});