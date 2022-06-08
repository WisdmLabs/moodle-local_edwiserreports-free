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
    "jquery",
    'core/notification',
    "./defaultconfig",
    "./common",
    "./jquery.dataTables",
    "./dataTables.bootstrap4"
], function($, Notification, cfg, common) {
    var liveUsersTable = null;
    var panel = cfg.getPanel("#liveusersblock");
    var panelBody = cfg.getPanel("#liveusersblock", "body");
    var loader = $(panelBody + " .loader");
    var table = $(panelBody + " .table");
    var searchTable = panel + " .table-search-input input";

    /**
     * Initialize
     * @param {function} invalidUser Callback function
     */
    function init(invalidUser) {
        if ($(panel).length) {
            // Show loader.
            common.loader.show("#liveusersblock");

            $.ajax({
                    url: cfg.requestUrl,
                    type: cfg.requestType,
                    dataType: cfg.requestDataType,
                    data: {
                        action: 'get_liveusers_data_ajax',
                        secret: M.local_edwiserreports.secret,
                        lang: $('html').attr('lang')
                    },
                }).done(function(response) {
                    if (response.error === true && response.exception.errorcode === 'invalidsecretkey') {
                        invalidUser('liveusersblock', response);
                        return;
                    }
                    setTimeout(function() {
                        init(invalidUser);
                    }, 2 * 60 * 1000);
                    createRealtimeUsersBlock(response.data);
                })
                .fail(function(error) {
                    Notification.exception(error);
                }).always(function() {
                    // Hide loader.
                    common.loader.hide("#liveusersblock");
                });

            // Search in table.
            $('body').on('input', searchTable, function() {
                liveUsersTable.column(0).search(this.value).draw();
            });
        }
    }

    /**
     * Create Datatable of the table
     * @param {Object} data Datatable data
     */
    function createRealtimeUsersBlock(data) {
        if (liveUsersTable) {
            liveUsersTable.destroy();
        } else {
            loader.hide();
            table.show();
        }

        liveUsersTable = table.DataTable({
            data: data,
            dom: '<"edwiserreports-table"<t><"table-pagination"p>>',
            language: {
                info: M.util.get_string('tableinfo', 'local_edwiserreports'),
                infoEmpty: M.util.get_string('infoempty', 'local_edwiserreports'),
                emptyTable: M.util.get_string('nousers', 'local_edwiserreports'),
                zeroRecords: M.util.get_string('zerorecords', 'local_edwiserreports'),
                paginate: {
                    previous: M.util.get_string('previous', 'moodle'),
                    next: M.util.get_string('next', 'moodle')
                }
            },
            aaSorting: [
                [1, 'asc']
            ],
            columnDefs: [{
                    "targets": 0,
                    "className": "text-left"
                },
                {
                    "targets": 1,
                    "className": "text-center"
                },
                {
                    "targets": 2,
                    "className": "text-center",
                    "orderable": false
                }
            ],
            drawCallback: function() {
                common.stylePaginationButton(this);
            },
            bInfo: false,
            lengthChange: false,
            initComplete: function() {
                if (data == undefined) {
                    return;
                }
                var usersCount = '<small class="ml-auto my-auto font-weight-bold">LoggedIn Users : ' + data.length + '</small>';
                $(document).find(".rtblock-filter").append(usersCount);
            }
        });
    }

    // Must return the init function
    return {
        init: init
    };
});
