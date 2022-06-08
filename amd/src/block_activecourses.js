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
 * Active Courses / Popular Courses block.
 *
 * @copyright   2022 Wisdmlabs <support@wisdmlabs.com>
 * @author      Yogesh Shirsath
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/* eslint-disable no-unused-vars */
/* eslint-disable no-console */
define([
    'jquery',
    './defaultconfig',
    './common',
], function(
    $,
    CFG,
    common
) {

    /**
     * Selectors.
     */
    var SELECTOR = {
        PANEL: '#activecoursesblock',
        TABLE: '#activecoursesblock table',
        SEARCH: '.table-search-input input',
        USERS: '#activecoursesblock table a.modal-trigger'
    };

    /**
     * Data table object.
     */
    var dataTable = null;

    /**
     * Promises list.
     */
    let PROMISE = {
        /**
         * Get active courses.
         * @returns {PROMISE}
         */
        GET_ACTIVECOURSES: function() {
            return $.ajax({
                url: CFG.requestUrl,
                type: CFG.requestType,
                dataType: CFG.requestDataType,
                data: {
                    action: 'get_activecourses_data_ajax',
                    secret: M.local_edwiserreports.secret,
                    lang: $('html').attr('lang')
                },
            });
        }
    };

    /**
     * Load data to dataTable using ajax.
     */
    function loadData() {
        // Show loader.
        common.loader.show(SELECTOR.PANEL);

        PROMISE.GET_ACTIVECOURSES().done(function(response) {
                if (dataTable !== null) {
                    dataTable.destroy();
                }
                dataTable = $(SELECTOR.TABLE).DataTable({
                    responsive: true,
                    data: response.data,
                    dom: '<"edwiserreports-table"<t><"table-pagination"p>>',
                    aaSorting: [
                        [2, 'desc']
                    ],
                    aoColumns: [null, null, {
                        "orderSequence": ["desc"]
                    }, {
                        "orderSequence": ["desc"]
                    }, {
                        "orderSequence": ["desc"]
                    }],
                    language: {
                        info: M.util.get_string('tableinfo', 'local_edwiserreports'),
                        infoEmpty: M.util.get_string('infoempty', 'local_edwiserreports'),
                        emptyTable: M.util.get_string('nocourses', 'local_edwiserreports'),
                        zeroRecords: M.util.get_string('zerorecords', 'local_edwiserreports'),
                        paginate: {
                            previous: M.util.get_string('previous', 'moodle'),
                            next: M.util.get_string('next', 'moodle')
                        }
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
                /* Added fixed column rank in datatable */
                dataTable.on('order.dt', function() {
                    dataTable.column(0, {
                        order: 'applied'
                    }).nodes().each(function(cell, i) {
                        let img = '';
                        if (i >= 0 && i <= 2) {
                            img = "<img class='ml-1' src='" + M.util.image_url(
                                'trophy/' + ['gold', 'silver', 'bronze'][i],
                                'local_edwiserreports'
                            ) + "'></img>";
                        }
                        cell.innerHTML = (i + 1) + img;
                    });
                    $(SELECTOR.TABLE + " td:not(.bg-secondary)").addClass("bg-white");
                }).draw();
            })
            .fail(function(data) {
                console.log(data);
            })
            .always(function() {
                // Hide loader.
                common.loader.hide(SELECTOR.PANEL);
            });
    }

    /**
     * Initialize
     * @param {function} invalidUser Callback function
     */
    function init(invalidUser) {

        // Block not present on page.
        if ($(SELECTOR.PANEL).length === 0) {
            return;
        }

        // Enable select2 on cohort filter.
        $(SELECTOR.PANEL).find('.singleselect').select2();

        loadData();

        // Search in table.
        $('body').on('input', `${SELECTOR.PANEL} ${SELECTOR.SEARCH}`, function() {
            dataTable.columns(1).search($(this).val()).draw();
        });
    }

    // Must return the init function
    return {
        init: init
    };
});
