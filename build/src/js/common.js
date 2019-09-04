define([
    'jquery',
    'core/notification',
    'core/fragment',
    'core/modal_factory',
    'core/modal_events', 
    'report_elucidsitereport/variables',
    'report_elucidsitereport/select2',
    'report_elucidsitereport/jquery.dataTables',
    'report_elucidsitereport/dataTables.bootstrap4'
    ], function($, notif, fragment, ModalFactory, ModalEvents, v) {
    var toggleMenuAndPin = "#toggleMenubar [data-toggle='menubar'], .page-aside-pin";

    $(document).ready(function() {
        var isNavlink = null;
        var pageContent = $("#page-admin-report-elucidsitereport-index .page-content");
        var pageWidth = pageContent.width();
        var exportEmailDropdown = '.export-dropdown a[data-action="email"]';

        rearrangeBlocks(pageWidth, isNavlink);

        // Resize block according to the block
        $(window).on('resize', function() {
            var pageWidth = v.pluginPage.width();
            rearrangeBlocks(pageWidth);
        });

        /*$(document).on("click", toggleMenuAndPin, function() {
            isNavlink = $(this).hasClass("nav-link");
            pageWidth = pageContent.width();

            rearrangeBlocks(pageWidth, isNavlink);
        });*/

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
    });

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