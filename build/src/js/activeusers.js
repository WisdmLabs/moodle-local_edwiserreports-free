define([
    'jquery',
    'core/modal_factory',
    'core/modal_events',
    'core/fragment',
    'core/templates',
    'report_elucidsitereport/variables',
    'report_elucidsitereport/jquery.dataTables',
    'report_elucidsitereport/dataTables.bootstrap4'
], function($, ModalFactory, ModalEvents, Fragment, Templates, V) {
    function init(CONTEXTID) {
        $(document).ready(function() {
            var PageId = "#wdm-activeusers-individual";
            var ActiveUsersTable = PageId + " .table";
            var loader = PageId + " .loader";
            var ModalTrigger = ActiveUsersTable + " a";

            $.ajax({
                url: V.requestUrl,
                data: {
                    action: 'get_activeusers_graph_data_ajax',
                    data: JSON.stringify({
                        filter : "all"
                    })
                },
            }).done(function(response) {
                var ActiveUsers = [];

                $.each(response.labels, function(idx, val) {
                    ActiveUsers[idx] = {
                        date : val,
                        filter : parseInt((new Date(val).getTime() / 1000)),
                        activeusers : response.data.activeUsers[idx],
                        courseenrolment : response.data.enrolments[idx],
                        coursecompletion : response.data.completionRate[idx]
                    };
                });

                var context = {
                    activeusers : ActiveUsers
                }

                Templates.render('report_elucidsitereport/activeusers', context)
                .then(function(html, js) {
                    Templates.replaceNode(PageId, html, js);
                }).fail(function(ex) {
                    console.log(ex);
                }).always(function() {
                    $(ActiveUsersTable).DataTable({
                        order : [[0, 'desc']],
                        dom: '<"pull-left"f><t><lip><"clear-fix">',
                        columnDefs: [
                            {
                                "targets": 0
                            },
                            {
                                "targets": 1,
                                "className": "text-center",
                            },
                            {
                                "targets": 2,
                                "className": "text-center",
                            },
                            {
                                "targets": 3,
                                "className": "text-center",
                            }
                        ],
                    });
                    $(ActiveUsersTable).removeClass("d-none");
                    $(loader).remove();
                });
            }).fail(function(error) {
                console.log(error);
            });

            // $('#wdm-activeusers-individual .table').DataTable();
            $(document).on('click', ModalTrigger, function() {
                var title;
                var action = $(this).data("action");
                var filter = $(this).data("filter");
                var ModalRoot = null;

                if (action == "activeusers") {
                    title = "Active Users in ";
                } else if (action == "enrolments") {
                    title = "Enroled Users in ";
                } else if (action == "completions") {
                    title = "Completed Users in ";
                }

                var titleDate = new Date(eval(filter*1000));
                title += titleDate.toLocaleString().split(',')[0];

                ModalFactory.create({
                    body: Fragment.loadFragment(
                        'report_elucidsitereport',
                        'userslist',
                        CONTEXTID,
                        {
                            page : 'activeusers',
                            filter : filter,
                            action : action
                        }
                    )
                }).then(function(modal) {
                    ModalRoot = modal.getRoot();
                    modal.setTitle(title);
                    modal.show();
                    ModalRoot.on(ModalEvents.hidden, function () {
                        modal.destroy();
                    });
                });
            });
        });
    }

    return {
        init : init
    };
	
});