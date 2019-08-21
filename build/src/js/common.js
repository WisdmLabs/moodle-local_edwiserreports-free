define([
    'jquery',
    'core/notification',
    'report_elucidsitereport/variables',
    'report_elucidsitereport/select2',
    'report_elucidsitereport/jquery.dataTables',
    'report_elucidsitereport/dataTables.bootstrap4'
    ], function($, notif, v) {
    var toggleMenuAndPin = "#toggleMenubar [data-toggle='menubar'], .page-aside-pin";

    $(document).ready(function() {
        var isNavlink = null;
        var pageContent = $("#page-admin-report-elucidsitereport-index .page-content");
        var pageWidth = pageContent.width();
        var exportDropdown = '.export-dropdown a[data-action="email"]';

        rearrangeBlocks(pageWidth, isNavlink);

        $(document).on("click", toggleMenuAndPin, function() {
            isNavlink = $(this).hasClass("nav-link");
            pageWidth = pageContent.width();

            rearrangeBlocks(pageWidth, isNavlink);
        });

        $(document).on("click", exportDropdown, function(e) {
            e.preventDefault();
            $.ajax({
                url: this.href
            }).done(function() {
                notif.addNotification({
                    message: "Email has been sent to your mail account",
                    type: "info"
                });
            }).fail(function() {
                notif.addNotification({
                    message: "Failed to send the report in your mail account",
                    type: "error"
                });
            })
        });
    });

    function rearrangeBlocks(pageWidth, isNavlink) {
        var blocks ="#wdm-elucidsitereport > div";

        if (isNavlink) {
            var menubarFolded = $("body").hasClass("site-menubar-fold");
            if (menubarFolded && pageWidth < 1080) {
                $(blocks).addClass("col-lg-12");
            } else {
                $(blocks).removeClass("col-lg-12");
            }
        } else {
            if (pageWidth < 820 ) {
                $(blocks).addClass("col-lg-12");
            } else {
                $(blocks).removeClass("col-lg-12");
            }
        }

        $(document).find('.table.dataTable').DataTable().draw();
    }
});