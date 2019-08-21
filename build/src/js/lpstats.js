define([
    'jquery',
    'core/templates',
    'core/fragment',
    'report_elucidsitereport/variables',
    'report_elucidsitereport/select2',
    'report_elucidsitereport/jquery.dataTables',
    'report_elucidsitereport/dataTables.bootstrap4',
    'report_elucidsitereport/common'
], function($, Templates, Fragment, V) {
    function init(CONTEXTID) {
        var PageId = "#wdm-lpstats-individual";
        var LpSelect = "#wdm-lp-select";
        var LpTable = PageId + " .table";
        var loader = PageId + " .loader";
        var filterSection = $("#wdm-userfilter .row .col-6:first-child");
        var LpDropdown = $(PageId).find("#wdm-lp-dropdown");
        var Table = null;

        // Varibales for cohort filter
        var cohortId = 0;
        var cohortFilterBtn   = "#cohortfilter";
        var cohortFilterItem  = cohortFilterBtn + " ~ .dropdown-menu .dropdown-item";

        $(document).ready(function() {
            filterSection.html(LpDropdown.html());
            $(document).find(LpSelect).select2();
            $(document).find(LpSelect).show();
            LpDropdown.remove();

            var lpid = $(document).find(LpSelect).val();
            addLpStatsTable(lpid, cohortId);

            /* Select cohort filter for active users block */
            $(cohortFilterItem).on('click', function() {
                cohortId = $(this).data('cohortid');
                $(cohortFilterBtn).html($(this).text());
                addLpStatsTable(lpid, cohortId);
            });

            $(document).find(LpSelect).on("change", function() {
                $(LpTable).hide();
                $(loader).show();

                lpid = $(document).find(LpSelect).val();
                addLpStatsTable(lpid, cohortId);
            })
        });

        function addLpStatsTable(lpid, cohortId) {
            if (Table) {
                Table.destroy();
                $(LpTable).hide();
                $(loader).show();
            }

            var fragment = Fragment.loadFragment(
                'report_elucidsitereport',
                'lpstats',
                CONTEXTID,
                {
                    lpid : lpid,
                    cohortid : cohortId
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
                        dom : "<'pull-left'f><t><p>",
                        oLanguage : {
                            sEmptyTable : "No Learning Program are available"
                        },
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