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
    './defaultconfig',
    './variables',
    './common',
    './jquery.dataTables',
    './dataTables.bootstrap4'
], function(
    $,
    cfg,
    V,
    common
) {

    /**
     * Initialize
     * @param {function} invalidUser Callback function
     */
    function init(invalidUser) {
        var activeCourseTable;

        var panel = cfg.getPanel("#activecoursesblock");
        var panelBody = cfg.getPanel("#activecoursesblock", "body");
        var loader = panelBody + " .loader";
        var table = panelBody + " .table";
        var searchTable = panel + ' .table-search-input input';

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
                        secret: M.local_edwiserreports.secret
                    },
                }).done(function(response) {
                    if (response.error === true && response.exception.errorcode === 'invalidsecretkey') {
                        invalidUser('activecoursesblock', response);
                        return;
                    }
                    /* Create active course table */
                    createActiveCourseTable(response.data);
                    /* Added fixed column rank in datatable */
                    activeCourseTable.on('order.dt', function() {
                        activeCourseTable.column(0, { order: 'applied' }).nodes().each(function(cell, i) {
                            let img = '';
                            if (i >= 0 && i <= 2) {
                                img = "<img class='ml-1' src='" + M.util.image_url('trophy/' + ['gold', 'silver', 'bronze'][i], 'local_edwiserreports') + "'></img>";
                            }
                            cell.innerHTML = (i + 1) + img;
                        });
                        $(table + " td:not(.bg-secondary)").addClass("bg-white");
                    }).draw();

                    // Search in table.
                    $('body').on('input', searchTable, function() {
                        activeCourseTable.column(1).search(this.value).draw();
                    });
                })
                .fail(function(error) {
                    // console.log(error);
                })
                .always(function() {
                    // Hide loader.
                    common.loader.hide('#activecoursesblock');
                });
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
                dom: '<"edwiserreports-table"<t><"table-pagination"p>>',
                aaSorting: [
                    [2, 'desc']
                ],
                aoColumns: [
                    null,
                    null,
                    { "orderSequence": ["desc"] },
                    { "orderSequence": ["desc"] },
                    { "orderSequence": ["desc"] }
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
                    common.stylePaginationButton(this);
                },
                columnDefs: [{
                        "targets": 0,
                        "className": "text-left pl-5",
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
