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
 *//* eslint-disable no-console */
define([
    'jquery',
    'core/chartjs',
    'local_edwiserreports/defaultconfig',
    './common',
    'local_edwiserreports/jquery.dataTables',
    'local_edwiserreports/dataTables.bootstrap4'
], function($, Chart, cfg, common) {
    /**
     * Initialize
     * @param {function} notifyListner Callback function
     */
    function init(notifyListner) {
        var activeCourseTable;

        var panel = cfg.getPanel("#activecoursesblock");
        var panelBody = cfg.getPanel("#activecoursesblock", "body");
        var loader = panelBody + " .loader";
        var table = panelBody + " .table";

        if ($(panel).length) {
            // Show loader.
            common.loader.show('#activecoursesblock');

            /* Ajax request to get data for active courses table */
            $.ajax({
                url: cfg.requestUrl,
                type: cfg.requestType,
                dataType: cfg.requestDataType,
                data: {
                    action: 'get_activecourses_data_ajax',
                    sesskey: $(panel).data("sesskey")
                },
            }).done(function(response) {
                /* Create active course table */
                createActiveCourseTable(response.data);
            })
            .fail(function(error) {
                console.log(error);
            })
            .always(function() {
                /* Added fixed column rank in datatable */
                activeCourseTable.on('order.dt search.dt', function() {
                    activeCourseTable.column(0, {search: 'applied', order: 'applied'}).nodes().each(function(cell, i) {
                        if (i == 0) {
                            cell.innerHTML = "<i class='fa fa-trophy text-gold'></i>";
                        } else if (i == 1) {
                            cell.innerHTML = "<i class='fa fa-trophy text-silver'></i>";
                        } else if (i == 2) {
                            cell.innerHTML = "<i class='fa fa-trophy text-bronze'></i>";
                        } else {
                            cell.innerHTML = i + 1;
                        }
                    });
                    $(table + " td:not(.bg-secondary)").addClass("bg-white");
                }).draw();

                /* Notify that this event is completed */
                notifyListner("activeCourses");

                // Hide loader.
                common.loader.hide('#activecoursesblock');
            });
        } else {
            notifyListner("activeCourses");
        }

        /**
         * Create active course table.
         * @param {object} data Table data object
         */
        function createActiveCourseTable(data) {
            /* If datable already created the destroy the table*/
            if (activeCourseTable) {
                activeCourseTable.destroy();
            }

            /* Create datatable for active courses */
            activeCourseTable = $(table).DataTable({
                responsive: true,
                data: data,
                aaSorting: [[2, 'desc']],
                aoColumns: [
                    null,
                    null,
                    {"orderSequence": ["desc"]},
                    {"orderSequence": ["desc"]},
                    {"orderSequence": ["desc"]}
                ],
                language: {
                    searchPlaceholder: "Search Course"
                },
                initComplete: function() {
                    /* Remove laoder and display table after table is created */
                    $(loader).hide();
                    $(table).fadeIn("slow");
                },
                drawCallback: function() {
                    $('.dataTables_paginate > .pagination').addClass('pagination-sm pull-right');
                    $('.dataTables_filter').addClass('pagination-sm pull-right');
                },
                columnDefs: [
                    {
                        "targets": 0,
                        "className": "text-center",
                        "orderable": false
                    },
                    {
                        "targets": 1,
                        "className": "text-left",
                        "orderable": false
                    },
                    {
                        "targets": "_all",
                        "className": "text-center",
                    }
                ],
                lengthChange: false,
                bInfo: false
            });
        }
    }

    // Must return the init function
    return {
        init: init
    };
});
