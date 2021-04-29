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
    'local_edwiserreports/defaultconfig',
    'core/templates',
    'local_edwiserreports/select2'
], function(
    $, cfg, Templates
) {
    $(document).ready(function() {
        /**
         * Loader HTML String
         * @type {String}
         */
        var loader = '<span class="px-10 py-5 pull-right"><i class="fa fa-spinner fa-spin"></i></span>';

        // Add select2 for the dropdowns
        $('#ed_rpm').select2({
            multiple: true,
            closeOnSelect: false,
            placeholder: "Reporting Managers"
        });
        $('#ed_lps').select2({
            multiple: true,
            closeOnSelect: false,
            placeholder: "Learning Programs"
        });
        $('#ed_courses').select2({
            multiple: true,
            closeOnSelect: false,
            placeholder: "Courses"
        });
        $('#ed_cohorts').select2({
            multiple: true,
            closeOnSelect: false,
            placeholder: "Cohorts"
        });
        $('#ed_users').select2({
            multiple: true,
            closeOnSelect: false,
            placeholder: "Users"
        });
        $('#ed_activitytype').select2({
            maximumSelectionLength: 1,
            multiple: true,
            closeOnSelect: true,
            placeholder: "Activity Type"
        });


        // Change Learning Programs and accordignly get courses
        var selectedLps = ["0"];
        $('#ed_lps').on('change', function(event) {
            $("div[class^='lp']").show();

            var values = [];
            // Copy all option values from selected
            $(event.currentTarget).find("option:selected").each(function(i, selected) {
                values[i] = $(selected).val();
            });

            if (JSON.stringify(selectedLps) !== JSON.stringify(values)) {
                $("#ed_courses").html('')
                    .siblings('.select2-container')
                    .find('.select2-selection')
                    .html(loader);

                var oldIndex = selectedLps.indexOf("0"),
                    newIndex = values.indexOf("0");

                switch (true) {
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
                        var template = "local_edwiserreports/customquery_options";
                        var context = {courses: response};
                        Templates.render(template, context).then(function(html, js) {
                            Templates.replaceNodeContents($("#ed_courses"), html, js);
                            return;
                        }).fail(function(ex) {
                            console.log(ex);
                        });
                    })
                    .fail(function(error) {
                        console.log(error);
                    }).always(function() {
                        $("#ed_courses").select2({
                            multiple: true,
                            closeOnSelect: false,
                            placeholder: "Courses"
                        });
                    });

                // Hide checkboxes of Learning programs if LP is not selected
                if (!values.length) {
                    $("div[class^='lp']").hide();
                }
            }
        });
        // Reporting manager dropdown change
        var selectedRPM = ["0"];
        $('#ed_rpm').on('change', function(event) {
            var values = [];
            // Copy all option values from selected
            $(event.currentTarget).find("option:selected").each(function(i, selected) {
                values[i] = $(selected).val();
            });

            var cohortids = [];
            $("#ed_cohorts").find("option:selected").each(function(i, selected) {
                cohortids[i] = $(selected).val();
            });

            if (JSON.stringify(selectedRPM) !== JSON.stringify(values)) {
                $("div[class^='rpm']").show();
                $("#ed_lps").html('')
                    .siblings('.select2-container')
                    .find('.select2-selection')
                    .html(loader);
                $("#ed_courses").html('')
                    .siblings('.select2-container')
                    .find('.select2-selection')
                    .html(loader);
                $("#ed_users").html('')
                    .siblings('.select2-container')
                    .find('.select2-selection')
                    .html(loader);

                var oldIndex = selectedRPM.indexOf("0");
                var newIndex = values.indexOf("0");

                switch (true) {
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
                            rpmids: values,
                            cohortids: cohortids
                        })
                    },
                })
                .done(function(response) {
                    $("#ed_lps").html('');
                    $("#ed_courses").html('');
                    var template = "local_edwiserreports/customquery_lpoptions";
                    var context = {lps: response.lps};
                    if (response.lps.length > 0) {
                        Templates.render(template, context).then(function(html, js) {
                            Templates.appendNodeContents($("#ed_lps"), html, js);
                            return;
                        }).fail(function(ex) {
                            console.log(ex);
                        });
                    }
                    if (response.courses.length > 0) {
                        template = "local_edwiserreports/customquery_options";
                        context = {courses: response.courses};
                        Templates.render(template, context).then(function(html, js) {
                            Templates.appendNodeContents($("#ed_courses"), html, js);
                            return;
                        }).fail(function(ex) {
                            console.log(ex);
                        });
                    }

                    // Render users
                    if (response.users.length > 0) {
                        template = "local_edwiserreports/customquery_lpoptions"; // Work same as lp filter
                        context = {lps: response.users}; // Work same as lp filters
                        if (response.users.length > 0) {
                            Templates.render(template, context).then(function(html, js) {
                                Templates.appendNodeContents($("#ed_users"), html, js);
                                return;
                            }).fail(function(ex) {
                                console.log(ex);
                            });
                        }
                    }
                })
                .fail(function(error) {
                    console.log(error);
                }).always(function() {
                    $("#ed_lps").select2({
                        multiple: true,
                        closeOnSelect: false,
                        placeholder: "Learning Programs"
                    });
                    $("#ed_courses").select2({
                        multiple: true,
                        closeOnSelect: false,
                        placeholder: "Courses"
                    });
                    $("#ed_users").select2({
                        multiple: true,
                        closeOnSelect: false,
                        placeholder: "Users"
                    });
                });

                // Hide checkboxes of Learning programs if LP is not selected
                if (!values.length) {
                    $("div[class^='rpm']").hide();
                    $("div[class^='lp']").hide();
                }
            }
        });
        // Courses dropdown change
        var selectedCourses = ["0"];
        $('#ed_courses').on('change', function(event) {
            var values = [];

            // Copy all option values from selected
            $(event.currentTarget).find("option:selected").each(function(i, selected) {
                values[i] = $(selected).val();
            });

            if (JSON.stringify(selectedCourses) !== JSON.stringify(values)) {
                var oldIndex = selectedCourses.indexOf("0");
                var newIndex = values.indexOf("0");

                switch (true) {
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

        // Cohort dropdown change
        var selectedCohort = ["0"];
        $('#ed_cohorts').on('change', function(event) {
            var values = [];

            // Copy all option values from selected
            $(event.currentTarget).find("option:selected").each(function(i, selected) {
                values[i] = $(selected).val();
            });

            var rpmids = [];
            // Copy all option values from selected
            $("#ed_rpm").find("option:selected").each(function(i, selected) {
                rpmids[i] = $(selected).val();
            });

            if (JSON.stringify(selectedCohort) !== JSON.stringify(values)) {
                $("#ed_users").html('')
                    .siblings('.select2-container')
                    .find('.select2-selection')
                    .html(loader);

                var oldIndex = selectedCohort.indexOf("0");
                var newIndex = values.indexOf("0");

                switch (true) {
                    case (oldIndex == -1 && newIndex > -1):
                        // Assign the selected courses
                        values = ["0"];
                        selectedCohort = values;
                        $("#ed_cohorts").select2('val', values);
                        break;
                    case (oldIndex > -1 && newIndex > -1):
                        values.splice(newIndex, 1);

                        // Assign the selected courses
                        selectedCohort = values;
                        $("#ed_cohorts").select2('val', values);
                        break;
                }

                selectedCohort = values;

                // Load only cohort based users
                $.ajax({
                    url: cfg.requestUrl,
                    type: cfg.requestType,
                    dataType: cfg.requestDataType,
                    data: {
                        action: 'get_customqueryreport_cohort_users_ajax',
                        sesskey: M.cfg.sesskey,
                        data: JSON.stringify({
                            cohortids: values,
                            rpmids: rpmids
                        })
                    },
                })
                    .done(function(response) {
                        var template = "local_edwiserreports/customquery_lpoptions"; // Work same as lp filter
                        var context = {lps: response.users}; // Work same as lp filters
                        if (response.users.length > 0) {
                            Templates.render(template, context).then(function(html, js) {
                                Templates.appendNodeContents($("#ed_users"), html, js);
                                return;
                            }).fail(function(ex) {
                                console.log(ex);
                            });
                        }
                    })
                    .fail(function(error) {
                        console.log(error);
                    }).always(function() {
                        $("#ed_users").select2({
                            multiple: true,
                            closeOnSelect: false,
                            placeholder: "Users"
                        });
                    });
            }
        });

        // Cohort dropdown change
        var selectedUsers = ["-1"];
        $('#ed_users').on('change', function(event) {
            var values = [];

            // Copy all option values from selected
            $(event.currentTarget).find("option:selected").each(function(i, selected) {
                values[i] = $(selected).val();
            });

            if (JSON.stringify(selectedUsers) !== JSON.stringify(values)) {
                var oldIndex = selectedUsers.indexOf("0");
                var newIndex = values.indexOf("0");

                switch (true) {
                    case (oldIndex == -1 && newIndex > -1):
                        // Assign the selected courses
                        values = ["0"];
                        selectedUsers = values;
                        $("#ed_users").select2('val', values);
                        break;
                    case (oldIndex > -1 && newIndex > -1):
                        values.splice(newIndex, 1);

                        // Assign the selected courses
                        selectedUsers = values;
                        $("#ed_users").select2('val', values);
                        break;
                }

                selectedUsers = values;
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
         * @param  {String} selector  Selector id
         * @param  {string} type      picker type
         * @param  {string} startdate Start data
         * @param  {string} maxdate   End date
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
        };

        /*
         * Get custom report selectors
         * It may be courses of learning program
         * Courses | Learning Programs
         */
        const getDateSelectorData = function(selectedDates, dateStr, instance) {
            // Get selected time
            const time = selectedDates[0].getTime() / 1000;

            // Set values according to the selector
            switch (true) {
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
        };

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
            $('#customqueryenrollstart ~ input.form-control').val("");

            // Set form value startdate
            reportForm.find('input[name=enrolstartdate]').val("");
        });

        $(document).on('click', '#customqueryenrollend ~ button.input-search-close', function() {
            $('#customqueryenrollend ~ input.form-control').val("");

            // Set form value enddate
            reportForm.find('input[name=enrolenddate]').val("");
        });

        // Clear search input text
        $(document).on('click', '#customquerycompletionstart ~ button.input-search-close', function() {
            $('#customquerycompletionstart ~ input.form-control').val("");

            // Set form value startdate
            reportForm.find('input[name=completionenddate]').val("");
        });

        // Clear search input text
        $(document).on('click', '#customquerycompletionend ~ button.input-search-close', function() {
            $('#customquerycompletionend ~ input.form-control').val("");

            // Set form value enddate
            reportForm.find('input[name=completionstartdate]').val("");
        });

        /**
         * Function ti get the selected fileds from checkboxes.
         */
        function getSelectedFields() {
            var checkedFields = [];
            $(panel).find("input[type=checkbox]:checked").each(function(key, value) {
                checkedFields.push(value.id);
            });
            reportForm.find('input[name=reporttype]').val("queryReport");
            reportForm.find('input[name=checkedFields]').val(checkedFields);
        }

        /**
         * Function to get filtered values.
         */
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

            // Get selected cohorts
            var cohortids = [];
            cohortids = $(panel).find("#ed_cohorts").val();
            reportForm.find('input[name=cohortids]').val(cohortids);

            // Get selected users
            var userids = [];
            userids = $(panel).find("#ed_users").val();
            reportForm.find('input[name=userids]').val(userids);

            // Get selected activity type
            var activitytype = [];
            activitytype = $(panel).find("#ed_activitytype").val();
            reportForm.find('input[name=activitytype]').val(activitytype);
        }

        /**
         * Validate the custom report form
         * @param {DOM} element Dom element
         */
        function validateCustomQueryReportForm(element) {
            // Get courses
            const courses = $("#ed_courses > option:selected").length;

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
                setTimeout(function() {
                    $(".coursealert").hide();
                }, 3000);
                element.preventDefault();
            } else if (
                (data.enrolstartdate > data.enrolenddate) ||
                (data.enrolstartdate == "" && data.enrolenddate !== "") ||
                (data.enrolstartdate !== "" && data.enrolenddate == "")
            ) {
                $(".enroldatealert").show();
                setTimeout(function() {
                    $(".enroldatealert").hide();
                }, 3000);
                element.preventDefault();
            } else if (
                (data.completionstartdate > data.completionenddate) ||
                (data.completionstartdate == "" && data.completionenddate !== "") ||
                (data.completionstartdate !== "" && data.completionenddate == "")
            ) {
                $(".completiondatealert").show();
                setTimeout(function() {
                    $(".completiondatealert").hide();
                }, 3000);
                element.preventDefault();
            } else {
                getSelectedFields();
                getFilters();
            }
        }

        // Get selected fields and filter values on click of download reports
        $("#customQueryReportDownload").click(function(element) {
            validateCustomQueryReportForm(element);
        });

        // Handle alert for course selection
        $("[data-hide]").on("click", function() {
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
        });

        // On change of activity plugin
        var reportsTypeSelector = "#edw_custom_reporttype input[type='radio']";
        $(panel).find(reportsTypeSelector).on('change', function() {
            if ($(this).val() == 'activities') {
                $(panel).find('#ed_activitytype').closest('.activitytype.select').show();
                $(panel).find('.activityreportfields').show();
                $(panel).find('.activityfields input[type="checkbox"]').attr("checked", true);
            } else {
                $(panel).find('#ed_activitytype').closest('.activitytype.select').hide();
                $(panel).find('.activityreportfields').hide();
                $(panel).find('.activityfields input[type="checkbox"]').removeAttr("checked");
            }

            $(reportForm).find('input[name="reportlevel"]').val($(this).val());
        });
    });
});
