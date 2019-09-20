define([
    'jquery',
    'core/notification',
    'core/fragment',
    'core/modal_factory',
    'core/modal_events', 
    'core/templates',
    'report_elucidsitereport/variables',
    'report_elucidsitereport/select2',
    'report_elucidsitereport/jquery.dataTables',
    'report_elucidsitereport/dataTables.bootstrap4'
    ], function($, notif, fragment, ModalFactory, ModalEvents, Templates, v) {
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
    var durationBtn = formRoot + ' button#durationcount';
    var durationInput = formRoot + ' input#esr-sendduration';

    // Times dropdown selector
    var timesDropdown = formRoot + ' .dropdown:not(.duration-dropdown)';
    var timesDropdownBtn = timesDropdown + ' button.dropdown-toggle';
    var timesDropdownLink = timesDropdown + ' a.dropdown-item';
    var dailyDropdownBtn = formRoot + ' .dropdown.daily-dropdown button.dropdown-toggle';
    var weeklyDropdownBtn = formRoot + ' .dropdown.weekly-dropdown button.dropdown-toggle';
    var monthlyDropdownBtn = formRoot + ' .dropdown.monthly-dropdown button.dropdown-toggle';
    
    var daySelector = formRoot + ' .dropdown.weeks-dropdown a.dropdown-item';
    var dayBtn = formRoot + ' button#weeksdropdown';
    var timeInput = formRoot + ' input#esr-sendtime';

    // For email schedule setting
    var settingBtn = "#listemailstab .esr-email-sched-setting";

    // Messaged for email schedule
    var loader = '<div class="w-full text-center"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i></div>'
    var errormsg = '<div class="alert alert-danger"><b>ERROR:</b> Error while scheduling email<div>';
    var successmsg = '<div class="alert alert-success"><b>Success:</b> Email scheduled successfully<div>';

    var tabs = '[data-plugin="tabs"] .nav-link, [data-plugin="tabs"] .tab-pane';
    var formTab = '[aria-controls="scheduletab"], #scheduletab';
    /**
     * Email Shcedule Modal Related Psrametres end
     */

    $(document).ready(function() {
        var isNavlink = null;
        var pageContent = $("#page-admin-report-elucidsitereport-index .page-content");
        var pageWidth = pageContent.width();
        var exportEmailDropdown = '.export-dropdown a[data-action="email"]';
        var scheduledEmailDropdown = '.export-dropdown a[data-action="emailscheduled"]';
        var durationDropdown = '#scheduletab .dropdown.duration-dropdown a.dropdown-item';
        var weeksDropdown = '#scheduletab .dropdown.weeks-dropdown a.dropdown-item';


        rearrangeBlocks(pageWidth, isNavlink);

        // Resize block according to the block
        $(window).on('resize', function() {
            var pageWidth = v.pluginPage.width();
            rearrangeBlocks(pageWidth);
        });

        // Send email the report
        $(document).on("click", exportEmailDropdown, function(e) {
            e.preventDefault();
            var _this = this;
            ModalFactory.create({
                type: ModalFactory.types.SAVE_CANCEL,
                title: 'Email Dialog Box',
                body: fragment.loadFragment(
                    'report_elucidsitereport',
                    'email_dialog',
                    $(_this).data("contextid"),
                    {
                        blockname : $(_this).data("blockname")
                    }
                ),
            }, $(this))
            .done(function(modal) {
                var root = modal.getRoot();
                root.on(ModalEvents.hidden, function() {
                    modal.destroy();
                });
                root.on(ModalEvents.save, function() {
                    sendMailToUser(_this, root);
                });
                modal.setSaveButtonText('Send');
                modal.show();
            });
        });

        /**
         * Schedule emails to send reports                                    e.preventDefault();            var _this [description]
         */
        $(document).on("click", scheduledEmailDropdown, function(e) {
            e.preventDefault();
            var _this = this;
            var context = {};

            ModalFactory.create({
                title: 'Schedule Emails',
                body: Templates.render('report_elucidsitereport/email_schedule_tabs', context)
            }, $(this))
            .done(function(modal) {
                var root = modal.getRoot();
                modal.modal.addClass("modal-lg");

                root.on(ModalEvents.bodyRendered, function() {
                    root.find("#esr-blockname").val($( _this).data("blockname"));
                    root.find("#esr-region").val($(_this).data("region"));
                    emailListTable = render_all_scheduled_emails(_this, modal);
                });

                root.on(ModalEvents.hidden, function() {
                    modal.destroy();
                });

                email_schedule_form_init(_this, root, modal);
                modal.show();
            });
        });
    });

    /**
     * Render all emails in modal
     * @param  {object} _this Anchor tag object
     * @param  {Object} modal Modal object
     */
    function render_all_scheduled_emails(_this, modal) {
        return modal.getRoot().find("#esr-shceduled-emails").DataTable({
            ajax : {
                url: v.requestUrl,
                type: v.requestType,
                data: {
                    action: 'get_scheduled_emails_ajax',
                    sesskey: $(_this).data("sesskey"),
                    data : JSON.stringify({
                        blockname : $(_this).attr("data-blockname"),
                        href : $(_this).attr("href"),
                        region : $(_this).attr("data-region")
                    })
                } 
            },
            scrollY : "300px",
            scrollCollapse : true,
            oLanguage : {
                sEmptyTable : "There is no scheduled emails"
            },
            columns : [
                { "data": "esrtoggle" },
                { "data": "esrname" }, 
                { "data": "esrcomponent" },
                { "data": "esrnextrun" },
                { "data": "esrfrequency" },
                { "data": "esrmanage" }
            ],
            bInfo : false,
            lengthChange : false,
            paging :   false
        });
    }

    /**
     * Save scheduled emails
     * @param  {object} root Modal root object
     */
    function save_schedule_email(_this, root, modal) {
        // On save perform operation
        root.on('click', '[data-action="save"]', function() {
            var errorBox = root.find(".esr-form-error");
            errorBox.html(loader).show();

            var filter = v.getUrlParams(_this.href, "filter");
            // Send ajax to save the scheduled email
            $.ajax({
                url: M.cfg.wwwroot + "/report/elucidsitereport/download.php?format=emailscheduled&filter=" + filter,
                type: "POST",
                data: root.find("form").serialize()
            }).done(function(response) {
                response = $.parseJSON(response);

                // If error then log the error
                if (response.error) {
                    errorBox.html(errormsg);
                    console.log(response.error);
                } else {
                    if (emailListTable) {
                        emailListTable.destroy();
                    }
                    emailListTable = render_all_scheduled_emails(_this, modal);
                    errorBox.html(successmsg);
                }
            }).fail(function(error) {
                errorBox.html(errormsg);
                console.log(error);
            }).always(function() {
                errorBox.delay(3000).fadeOut('slow');
            });
        });
    }

    /**
     * Update dropdown button text
     * @param  {object} _this click object
     */
    function update_dropdown_btn_text(_this) {
        var val = $(_this).data('value');
        var text = $(_this).text();
        var dropdownBtn = $(_this).closest(".dropdown").find(dropdownSelector);
        
        // Set button values
        dropdownBtn.text(text);
        dropdownBtn.data("value", val);
    }

    /**
     * Duration dropdown init
     * @param  {object} _this click object
     */
    function duration_dropdown_init(_this, root) {
        var val = $(_this).data('value');
        var text = $(_this).text();

        root.find(durationInput).val(val);
        $(timesDropdownBtn).hide();

        // Show only selected dropdown
        var subDropdown = null;
        switch(val) {
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
     * @param  {[type]} _this [description]
     * @return {[type]}       [description]
     */
    function email_schedule_setting_init(_this, root) {
        var id = $(_this).data("id");
        var blockname = $(_this).data("blockname");
        var region = $(_this).data("region");

        $.ajax({
            url: v.requestUrl,
            type: v.requestType,
            sesskey : $(_this).data("sesskey"),
            data: {
                action: 'get_scheduled_email_detail_ajax',
                sesskey: $(_this).data("sesskey"),
                data : JSON.stringify({
                    id : $(_this).data("id"),
                    blockname : $(_this).data("blockname"),
                    region : $(_this).data("region")
                })
            }
        }).done(function(response) {
            if (!response.error) {
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
                    } else {
                        // Set values for input text
                        $('[name="' + idx + '"]').val(val);
                    }

                });

                // Subdropdown click event
                var subSelectedDropdpown = '.dropdown-item[data-value="' + esrTimeVal + '"]';
                
                // Show only selected dropdown
                var subDropdown = null;
                switch(esrDurationVal) {
                    case "1": // Weekly
                        console.log("test");
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

                root.find(tabs).removeClass("active show");
                root.find(formTab).addClass("active show");
            } else {
                console.log(response);
            }
            console.log(response);
        }).fail(function(error) {
            console.log(error);
        });
    }

    /**
     * Manage schedule emails form initialization
     * @param  {object} root Modal root object
     */
    function email_schedule_form_init(_this, root, modal) {
        // If dropdown selected then update the button text
        root.on('click', dropdowns, function() {
            update_dropdown_btn_text(this);
        });

        // Select duration for email schedule
        root.on('click', durationSelector, function() {
            duration_dropdown_init(this, root);
        });

        // Select time for schedule
        root.on('click', timesDropdownLink, function() {
            root.find(timeInput).val($(this).data('value'));
        });

        // When setting button clicked then
        root.on('click', settingBtn, function() {
            email_schedule_setting_init(this, root);
        });

        // On save perform operation
        save_schedule_email(_this, root, modal);
    }

    /**
     * Send mail to user
     * @param  {object} _this anchor tag
     * @param  {object} root Modal root object
     */
    function sendMailToUser(_this, root) {
        $.ajax({
            url: _this.href,
            type: "POST",
            data: root.find('form').serialize()
        }).done(function(response) {
            response = $.parseJSON(response);
            if (response.error) {
                notif.addNotification({
                    message: response.errormsg,
                    type: "error"
                });
            } else {
                notif.addNotification({
                    message: "Email has been sent",
                    type: "info"
                });
            }
        }).fail(function() {
            notif.addNotification({
                message: "Failed to send the email",
                type: "error"
            });
        });
    }

    // Rearrange Block according to the width of the page
    function rearrangeBlocks(pageWidth) {
        var blocks ="#wdm-elucidsitereport > div";

        if (pageWidth < 780 ) {
            $(blocks).addClass("col-lg-12");
        } else {
            $(blocks).removeClass("col-lg-12");
        }

        $(document).find('.table.dataTable').DataTable().draw();
    }

    /**
     * Generate Email dialog to send emails 
     * @param  {string} url Url to send emails
     * @param  {int} CONTEXTID Context Id
     */
    function emailDialogBox(url, CONTEXTID) {
        fragment.loadFragment('report_elucidsitereport', 'email_dialog', CONTEXTID)
        .done(function(html, js) {
            ModalFactory.create({            
                type: ModalFactory.types.SAVE_CANCEL,
                title: 'Email Dialog Box',
                body: html,
            }, $(this))
            .done(function(modal) {
                // Do what you want with your new modal.
            });
        })
        .fail(function(e) {
            console.log(e)
        });
    }
});