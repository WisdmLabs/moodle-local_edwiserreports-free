define([
    'jquery',
    'core/ajax',
    'core/modal_factory',
    'core/modal_events',
    'core/templates',
    'core/notification',
    'core/str',
    'local_edwiserreports/jquery.dataTables',
    'local_edwiserreports/dataTables.bootstrap4'
], function (
    $,
    ajax,
    modalFactory,
    modalEvents,
    templates,
    notif,
    str
) {
    'use strict';

    // Field group checkbox
    var cfCheckbox = '.field .custom-field-checkbox';
    var cfSelect = '.reports-filter-body .custom-field-select';
    var cfCohort = '#wdm-cohort-select';
    var cfCourse = '#wdm-course-select';
    var cfSave = '#wdm-custom-reports-save';
    var cfSaveForm = '#wdm-customreports-save-form';
    var crPageId = '#wdm_custom_reports_id';
    var crPageFullname = '#wdm_custom_reports_fullname';
    var crPageShortname = '#wdm_custom_reports_shortname';
    var crPageDownloadEnable = '#wdm_custom_reports_downloadenable';
    var crPageDesktopEnable = '#wdm_custom_reports_desktopenable';
    var crDesktopEnableSwitch = '[id^="wdm-desktopenable-"]';
    var crDeleteBtn = '#cr-list-table a[data-action="delete"]';
    var crHideBtn = '#cr-list-table a[data-action="hide"]';
    var cpHeader = '.reports-preview-header .rp-header';
    var cfPreviewTable = null;
    var crListTable = null;
    var customReportSaveTitle = M.util.get_string('savecustomreport', 'local_edwiserreports');

    var selectedFields = [];
    var courses = [];
    var cohorts = [];
    var reportsId = 0;
    var reportsDataLoaded = true;
    var reportsData = {
        downloadenable : true
    };

    var cfPreviewLoader = {
        selector : '.reports-preview-body .reports-preview-content.loader',

        show : function() {
            $(this.selector).removeClass('d-none').addClass('d-flex');
        },

        hide : function() {
            $(this.selector).removeClass('d-flex').addClass('d-none');
        }
    }

    var cfPreview = {
        selector : '.reports-preview-body .reports-preview-content',

        show : function() {
            $(this.selector + ':not(.loader)').removeClass('d-none').addClass('d-flex');
            $(this.selector + '.empty').removeClass('d-flex').addClass('d-none');
            cfPreviewLoader.hide();
        },

        hide : function() {
            $(this.selector + ':not(.loader)').removeClass('d-flex').addClass('d-none');
            $(this.selector + '.empty').removeClass('d-flex').addClass('d-none');
            cfPreviewLoader.show();
        },

        empty : function() {
            $(this.selector + '.empty').removeClass('d-none').addClass('d-flex');
            $(this.selector + ':not(.empty)').removeClass('d-flex').addClass('d-none');
            cfPreviewLoader.hide();
        }
    }

    var crListLoader = {
        selector : '.reports-list-body .reports-list-content.loader',

        show : function() {
            $(this.selector).removeClass('d-none').addClass('d-flex');
        },

        hide : function() {
            $(this.selector).removeClass('d-flex').addClass('d-none');
        }
    }

    var crList = {
        selector : '.reports-list-body .reports-list-content',

        show : function() {
            $(this.selector + ':not(.loader)').removeClass('d-none').addClass('d-flex');
            $(this.selector + '.empty').removeClass('d-flex').addClass('d-none');
            crListLoader.hide();
        },

        hide : function() {
            $(this.selector + ':not(.loader)').removeClass('d-flex').addClass('d-none');
            $(this.selector + '.empty').removeClass('d-flex').addClass('d-none');
            crListLoader.show();
        },

        empty : function() {
            $(this.selector + '.empty').removeClass('d-none').addClass('d-flex');
            $(this.selector + ':not(.empty)').removeClass('d-flex').addClass('d-none');
            crListLoader.hide();
        }
    }

    function getCustomReportsData() {
        if (reportsDataLoaded) {
            reportsDataLoaded = false;
            selectedFields = []
            cohorts = $(cfCohort).val();
            courses = $(cfCourse).val();
            $(cfCheckbox + ":checked").each(function() {
                selectedFields.push($(this).val());
            });

            var getCustomReportsDataAjax = ajax.call([{
                methodname: 'local_edwiserreports_get_customreports_data',
                args: {
                    params: JSON.stringify({
                        fields : selectedFields,
                        cohorts : cohorts,
                        courses : courses
                    })
                }
            }]);

            if (selectedFields.length == 0) {
                cfPreview.empty();
                $(cfSave).prop('disabled', true);
                reportsDataLoaded = true;
            } else {
                cfPreview.hide();
                getCustomReportsDataAjax[0].done(function(response) {
                    if (response.success) {
                        var data = JSON.parse(response.data);
                        console.log(data.reportsdata.length);
                        if (data.reportsdata.length == 0) {
                            cfPreview.empty();
                            $(cfSave).prop('disabled', true);
                        } else {
                            if (cfPreviewTable) {
                                cfPreviewTable.clear().destroy();
                                $('#cr-preview-table').html('');
                            }
                            cfPreview.show();
                            cfPreviewTable = $('#cr-preview-table').DataTable({
                                columns: data.columns,
                                data: data.reportsdata,
                                bInfo: false,
                                bFilter: false,
                                searching: false,
                                lengthChange: false,
                                drawCallback: function() {
                                    $('.dataTables_paginate > .pagination').addClass('pagination-sm pull-right');
                                    $('.dataTables_filter').addClass('pagination-sm pull-right');
                                }
                            });
                            $(cfSave).prop('disabled', false);
                        }
                    }
                }).always(function () {
                    reportsDataLoaded = true;
                });
            }
        }
    }

    function validateCustomReprtsSaveData(data) {
        var ret = true;
        if (data.reportname == '') {
            $('#id_error_crb_reportname')
                .html(M.util.get_string('emptyfullname', 'local_edwiserreports'))
                .show();
            ret = false;
        }

        if (data.reportshortname == '') {
            $('#id_error_crb_reportshortname')
                .html(M.util.get_string('emptyshortname', 'local_edwiserreports'))
                .show();
            ret = false;
        }

        var format = /[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
        if (format.test(data.reportshortname)) {
            $('#id_error_crb_reportshortname')
                .html(M.util.get_string('nospecialchar', 'local_edwiserreports'))
                .show();
            ret = false;
        }
        return ret;
    }

    function saveCustomReportsData() {
        modalFactory.create({
            title: customReportSaveTitle,
            type: modalFactory.types.SAVE_CANCEL,
            body: templates.render('local_edwiserreports/custom_reports_save_form', reportsData)
        }).done(function(modal) {
            var root = modal.getRoot();
            root.find('.modal-dialog').addClass('modal-lg');
            modal.show();
            root.on(modalEvents.save, function(e) {
                // Stop the default save button behaviour which is to close the modal.
                e.preventDefault();

                // Remove all error on type
                root.find('input').on('input', function() {
                    $('[id^="id_error_crb"]').html('');
                });

                var data = $(cfSaveForm).serializeArray().reduce(function(obj, item) {
                    obj[item.name] = item.value;
                    return obj;
                }, {});

                if (validateCustomReprtsSaveData(data)) {
                    // Prepare query data
                    data.querydata = {
                        'selectedfield': selectedFields,
                        'cohorts': cohorts,
                        'courses': courses
                    }

                    saveCustomReportsDataService(data, modal);
                }
            });
        });
    }

    function saveCustomReportsDataService(data, modal) {
        var saveCustomReportsData = ajax.call([{
            methodname: 'local_edwiserreports_save_customreports_data',
            args: {
                params: JSON.stringify(data)
            }
        }]);

        saveCustomReportsData[0].done(function(response) {
            if (response.success) {
                if (modal) {
                    reportsData = JSON.parse(response.reportsdata);
                    $(cpHeader).html(reportsData.reportname);
                    $(cfSave).html(M.util.get_string('updatereports', 'local_edwiserreports'));
                }
                $('#user-notifications .close').click();
                notif.addNotification({
                    message: M.util.get_string('reportssavesuccess', 'local_edwiserreports'),
                    type: "success"
                });
            } else {
                $('#user-notifications .close').click();
                notif.addNotification({
                    message: response.errormsg,
                    type: "error"
                });
            }
            if (modal) {
                $("html, body").animate({ scrollTop: 0 }, "slow");
                getCustomReportsList();
                modal.hide();
                modal.destroy();
            }
        });
    }

    function getCustomReportsList() {
        var getCustomReportsList = ajax.call([{
            methodname: 'local_edwiserreports_get_customreports_list',
            args: {
                params: JSON.stringify({})
            }
        }]);
        getCustomReportsList[0].done(function(response) {
            var data = JSON.parse(response.data);
            if (response.success) {
                if (data.length == 0) {
                    crList.empty();
                } else {
                    if (crListTable) {
                        crListTable.clear().destroy();
                        $('#cr-list-table').html('');
                    }

                    crListTable = $('#cr-list-table').DataTable({
                        columns: [
                            {
                                data: 'fullname',
                                title: M.util.get_string('title', 'local_edwiserreports')
                            },
                            {
                                data: 'createdby',
                                title: M.util.get_string('createdby', 'local_edwiserreports')
                            },
                            {
                                data: 'datecreated',
                                title: M.util.get_string('datecreated', 'local_edwiserreports')
                            },
                            {
                                data: 'datemodified',
                                title: M.util.get_string('datemodified', 'local_edwiserreports')
                            },
                            {
                                data: 'managehtml',
                                title: M.util.get_string('manage', 'local_edwiserreports')
                            }
                        ],
                        language: { 
                            searchPlaceholder: M.util.get_string('searchreports', 'local_edwiserreports'),
                            emptyTable: M.util.get_string('noresult', 'local_edwiserreports')
                        },
                        order: [[0, 'asc']],
                        data: data,
                        bInfo: false,
                        lengthChange: false,
                        drawCallback: function() {
                            $('.dataTables_paginate > .pagination').addClass('pagination-sm pull-right');
                            $('.dataTables_filter').addClass('pagination-sm pull-right');
                        }
                    });
                    crList.show();
                }
            }
        });
    }

    function customReportDelete(reportsId) {
        var deleteCustomReport = ajax.call([{
            methodname: 'local_edwiserreports_delete_custom_report',
            args: {
                params: JSON.stringify({reportsid: reportsId})
            }
        }]);
        deleteCustomReport[0].done(function(response) {
            if (response.success) {
                notif.addNotification({
                    message: response.message,
                    type: "success"
                });
            } else {
                notif.addNotification({
                    message: response.message,
                    type: "error"
                });
            }
        }).always(function() {
            $("html, body").animate({ scrollTop: 0 }, "slow");
            getCustomReportsList();
        })
    }

    function customReportServiceInit() {
        $(cfCheckbox).on('change', function () {
            getCustomReportsData();
        });
        $(cfSelect).on('change', function () {
            getCustomReportsData();
        });
        $(cfSave).on('click', function () {
            saveCustomReportsData();
        });
        getCustomReportsList();
        $(document).on('change', crDesktopEnableSwitch, function () {
            var enabledesktop = false;
            if ($(this).is(":checked")) {
                enabledesktop = true;
            }
            var updateData = {
                reportsid: $(this).data('reportsid'),
                enabledesktop: enabledesktop,
                action: 'enabledesktop'
            }

            saveCustomReportsDataService(updateData, false);
        });
        $(document).on('click', crDeleteBtn, function (e) {
            e.preventDefault();

            var reportId = $(this).data('reportsid');
            str.get_strings([
                {
                    key: 'deletecustomreportstitle',
                    component: 'local_edwiserreports'
                },
                {
                    key: 'deletecustomreportsquestion',
                    component: 'local_edwiserreports'
                },
                {
                    key: 'yes',
                    component: 'moodle'
                },
                {
                    key: 'no',
                    component: 'moodle'
                }
            ]).done(function(s) {
                notif.confirm(s[0], s[1], s[2], s[3], $.proxy(function() {
                    customReportDelete(reportId);
                }, e.currentTarget));
            });
        });
        $(document).on('click', crHideBtn, function (e) {
            e.preventDefault();

            var checkboxId = $(this).data('target');
            if ($(this).data('value')) {
                $(this).attr('title', $(this).data('titlehide'));
                $(this).attr('data-original-title', $(this).data('titlehide'));
                $(this).data('value', 0)
            } else {
                $(this).attr('title', $(this).data('titleshow'));
                $(this).attr('data-original-title', $(this).data('titleshow'));
                $(this).data('value', 1)
            }
            $(this).find('.icon').toggleClass('fa-eye');
            $(this).find('.icon').toggleClass('fa-eye-slash');
            $(checkboxId).trigger('click');
        });
    }

    return {
        init : function () {
            $(document).ready(function () {
                customReportServiceInit();
                if (reportsId = $(crPageId).val()) {
                    reportsData.reportsid = reportsId;
                    reportsData.reportname = $(crPageFullname).val();
                    reportsData.reportshortname = $(crPageShortname).val();

                    if ($(crPageDownloadEnable).val() != 0) {
                        reportsData.downloadenable = true;
                    } else {
                        reportsData.downloadenable = false;
                    }

                    if ($(crPageDesktopEnable).val() != 0) {
                        reportsData.enabledesktop = true;
                    } else {
                        reportsData.enabledesktop = false;
                    }

                    getCustomReportsData();
                }
            });
        }
    }
});
