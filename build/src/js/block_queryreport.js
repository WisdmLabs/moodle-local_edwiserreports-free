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
        selectedLps = ["0"];
        $('#ed_lps').on('change', function(event){
            $( "div[class^='lp']" ).show();
            var values = [];
            // copy all option values from selected
            /*$(event.currentTarget).find("option:selected").each(function(i, selected){
               values[i] = $(selected).val();
            });
            if (values.length > 1) {
                var index = values.indexOf("0");
                if (index > -1) {
                   values.splice(index, 1);
                   event.preventDefault();
                   $("#ed_lps").select2('val', values);
                }
            }*/

            $(event.currentTarget).find("option:selected").each(function(i, selected){
               values[i] = $(selected).val();
            });

            if (JSON.stringify(selectedLps) !== JSON.stringify(values)) {
                oldIndex = selectedLps.indexOf("0");
                newIndex = values.indexOf("0");

                switch(true) {
                    case (oldIndex == -1 && newIndex > -1):
                        // Assign the selected courses
                        values = ["0"];
                        selectedLps = values;
                        $("#ed_lps").select2('val', values);
                        break;
                    case (oldIndex > -1 && newIndex > -1):
                        values.splice(newIndex, 1);

                        // Assign the selected courses
                        selectedLps = values;
                        $("#ed_lps").select2('val', values);
                        break;
                }

                selectedLps = values;

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
                            Templates.replaceNodeContents($("#ed_courses"), html , js);
                        }
                    );
                })
                .fail(function(error) {
                });
                // hide checkboxes of Learning programs if LP is not selected
                if (!values.length) {
                    $( "div[class^='lp']" ).hide();
                }
            }
        });
        // Reporting manager dropdown change
        selectedRPM = ["0"];
        $('#ed_rpm').on('change', function(event){
            $( "div[class^='rpm']" ).show();
            var values = [];
            // copy all option values from selected
            /*$(event.currentTarget).find("option:selected").each(function(i, selected){
               values[i] = $(selected).val();
            });
            if (values.length > 1) {
                var index = values.indexOf("0");
                if (index > -1) {
                   values.splice(index, 1);
                   $("#ed_rpm").select2('val', values);
                }
            }*/

            $(event.currentTarget).find("option:selected").each(function(i, selected){
               values[i] = $(selected).val();
            });

            if (JSON.stringify(selectedRPM) !== JSON.stringify(values)) {
                oldIndex = selectedRPM.indexOf("0");
                newIndex = values.indexOf("0");

                switch(true) {
                    case (oldIndex == -1 && newIndex > -1):
                        // Assign the selected courses
                        values = ["0"];
                        selectedRPM = values;
                        $("#ed_rpm").select2('val', values);
                        break;
                    case (oldIndex > -1 && newIndex > -1):
                        values.splice(newIndex, 1);

                        // Assign the selected courses
                        selectedRPM = values;
                        $("#ed_rpm").select2('val', values);
                        break;
                }

                selectedRPM = values;

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
                    $("#ed_courses").html('');
                    var template = "report_elucidsitereport/customquery_lpoptions";
                    var context = {lps:response.lps};
                    if (response.lps.length > 0) {
                        Templates.render(template, context).then(function(html, js) {
                                Templates.appendNodeContents($("#ed_lps"), html , js);
                            }
                        );
                    }
                    if (response.courses.length > 0) {
                        var template = "report_elucidsitereport/customquery_options";
                        var context = {courses:response.courses};
                        Templates.render(template, context).then(function(html, js) {
                                Templates.appendNodeContents($("#ed_courses"), html , js);
                            }
                        );
                    }
                })
                .fail(function(error) {
                    console.log(error);
                });
                // hide checkboxes of Learning programs if LP is not selected
                if (!values.length) {
                    $( "div[class^='rpm']" ).hide();
                    $( "div[class^='lp']" ).hide();
                }
            }
        });
        // Courses dropdown change
        var selectedCourses = ["0"];
        $('#ed_courses').on('change', function(event){
            var values = [];

            // Copy all option values from selected
            $(event.currentTarget).find("option:selected").each(function(i, selected){
               values[i] = $(selected).val();
            });

            if (JSON.stringify(selectedCourses) !== JSON.stringify(values)) {
                oldIndex = selectedCourses.indexOf("0");
                newIndex = values.indexOf("0");

                switch(true) {
                    case (oldIndex == -1 && newIndex > -1):
                        // Assign the selected courses
                        values = ["0"];
                        selectedCourses = values;
                        $("#ed_courses").select2('val', values);
                        break;
                    case (oldIndex > -1 && newIndex > -1):
                        values.splice(newIndex, 1);

                        // Assign the selected courses
                        selectedCourses = values;
                        $("#ed_courses").select2('val', values);
                        break;
                }

                selectedCourses = values;
            }
        });
        /**
         * Get panel of custom reports block
         * @type {string}
         */
        var panel = cfg.getPanel("#customQueryReportBlock");

        /**
         * Report form
         * @type {Object}
         */
        let reportForm = $(panel).find('#customQueryReportsForm');

        /**
         * Create flatpicker
         * @param  {string} type      [description]
         * @param  {string} startdate [description]
         * @param  {string} enddate   [description]
         * @return {[type]}           [description]
         */
        const createFlatpicker = function(selector, type, startdate, maxdate) {
            $(panel).find(selector).flatpickr({
                mode: type,
                altInput: true,
                altFormat: "d/m/Y",
                dateFormat: "Y-m-d",
                maxDate: maxdate,
                defaultDate: startdate,
                onClose: getDateSelectorData,
                onReady: getDateSelectorData
            });
        }

        /*
         * Get custom report selectors
         * It may be courses of learning program
         * Courses | Learning Programs
         */
        const getDateSelectorData = function(selectedDates, dateStr, instance) {
            // Get selected time
            const time = selectedDates[0].getTime() / 1000;

            // set values according to the selector
            switch(true) {
                case $(instance.element).is('#customqueryenrollstart'):
                    reportForm.find('input[name=enrolstartdate]').val(time);
                    break;

                case $(instance.element).is('#customqueryenrollend'):
                    reportForm.find('input[name=enrolenddate]').val(time);
                    break;

                case $(instance.element).is('#customquerycompletionstart'):
                    reportForm.find('input[name=completionstartdate]').val(time);
                    break;

                case $(instance.element).is('#customquerycompletionend'):
                    reportForm.find('input[name=completionenddate]').val(time);
                    break;
            }
        }

        // Create selectors for flatpicker
        /**
         * Flat Picker Selectors
         * @type {String}
         */
        const flatPickerSelectorStart = '#customqueryenrollstart, #customquerycompletionstart';
        const flatPickerSelectorEnd = '#customqueryenrollend, #customquerycompletionend';
        const threeMothsAgo = new Date().setDate(new Date().getDate() - 90);
        createFlatpicker(flatPickerSelectorStart, 'single', threeMothsAgo, 'today');
        createFlatpicker(flatPickerSelectorEnd, 'single', 'today', 'today');

        // Clear search input text
        $(document).on('click', '#customqueryenrollstart ~ button.input-search-close', function() {
            $('#customqueryenrollstart ~ input.form-control').val("")

            // Set form value startdate
            reportForm.find('input[name=enrolstartdate]').val("")
        });

        $(document).on('click', '#customqueryenrollend ~ button.input-search-close', function() {
            $('#customqueryenrollend ~ input.form-control').val("")

            // Set form value enddate
            reportForm.find('input[name=enrolenddate]').val("")
        });

        // Clear search input text
        $(document).on('click', '#customquerycompletionstart ~ button.input-search-close', function() {
            $('#customquerycompletionstart ~ input.form-control').val("")

            // Set form value startdate
            reportForm.find('input[name=completionenddate]').val("")
        });

        // Clear search input text
        $(document).on('click', '#customquerycompletionend ~ button.input-search-close', function() {
            $('#customquerycompletionend ~ input.form-control').val("")

            // Set form value enddate
            reportForm.find('input[name=completionstartdate]').val("")
        });

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

        /**
         * Validate the custom report form
         * @return {[type]} [description]
         */
        function validateCustomQueryReportForm(element) {
            // Get courses
            const courses = jQuery("#ed_courses > option:selected").length;

            // Formdata
            const formData = reportForm.serializeArray();

            // Get form data for validation
            const data = {};
            $.each(formData, function(idx, val) {
                data[val.name] = val.value;
            });

            // Validate form data
            if (courses < 1) {
                $(".coursealert").show();
                setTimeout(function(){
                    $(".coursealert").hide();
                }, 3000);
                element.preventDefault();
            } else if (
                (data.enrolstartdate > data.enrolenddate) ||
                (data.enrolstartdate == "" && data.enrolenddate !== "") ||
                (data.enrolstartdate !== "" && data.enrolenddate == "")
            ) {
                $(".enroldatealert").show();
                setTimeout(function(){
                    $(".enroldatealert").hide();
                }, 3000);
                element.preventDefault();
            } else if (
                (data.completionstartdate > data.completionenddate) ||
                (data.completionstartdate == "" && data.completionenddate !== "") ||
                (data.completionstartdate !== "" && data.completionenddate == "")
            ) {
                $(".completiondatealert").show();
                setTimeout(function(){
                    $(".completiondatealert").hide();
                }, 3000);
                element.preventDefault();
            } else {
                getSelectedFields();
                getFilters();
            }
        }

        // get selected fields and filter values on click of download reports
        $("#customQueryReportDownload").click(function(element){
            validateCustomQueryReportForm(element);
            /*var courses = jQuery("#ed_courses > option:selected").length;
            if (courses >= 1) {
                getSelectedFields();
                getFilters();
            } else {
                $(".coursealert").show();
                setTimeout(function(){
                    $(".coursealert").hide();
                }, 3000);
                e.preventDefault();
            }*/
        });

        // handle alert for course selection
        $("[data-hide]").on("click", function(){
            $(this).closest("." + $(this).attr("data-hide")).hide();
        });

        // Unselect all fields
        $('.reportfields a[class^="unselect-"]').on('click', function() {
            $(this).closest('.reportfields')
                .find('input[type="checkbox"]:not(:disabled)')
                .prop("checked", false);
            $(this).hide().siblings('a[class^="select-"]').show();
        });

        // Select  all fields
        $('.reportfields a[class^="select-"]').on('click', function() {
            $(this).closest('.reportfields')
                .find('input[type="checkbox"]:not(:disabled)')
                .prop("checked", true);
            $(this).hide().siblings('a[class^="unselect-"]').show();
        });

        // Select and unselect on click of checkbox
        $(panel).find('.checkbox-custom').on('click', function() {
            const allCheckboxCount = $(this).closest('.reportfields')
                .find('input[type="checkbox"]').length;
            const selectedCheckboxCount = $(this).closest('.reportfields')
                .find('input[type="checkbox"]:checked').length;

            // If all checkboxes are seleced
            if (allCheckboxCount == selectedCheckboxCount) {
                $(this).closest('.reportfields')
                    .find('a[class^="select-"]')
                    .hide().siblings('a[class^="unselect-"]').show();
            } else {
                $(this).closest('.reportfields')
                    .find('a[class^="unselect-"]')
                    .hide().siblings('a[class^="select-"]').show();
            }
        })
    });
});