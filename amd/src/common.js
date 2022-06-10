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
 * @copyright   2021 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/* eslint-disable no-console */
define([
    'jquery',
    'core/notification',
    'core/fragment',
    'core/modal_factory',
    'core/modal_events',
    'core/templates',
    'core/str',
    './variables',
    './selectors',
    './jspdf',
    './select2',
    './jquery.dataTables',
    './dataTables.bootstrap4'
], function(
    $,
    Notification,
    Fragment,
    ModalFactory,
    ModalEvents,
    Templates,
    str,
    v,
    selector,
    jsPDF
) {
    var emailListTable = null;
    /**
     * Email Shcedule Modal Related Psrametres start
     */
    // Form root
    var formRoot = '#scheduletab';

    // Dropdowns
    var dropdowns = formRoot + ' .dropdown a.dropdown-item';
    var dropdownSelector = 'button.dropdown-toggle';

    // Duration dropdown selectors
    var durationSelector = formRoot + ' .dropdown.duration-dropdown a.dropdown-item';
    var durationInput = formRoot + ' input#esr-sendduration';

    // Times dropdown selector
    var timesDropdown = formRoot + ' .dropdown:not(.duration-dropdown)';
    var timesDropdownBtn = timesDropdown + ' button.dropdown-toggle';
    var timesDropdownLink = timesDropdown + ' a.dropdown-item';
    var dailyDropdownBtn = formRoot + ' .dropdown.daily-dropdown button.dropdown-toggle';
    var weeklyDropdownBtn = formRoot + ' .dropdown.weekly-dropdown button.dropdown-toggle';
    var monthlyDropdownBtn = formRoot + ' .dropdown.monthly-dropdown button.dropdown-toggle';

    var timeInput = formRoot + ' input#esr-sendtime';

    // For email schedule setting
    var settingBtn = "#listemailstab .esr-email-sched-setting";
    var deleteBtn = "#listemailstab .esr-email-sched-delete";
    var emailListToggleSwitch = "#listemailstab .esr-switch";

    // Messaged for email schedule
    var schedulerrormsg = M.util.get_string('scheduleerrormsg', 'local_edwiserreports');
    var schedulesuccessmsg = M.util.get_string('schedulesuccessmsg', 'local_edwiserreports');
    var deletesuccessmsg = M.util.get_string('deletesuccessmsg', 'local_edwiserreports');
    var deleteerrormsg = M.util.get_string('deleteerrormsg', 'local_edwiserreports');
    var emptyerrormsg = M.util.get_string('emptyerrormsg', 'local_edwiserreports');
    var emailinvaliderrormsg = M.util.get_string('emailinvaliderrormsg', 'local_edwiserreports');

    var tabs = '[data-plugin="tabs"] .nav-link, [data-plugin="tabs"] .tab-pane';
    var formTab = '[aria-controls="scheduletab"], #scheduletab';

    var windowLoader = '<div id="cover-spin"></div>';

    // Regular expression for email validation.
    var emailRegex = /^[a-zA-Z0-9]+[a-zA-Z0-9+_.-]+[a-zA-Z0-9]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]*\.[a-zA-Z]{2,}$/;

    /**
     * Selectors list.
     */
    let SELECTOR = {
        TABLE: '.edwiserreports-table',
        FILTER: '.table-filter',
        SEARCHTABLE: '.table-search-input input',
        PAGINATION: '.table-pagination',
        PAGINATIONITEM: '.paginate_button'
    };

    Templates.render('local_edwiserreports/insight-placeholder', {});
    Templates.render('local_edwiserreports/insight', {});

    // Loader functions.
    var loader = {
        show: function(id, position) {
            var $class;
            if (id == undefined) {
                id = 'body';
                $class = 'position-fixed';
            } else {
                $class = 'position-absolute';
            }
            if (position != undefined) {
                $class = position;
            }
            $(id).append(`
            <div class="edwiserreports-loader ${$class}">
                <div class="animation-wrapper">
                    <div class="fa-animation">
                        <i class="fa fa-cog fa-lg fa-spin"></i>
                        <i class="fa fa-cog fa-md fa-spin spin-reverse"></i>
                        <i class="fa fa-cog fa-sm fa-spin spin-reverse"></i>
                    </div>
                    ${M.util.get_string('loading', 'moodle')}
                </div>
            </div>
            `);
        },
        hide: function(id) {
            if (id == undefined) {
                id = 'body';
            }
            $(id + ' > .edwiserreports-loader').remove();
        }
    };

    /**
     * Validate comma separated emails.
     * @param {String} emails Comma separated emails
     * @param {Boolean} highlight Highlight error if email is invalid
     * @return {Boolean}
     */
    function validateEmails(emails, highlight = true) {
        var valid = true;
        var domElement = $('[name="esrrecepient"]').get(0);
        var duplicates = [],
            duplicate;
        emails.replaceAll(' ', '').replaceAll(';', ',').split(',')
            .forEach(email => {
                if (duplicates[email] != undefined) {
                    duplicate = true;
                }
                duplicates[email] = true;
                valid = valid && emailRegex.test(email);
            });
        if (duplicate) {
            if (highlight) {
                domElement.setCustomValidity('Invalid email');
                $(domElement).next().text(M.util.get_string('duplicateemail', 'local_edwiserreports'));
            }
            return false;
        }
        if (!valid) {
            if (highlight) {
                domElement.setCustomValidity('Invalid email');
                $(domElement).next().text(M.util.get_string('invalidemail', 'local_edwiserreports'));
            }
            return false;
        }
        if (highlight) {
            domElement.setCustomValidity('');
        }
        return true;
    }

    /**
     * Email Shcedule Modal Related Psrametres end
     */

    $(document).ready(function() {
        var exportPdf = '.download-links button[value="pdf"]';
        var exportCsv = '.download-links button[value="csv"][type="button"]';
        var exportExcel = '.download-links button[value="excel"][type="button"]';
        var scheduledEmailDropdown = '.download-links button[value="email"]';

        // Show pro feature warning for Excel and CSV export.
        $(document).on('click', [exportCsv, exportExcel].join(', '), function() {
            var $this = $(this);
            ModalFactory.create({
                    title: '',
                    type: ModalFactory.types.default,
                    body: Fragment.loadFragment(
                        'local_edwiserreports',
                        'export_data_warning',
                        1, {
                            warning: $this.val() == 'csv' ? 'csvprowarning' : 'excelprowarning'
                        }
                    ),
                })
                .done(function(modal) {
                    var root = modal.getRoot();
                    root.find('.modal-header').addClass('d-none');
                    root.find('.modal-dialog').addClass('export-notice-modal');

                    root.on(ModalEvents.hidden, function() {
                        modal.destroy();
                    });

                    root.on('click', '[data-action="download"]', function() {
                        // Following is my dirty hack to handle warning and downloading report using same button.

                        // Change button to type submit to download report.
                        $this.attr('type', 'submit');

                        // Simulate click to start downloading report.
                        $this.click();

                        // Change button to type to button to show this warning again.
                        $this.attr('type', 'button');
                        modal.hide();
                    });
                    modal.show();
                });
        });

        // Validating email field of schedule email form.
        $(document).on('input', '[name="esrrecepient"]', function() {
            validateEmails($(this).val());
        });

        // Validating schedule email form fields.
        $(document).on('input', '[name="esrname"], [name="esrrecepient"], [name="esrsubject"]', function() {
            var name = $('[name="esrname"]').val() == "";
            var recepient = !validateEmails($('[name="esrrecepient"]').val(), false);
            var subject = $('[name="esrsubject"]').val() == "";
            var invalid = name || recepient || subject;
            $(this).closest('.fitem').addClass('was-validated');
            $('.modal-footer.schedule-email').find(`[data-action="save"], [data-action="send"]`).prop('disabled', invalid);
            $('#scheduletab .date-filters').toggleClass('disabled', invalid);
        });

        // Highlight invalid input.
        $(document).on('blur', '[name="esrname"], [name="esrrecepient"], [name="esrsubject"]', function() {
            $(this).closest('.fitem').addClass('was-validated');
        });

        // Export data in pdf
        $(document).on("click", exportPdf, function() {
            var _this = this;
            var form = $(_this).closest("form");
            loader.show('body', 'position-fixed');
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: {
                    'type': $(_this).val(),
                    'block': form.find('input[name="block"]').val(),
                    'filter': form.find('input[name="filter"]').val(),
                    'cohortid': form.find('input[name="cohortid"]').val(),
                    'region': form.find('input[name="region"]').val()
                }
            }).done(function(response) {
                try {
                    response = JSON.parse(response);
                    var orientation = 'p';
                    var format = 'a4';
                    if (response.options != undefined) {
                        if (response.options.orientation != undefined) {
                            orientation = response.options.orientation;
                        }
                        if (response.options.format != undefined) {
                            format = response.options.format;
                        }
                    }
                    var pdf = jsPDF(orientation, 'pt', format);
                    var margins = {
                        top: 40,
                        bottom: 30,
                        left: 10,
                        width: "100%"
                    };

                    pdf.setFontSize(10);

                    // ID selector for either ID or node name. ("#iAmID", "div", "span" etc.)
                    // There is no support for any other type of selectors
                    // (class, of compound) at this time.
                    var specialElementHandlers = {
                        // Element with id of "bypass" - jQuery style selector
                        // eslint-disable-next-line no-unused-vars
                        '#bypassme': function(element, renderer) {
                            // True = "handled elsewhere, bypass text extraction"
                            return true;
                        }
                    };
                    // All coords and widths are in jsPDF instance's declared units
                    // 'inches' in this case
                    pdf.fromHTML(
                        response.data.html, // HTML string or DOM elem ref.
                        margins.left, // X coord
                        margins.top, { // Y coord
                            'width': margins.width, // Max width of content on PDF
                            'elementHandlers': specialElementHandlers
                        },
                        // eslint-disable-next-line no-unused-vars
                        function(dispose) {
                            // Dispose: object with X, Y of the last line add to the PDF
                            //          this allow the insertion of new lines after html
                            pdf.save(response.data.filename);
                        }, margins);
                } catch (Exception) {
                    loader.hide('body');
                }
            }).always(function() {
                loader.hide('body');
            });
        });

        /**
         * Schedule emails to send reports.
         */
        $(document).on("click", scheduledEmailDropdown, function() {
            var data = v.getScheduledEmailFormContext();
            var form = $(this).closest('form');
            var formData = form.serializeArray();
            $(formData).each(function($k, $d) {
                data[$d.name] = $d.value;
            });

            var modalTitle = M.util.get_string('scheduleemailfor', 'local_edwiserreports');
            if (data.block.includes("customreportsblock")) {
                modalTitle += ' ' + $('#' + data.block).data('blockname');
            } else {
                modalTitle += ' ' + M.util.get_string(data.block + 'exportheader', 'local_edwiserreports');
            }

            ModalFactory.create({
                    title: modalTitle,
                    body: Fragment.loadFragment(
                        'local_edwiserreports',
                        'email_schedule_tabs',
                        1, {
                            data: JSON.stringify(data)
                        }
                    )
                })
                .done(function(modal) {
                    var root = modal.getRoot();
                    root.find('.modal-header').addClass('border-bottom-0');
                    root.find('.modal-title').addClass('h4 font-weight-600');
                    modal.modal.addClass("modal-lg");

                    root.on(ModalEvents.bodyRendered, function() {
                        root.find("#esr-blockname").val(data.blockname);
                        root.find("#esr-region").val(data.region);
                        root.find("#esr-sesskey").val(data.sesskey);
                        emailListTable = renderAllScheduledEmails(data, modal);
                    });

                    root.on(ModalEvents.hidden, function() {
                        modal.destroy();
                    });

                    emailScheduleFormInit(data, root, modal);
                    modal.show();
                });
        });

        // Search in table.
        $(document).on('input', '#listemailstab .table-search-input input', function() {
            emailListTable.search(this.value).draw();
        });

        // Show reports page when document is ready
        $('#wdm-edwiserreports').removeClass('d-none');

        setupBlockEditing();

        setupBlockHiding($('#wdm-edwiserreports').data("editing"));
    });

    /**
     * Setup block hiding
     * @param {Boolean} editing True if editing is on
     */
    function setupBlockHiding(editing) {
        var editForm = '<div id="editing-btn" class="d-flex flex-wrap">';
        editForm += '<div class="ml-auto d-flex">';
        var editingtxt;

        // Change editing option
        if (editing) {
            editing = 0;
            editingtxt = "Stop Customising this page";

            editForm += '<div class="singlebutton">';
            editForm += '<form method="post" action="' + M.cfg.wwwroot + '/local/edwiserreports/index.php">';
            editForm += '<input type="hidden" name="reset" value="1">';
            editForm += '<input type="hidden" name="sesskey" value="' + M.cfg.sesskey + '">';
            editForm += '<button type="submit" class="btn btn-secondary" title="">Reset Page to Default</button>';
            editForm += '</form>';
            editForm += '</div>';
        } else {
            editing = 1;
            editingtxt = "Customise this page";
        }

        editForm += '<div class="singlebutton">';
        editForm += '<form method="post" action="' + M.cfg.wwwroot + '/local/edwiserreports/index.php">';
        editForm += '<input type="hidden" name="edit" value="' + editing + '">';
        editForm += '<input type="hidden" name="sesskey" value="' + M.cfg.sesskey + '">';
        editForm += '<button type="submit" class="btn btn-secondary" title="">' + editingtxt + '</button>';
        editForm += '</form>';
        editForm += '</div>';

        editForm += '</div></div>';

        $("#page-local-edwiserreports-index #page-header .card-body").append(editForm);
    }

    /**
     * Setup block setting button
     */
    function setupBlockEditing() {
        $(document).on('click', selector.blockSettingsBtn, function(e) {
            e.preventDefault();

            var contextid = $(e.currentTarget).data('contextid');
            var blockname = $(e.currentTarget).data('blockname');
            var action = $(e.currentTarget).data('action');

            if (action == 'edit') {
                ModalFactory.create({
                    title: M.util.get_string('editblocksetting', 'local_edwiserreports'),
                    body: Fragment.loadFragment(
                        'local_edwiserreports',
                        'get_blocksetting_form',
                        contextid, {
                            blockname: blockname
                        }
                    )
                }).done(function(modal) {
                    var root = modal.getRoot();
                    modal.modal.addClass('modal-dialog-centered');

                    root.on(ModalEvents.bodyRendered, function() {
                        var form = modal.modal.find('.block-settings-form');
                        modal.modal.find('.save-block-settings').on('click', function(event) {
                            event.preventDefault();
                            var formData = form.serializeArray();
                            var data = {};
                            $(formData).each(function($k, $d) {
                                data[$d.name] = $d.value;
                            });

                            // Set users preferences
                            setBlockPreference(blockname, data);
                        });
                    });

                    root.on(ModalEvents.hidden, function() {
                        modal.destroy();
                    });
                    modal.show();
                });
            } else if (action == "hide") {
                var hidden = $(this).data("hidden");
                var _this = $(this);
                $.ajax({
                    url: v.requestUrl,
                    type: 'GET',
                    data: {
                        action: 'toggle_hide_block_ajax',
                        sesskey: M.cfg.sesskey,
                        data: JSON.stringify({
                            'blockname': blockname,
                            'hidden': hidden
                        })
                    }
                }).done(function(response) {
                    response = JSON.parse(response);
                    if (response.success) {
                        if (hidden) {
                            _this.closest('.edwiserReport-block').removeClass('block-hidden');
                            _this.data("hidden", 0);
                            _this.html(M.util.get_string('hide', 'local_edwiserreports'));
                        } else {
                            _this.closest('.edwiserReport-block').addClass('block-hidden');
                            _this.data("hidden", 1);
                            _this.html(M.util.get_string('unhide', 'local_edwiserreports'));
                        }
                    }
                });
            } else if (action == "editcap") {
                ModalFactory.create({
                    title: M.util.get_string('editblockcapabilities', 'local_edwiserreports'),
                    body: Fragment.loadFragment(
                        'local_edwiserreports',
                        'get_blockscap_form',
                        contextid, {
                            blockname: blockname
                        }
                    )
                }).done(function(modal) {
                    var root = modal.getRoot();
                    modal.modal.addClass('modal-dialog-centered modal-lg');

                    root.on(ModalEvents.bodyRendered, function() {
                        var form = modal.modal.find('.block-cap-form');
                        modal.modal.find('#menucapabilities').on('change', function(event) {
                            event.preventDefault();
                            var formData = form.serializeArray();
                            var data = {};
                            $(formData).each(function($k, $d) {
                                data[$d.name] = $d.value;
                            });

                            Fragment.loadFragment(
                                'local_edwiserreports',
                                'block_overview_display',
                                contextid, {
                                    capvalue: data.capabilities
                                }
                                // eslint-disable-next-line no-unused-vars
                            ).done(function(html, js, css) {
                                Templates.replaceNodeContents(modal.modal.find('.cap-overview'), html, js);
                                switchCapabilitiesBlock(modal);
                            });
                        });

                        switchCapabilitiesBlock(modal);

                        form = modal.modal.find('.block-cap-form');
                        modal.modal.find('.save-block-caps').on('click', function(event) {
                            event.preventDefault();
                            var formData = form.serializeArray();
                            var data = {};
                            $(formData).each(function($k, $d) {
                                data[$d.name] = $d.value;
                            });

                            // Set block capabilities
                            setBlockCapabilities(blockname, data, function() {
                                modal.hide();
                            });
                        });
                    });

                    root.on(ModalEvents.hidden, function() {
                        modal.destroy();
                    });
                    modal.show();
                });
            }
        });
    }

    /**
     * Switch capability block.
     * @param {Object} modal Modal object
     */
    function switchCapabilitiesBlock(modal) {
        modal.modal.find('.comparisontable .switch-capability').on('click', function(event) {
            var permissions = $(event.currentTarget).find('input[type=radio]');
            var current = permissions.filter(':checked');
            var next = permissions.eq(permissions.index(current) + 1);
            if (next.length === 0) {
                next = permissions.eq(0);
            }
            next.prop('checked', true);

            var perStr = next.data('strpermission');
            var perClass = next.data('permissionclass');

            $(event.currentTarget).removeClass('inherit allow prevent prohibit');
            $(event.currentTarget).addClass(perClass);
            $(event.currentTarget).find('label').html(perStr);
        });
    }

    /**
     * Set blocks capabilities
     * @param {string} blockname
     * @param {string} data
     * @param {function} callback
     */
    function setBlockCapabilities(blockname, data, callback) {
        data.blockname = blockname;
        data = JSON.stringify(data);
        var sesskey = $('#' + blockname).data('sesskey');

        // Update users capability
        $.ajax({
            url: v.requestUrl,
            type: 'GET',
            data: {
                action: 'set_block_capability_ajax',
                sesskey: sesskey,
                data: data
            }
        }).done(function(response) {
            response = JSON.parse(response);
            if (response.success) {
                callback();
                location.reload();
            } else {
                Notification.addNotification({
                    message: "Error",
                    type: "error"
                });
            }
        }).fail(function(error) {
            console.log(error);
            Notification.addNotification({
                message: error,
                type: "error"
            });
        }).always(function() {
            callback();
            location.reload();
        });
    }

    /**
     * Set users preferences
     * @param {string} blockname Block name
     * @param {string} data Data for preference
     */
    function setBlockPreference(blockname, data) {
        data.blockname = blockname;
        data = JSON.stringify(data);
        var sesskey = $('#' + blockname).data('sesskey');

        // Set users preferences
        $.ajax({
            url: v.requestUrl,
            type: 'GET',
            data: {
                action: 'set_block_preferences_ajax',
                sesskey: sesskey,
                data: data
            }
        }).fail(function(error) {
            console.log(error);
            Notification.addNotification({
                message: error,
                type: "error"
            });
        }).always(function() {
            location.reload();
        });
    }

    /**
     * Render all emails in modal
     * @param {object} data Data for datatable
     * @param {Object} modal Modal object
     * @return {Object} Datatable object
     */
    function renderAllScheduledEmails(data, modal) {
        var table = modal.getRoot().find("#esr-shceduled-emails");

        // Resize event to adjust datatable when click on the all list tab
        // Not able to call resize when ajax completed
        // So got the temporary solution
        $(document).on('click', '[aria-controls="listemailstab"]', function() {
            $(window).resize();
        });

        // Create datatable
        return table.DataTable({
            ajax: {
                url: v.requestUrl + '?sesskey=' + data.sesskey,
                type: v.requestType,
                data: {
                    action: 'get_scheduled_emails_ajax',
                    data: JSON.stringify({
                        blockname: data.block,
                        region: data.region
                    })
                }
            },
            dom: '<"edwiserreports-table"i<t><"table-pagination"p>>',
            language: {
                info: M.util.get_string('tableinfo', 'local_edwiserreports'),
                infoEmpty: M.util.get_string('infoempty', 'local_edwiserreports'),
                emptyTable: M.util.get_string('noscheduleemails', 'local_edwiserreports'),
                zeroRecords: M.util.get_string('zerorecords', 'local_edwiserreports'),
                sClass: 'text-center',
                paginate: {
                    previous: M.util.get_string('previous', 'moodle'),
                    next: M.util.get_string('next', 'moodle')
                }
            },
            drawCallback: function() {
                stylePaginationButton(this);
            },
            order: [
                [2, "asc"]
            ],
            columns: [{
                    "data": "esrname",
                    "orderable": true
                },
                {
                    "data": "esrnextrun",
                    "orderable": true
                },
                {
                    "data": "esrfrequency",
                    "orderable": true
                },
                {
                    "data": "esrmanage",
                    "orderable": false
                }
            ],
            responsive: true,
            lengthChange: false,
        });
    }

    /**
     * Validate email shceduled form
     * @param  {object} form Form object
     * @param  {object} errorBox Box to show error
     * @return {boolean} Return form validation status
     */
    function validateEmailScheduledForm(form, errorBox) {
        var esrname = form.find('[name="esrname"]').val();
        var esrsubject = form.find('[name="esrsubject"]').val();
        var valid = true;
        if (esrname == "" || esrsubject == "") {
            errorBox.html(emptyerrormsg).show();
            valid = false;
        }
        if (!validateEmails(form.find('[name="esrrecepient"]').val())) {
            valid = false;
            errorBox.html(emailinvaliderrormsg).show();
        }
        return valid;
    }

    /**
     * Save scheduled emails
     * @param {Object} data Data for email
     * @param {object} root Modal root object
     * @param {Object} modal Modal object
     */
    function saveScheduleEmailInit(data, root, modal) {
        // On save perform operation
        root.on('click', '[data-action="save"]', function() {
            var errorBox = root.find(".esr-form-error");
            errorBox.html(loader).show();

            if (validateEmailScheduledForm(root.find("form"), errorBox)) {
                var filter = data.filter;
                var cohortid = data.cohortid;
                var block = data.block;
                var url = M.cfg.wwwroot + "/local/edwiserreports/download.php?type=emailscheduled&filter=" +
                    filter + "&cohortid=" + cohortid + "&block=" + block;

                // Send ajax to save the scheduled email
                $.ajax({
                    url: url,
                    type: "POST",
                    data: root.find("form").serialize()
                }).done(function(response) {
                    response = $.parseJSON(response);

                    // If error then log the error
                    if (response.error) {
                        errorBox.html(schedulerrormsg);
                        console.log(response.error);
                    } else {
                        if (emailListTable) {
                            emailListTable.destroy();
                        }
                        emailListTable = renderAllScheduledEmails(data, modal);
                        errorBox.html(schedulesuccessmsg);
                    }
                }).fail(function(error) {
                    errorBox.html(schedulerrormsg);
                    console.log(error);
                }).always(function() {
                    errorBox.delay(3000).fadeOut('slow');
                });
            }
        });

        // Reset scheduled form
        root.on('click', '[data-action="cancel"]', function() {
            root.find('[name^=esr]:not(.d-none):not([id="esr-toggle-"])').val("")
                .each(function(index, element) {
                    $(element).trigger("input");
                });
            root.find('#esr-id').val(-1);
        });
    }

    /**
     * Update dropdown button text
     * @param  {object} _this click object
     */
    function updateDropdownBtnText(_this) {
        var val = $(_this).data('value');
        var text = $(_this).text();
        var dropdownBtn = $(_this).closest(".dropdown").find(dropdownSelector);

        // Set button values
        dropdownBtn.text(text);
        dropdownBtn.data("value", val);
    }

    /**
     * Duration dropdown init
     * @param {object} _this click object
     * @param {Object} root Modal root obuject
     */
    function durationDropdownInit(_this, root) {
        var val = $(_this).data('value');

        root.find(durationInput).val(val);
        $(timesDropdownBtn).hide();

        // Show only selected dropdown
        var subDropdown = null;
        switch (val) {
            case 1: // Weekly
                subDropdown = $(weeklyDropdownBtn);
                break;
            case 2: // Monthly
                subDropdown = $(monthlyDropdownBtn);
                break;
            default: // Daily
                subDropdown = $(dailyDropdownBtn);
        }

        // Show subdropdown
        subDropdown.show();

        // Set values to hidden input fieds
        var timeval = subDropdown.data("value");
        $(timeInput).val(timeval);
    }

    /**
     * Email schedle setting session initialization
     * @param {object} _this click object
     * @param {Object} root Modal root obuject
     */
    function emailScheduleSettingInit(_this, root) {
        var id = $(_this).data("id");
        var blockname = $(_this).data("blockname");
        var region = $(_this).data("region");

        $.ajax({
            url: v.requestUrl,
            type: v.requestType,
            sesskey: $(_this).data("sesskey"),
            data: {
                action: 'get_scheduled_email_detail_ajax',
                sesskey: $(_this).data("sesskey"),
                data: JSON.stringify({
                    id: id,
                    blockname: blockname,
                    region: region
                })
            }
        }).done(function(response) {
            response = JSON.parse(response);
            if (!response.error) {
                setEmailSheduleFormValues(response, _this, root);

                root.find(tabs).removeClass("active show");
                root.find(formTab).addClass("active show");
            } else {
                console.log(response);
            }
        }).fail(function(error) {
            console.log(error);
        });
    }

    /**
     * Set email shcedule values in form
     * @param {Object} response Response object
     * @param {object} _this click object
     * @param {Object} root Modal root obuject
     */
    function setEmailSheduleFormValues(response, _this, root) {
        var esrDurationVal = null;
        var esrTimeVal = null;

        $.each(response.data, function(idx, val) {
            if (typeof val === 'object') {
                // Set block value name
                root.find("#esr-blockname").val(val.blockname);
                root.find("#esr-region").val(val.region);
            } else if (idx === "esrduration") {
                var esrDuration = '[aria-labelledby="durationcount"] .dropdown-item[data-value="' + val + '"]';
                esrDurationVal = val;
                // Trigger click event
                root.find(esrDuration).click();
            } else if (idx === "esrtime") {
                esrTimeVal = val;
            } else if (idx === "esremailenable") {
                var checkbox = $('input[name="' + idx + '"]');
                if (val) {
                    checkbox.prop("checked", true);
                } else {
                    checkbox.prop("checked", false);
                }
            } else {
                // Set values for input text.
                $('[name="' + idx + '"]').val(val);
                if ($('input[name="' + idx + '"].form-control').length) {
                    $('input[name="' + idx + '"].form-control').trigger('input');
                }
            }

        });

        // Subdropdown click event
        var subSelectedDropdpown = '.dropdown-item[data-value="' + esrTimeVal + '"]';

        // Show only selected dropdown
        var subDropdown = null;
        switch (esrDurationVal) {
            case "1": // Weekly
                subDropdown = $(".weekly-dropdown");
                break;
            case "2": // Monthly
                subDropdown = $(".monthly-dropdown");
                break;
            default: // Daily
                subDropdown = $(".daily-dropdown");
        }

        // Trigger click event
        subDropdown.find(subSelectedDropdpown).click();
    }

    /**
     * Delete Scheduled email
     * @param {Object} data Data for email
     * @param {object} root Modal root object
     * @param {Object} modal Modal object
     */
    function emailScheduleDeleteInit(data, root, modal) {
        var id = data.id;
        var blockname = data.block;
        var region = data.region;
        var errorBox = root.find(".esr-form-error");
        errorBox.html(loader).show();

        $.ajax({
            url: v.requestUrl,
            type: v.requestType,
            sesskey: data.sesskey,
            data: {
                action: 'delete_scheduled_email_ajax',
                sesskey: data.sesskey,
                data: JSON.stringify({
                    id: id,
                    blockname: blockname,
                    region: region
                })
            }
        }).done(function(response) {
            if (!response.error) {
                if (emailListTable) {
                    emailListTable.destroy();
                }
                emailListTable = renderAllScheduledEmails(data, modal);
                errorBox.html(deletesuccessmsg);
            } else {
                errorBox.html(deleteerrormsg);
            }
        }).fail(function(error) {
            errorBox.html(deleteerrormsg);
            console.log(error);
        }).always(function() {
            errorBox.delay(3000).fadeOut('slow');
        });
    }

    /* eslint-disable no-unused-vars */
    /**
     * Change scheduled email status
     * @param {Object} data Data for email
     * @param {object} root Modal root object
     * @param {Object} modal Modal object
     */
    function changeScheduledEmailStatusInit(data, root, modal) {
        /* eslint-enable no-unused-vars */
        var id = data.id;
        var blockname = data.block;
        var region = data.region;
        var sesskey = data.sesskey;

        var errorBox = root.find(".esr-form-error");

        $.ajax({
            url: v.requestUrl,
            type: v.requestType,
            data: {
                action: 'change_scheduled_email_status_ajax',
                sesskey: sesskey,
                data: JSON.stringify({
                    id: id,
                    blockname: blockname,
                    region: region
                })
            }
        }).done(function(response) {
            let switchElement = root.find('.esr-manage-scheduled-emails .esr-switch[data-id="' + data.id + '"]');
            response = JSON.parse(response);
            if (switchElement.attr('data-value') == "0") {
                switchElement.attr('data-value', 'on');
            } else {
                switchElement.attr('data-value', '0');
            }
            if (!response.error) {
                errorBox.html(response.successmsg);
                errorBox.show();
                errorBox.delay(3000).fadeOut('slow');
            } else {
                errorBox.html(response.errormsg);
                errorBox.show();
                errorBox.delay(3000).fadeOut('slow');
            }
        });
    }

    /**
     * Manage schedule emails form initialization
     * @param {Object} data Data for email
     * @param {object} root Modal root object
     * @param {Object} modal Modal object
     */
    function emailScheduleFormInit(data, root, modal) {
        // If dropdown selected then update the button text
        root.on('click', dropdowns, function() {
            updateDropdownBtnText(this);
        });

        // Select duration for email schedule
        root.on('click', durationSelector, function() {
            durationDropdownInit(this, root);
        });

        // Select time for schedule
        root.on('click', timesDropdownLink, function() {
            root.find(timeInput).val($(this).data('value'));
        });

        // When setting button clicked then
        root.on('click', settingBtn, function() {
            emailScheduleSettingInit(this, root);
        });

        // When delete button clicked then
        root.on('click', deleteBtn, function(e) {
            data.id = $(this).data("id");

            str.get_strings([{
                    key: 'confirmemailremovaltitle',
                    component: 'local_edwiserreports'
                },
                {
                    key: 'confirmemailremovalquestion',
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
                Notification.confirm(s[0], s[1], s[2], s[3], $.proxy(function() {
                    emailScheduleDeleteInit(data, root, modal);
                }, e.currentTarget));
            });
        });

        // When toggle switch clicked then
        root.on('click', emailListToggleSwitch, function() {
            data.id = $(this).data("id");
            changeScheduledEmailStatusInit(data, root, modal);
        });

        // Send the notification immidiatly
        root.on('click', '[data-action="send"]', function() {
            sendMailToUser(data, this, root);
        });

        // On save perform operation
        saveScheduleEmailInit(data, root, modal);
    }

    /**
     * Send mail to user
     * @param {Object} data Data for email
     * @param {object} _this anchor tag
     * @param {object} root Modal root object
     */
    function sendMailToUser(data, _this, root) {
        var filter = data.filter;
        var cohortid = data.cohortid;
        var block = data.block;
        var errorBox = root.find(".esr-form-error");
        errorBox.html('').show();
        if ($(document).find('#cover-spin')) {
            $("body").append(windowLoader);
        }
        $(document).find('#cover-spin').show(0);

        $.ajax({
            url: M.cfg.wwwroot + "/local/edwiserreports/download.php?type=email&filter=" +
                filter + "&cohortid=" + cohortid + "&block=" + block,
            type: "POST",
            data: root.find('form').serialize()
        }).done(function(response) {
            response = $.parseJSON(response);
            if (response.error) {
                errorBox.html('<div class="alert alert-danger"><b>ERROR:</b>' + response.errormsg + '</div>');
            } else {
                errorBox.html('<div class="alert alert-success"><b>Success:</b>' + response.errormsg + '</div>');
            }
        }).fail(function(response) {
            errorBox.html('<div class="alert alert-danger"><b>ERROR:</b>' + response.errormsg + '</div>');
        }).always(function() {
            errorBox.delay(3000).fadeOut('slow');
            $(document).find('#cover-spin').hide();
        });
    }

    /**
     * Send plain formatted time.
     * @param {Number} h Hours
     * @param {Number} m Minutes
     * @param {Number} s Seconds
     * @returns {String}
     */
    function timePlainFormat(h, m, s) {
        if (h > 0) {
            h = h < 10 ? "0" + h : h;
        } else {
            h = "00";
        }
        if (m > 0) {
            m = m < 10 ? "0" + m : m;
        } else {
            m = "00";
        }
        if (s > 0) {
            s = s < 10 ? "0" + s : s;
        } else {
            s = "00";
        }
        return h + ":" + m + ":" + s;
    }

    /**
     * Convert seconds to HH:MM:SS
     * @param {Integer} seconds Seconds
     * @param {Object} opts Options
     * @returns {String}
     */
    function timeFormatter(seconds, opts) {
        seconds = Number(seconds);
        var h = Math.floor(seconds / 3600);
        var m = Math.floor(seconds % 3600 / 60);
        var s = Math.floor(seconds % 3600 % 60);

        if (typeof opts == 'object' && opts.dataPointIndex !== undefined && opts.dataPointIndex !== -1) {
            var time = [];
            var short = opts.short !== undefined && opts.short;
            if (h > 0) {
                if (short) {
                    time.push(h + " " + "h.");
                } else {
                    time.push(h + " " + (h == 1 ? M.util.get_string('hour', 'local_edwiserreports') :
                        M.util.get_string('hours', 'local_edwiserreports')));
                }
            }
            if (m > 0) {
                if (short) {
                    time.push(m + " " + "min.");
                } else {
                    time.push(m + " " + (m == 1 ? M.util.get_string('minute', 'local_edwiserreports') :
                        M.util.get_string('minutes', 'local_edwiserreports')));
                }
            }
            if (s > 0) {
                if (short) {
                    if (time.length === 0) {
                        time.push(s + " " + "sec.");
                    }
                } else {
                    time.push(s + " " + (s == 1 ? M.util.get_string('second', 'local_edwiserreports') :
                        M.util.get_string('seconds', 'local_edwiserreports')));
                }
            }
            if (time.length == 0) {
                time.push(0);
            }
            return time.join(', ');
        }
        return timePlainFormat(h, m, s);
    }

    /**
     * Render insight card.
     * @param {String} selector DOM selector
     * @param {Object} data     Insight data
     */
    function insight(selector, data) {
        data.insight.title = M.util.get_string(data.insight.title, 'local_edwiserreports');
        if (data.details !== undefined && data.details.data !== undefined) {
            data.details.data.forEach(function(value, index) {
                data.details.data[index].title = M.util.get_string(value.title, 'local_edwiserreports');
            });
        }
        Templates.render('local_edwiserreports/insight-placeholder', {})
            .done(function(html, js) {
                Templates.replaceNodeContents(selector, html, js);
                Templates.render('local_edwiserreports/insight', data)
                    .done(function(html, js) {
                        Templates.replaceNodeContents(selector, html, js);
                    })
                    .fail(function(ex) {
                        Notification.exception(ex);
                        $(selector).remove();
                    });
            });
    }

    /**
     * Styling pagination button of data table.
     * @param {DOM} element Table element
     */
    function stylePaginationButton(element) {
        let pagination = $(element).closest(SELECTOR.TABLE).find(SELECTOR.PAGINATION);
        if (pagination.find(SELECTOR.PAGINATIONITEM).length < 4) {
            pagination.addClass('d-none');
            return;
        }
        pagination.removeClass('d-none');
        pagination.find(SELECTOR.PAGINATIONITEM).addClass(v.datatableClasses.buttonSpacing);
        pagination.find(SELECTOR.PAGINATIONITEM + ' a').addClass(v.datatableClasses.buttonSize);
        pagination.find(SELECTOR.PAGINATIONITEM + '.active a').addClass(v.datatableClasses.buttonActive);
        pagination.find(SELECTOR.PAGINATIONITEM + ':not(.active) a').addClass(v.datatableClasses.buttonInactive);
        pagination.find(SELECTOR.PAGINATIONITEM + '.previous a').addClass(v.datatableClasses.prevNextSpacing);
        pagination.find(SELECTOR.PAGINATIONITEM + '.next a').addClass(v.datatableClasses.prevNextSpacing);
        // Different margin.
        pagination.find(SELECTOR.PAGINATIONITEM + '.previous').removeClass(v.datatableClasses.buttonsSpacing)
            .addClass('mx-4');
        pagination.find(SELECTOR.PAGINATIONITEM + '.next').removeClass(v.datatableClasses.buttonsSpacing)
            .addClass('mx-4');

    }

    /**
     * This function calls callback function when user change the date.
     *
     * @param {function} callback Callback function to handle date change
     */
    function dateChange(callback) {
        $(document).on('edwiserreport:datechange', function(event) {
            callback(event.detail.date);
        });
    }

    /**
     * Handling highlight of search input.
     */
    function handleSearchInput() {
        /* Table search listener */
        $('body').on('input', SELECTOR.SEARCHTABLE, function() {
            $(this).toggleClass('empty', $(this).val() === '');
        });
    }
    /**
     * Apply precision to number. If number is whole then return as it is.
     * @param {Number} value     Value to apply precision
     * @param {Number} precision Precision to apply
     * @returns {Number}
     */
    function toPrecision(value, precision) {
        if (value % 1 === 0) {
            return value;
        }
        return value.toPrecision(precision);
    }

    return {
        loader: loader,
        toPrecision: toPrecision,
        insight: insight,
        timeFormatter: timeFormatter,
        dateChange: dateChange,
        stylePaginationButton: stylePaginationButton,
        handleSearchInput: handleSearchInput
    };
});
