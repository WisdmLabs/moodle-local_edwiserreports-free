define([
    'jquery',
    'core/templates',
    'core/fragment',
    'report_elucidsitereport/variables',
    'report_elucidsitereport/jquery.dataTables',
    'report_elucidsitereport/dataTables.bootstrap4'
], function($, Templates, Fragment, V) {
    function init(CONTEXTID) {
        var PageId = "#wdm-lpstats-individual";
        var LpSelect = PageId + " #wdm-lp-select";
        var LpTable = PageId + " .table";
        var loader = PageId + " .loader";
        var Table = null;

        $(document).ready(function() {
            var lpid = $(LpSelect).val();
            addLpStatsTable(lpid);

            $(LpSelect).on("change", function() {
                if (Table) {
                    Table.destroy();
                }

                $(LpTable).hide();
                $(loader).show();

                lpid = $(LpSelect).val();
                addLpStatsTable(lpid);
            })
        });

        function addLpStatsTable(lpid) {
            var fragment = Fragment.loadFragment(
                'report_elucidsitereport',
                'lpstats',
                CONTEXTID,
                {
                    lpid : lpid
                }
            );

            fragment.done(function(response) {
                var context = JSON.parse(response);
                Templates.render('report_elucidsitereport/lpstatsinfo', context)
                .then(function(html, js) {
                    Templates.replaceNode(LpTable, html, js);
                }).fail(function(ex) {
                    console.log(ex);
                }).always(function() {
                    $(LpTable).show();
                    Table = $(LpTable).DataTable({
                        responsive : true
                    });
                    $(loader).hide();
                });
            });
        }
    }

    return {
        init : init
    };
	
});