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
    "jquery",
    "core/templates",
    "local_edwiserreports/defaultconfig",
    './common',
], function($, templates, cfg, common) {
    var panel = cfg.getPanel("#certificatesblock");
    var panelBody = cfg.getPanel("#certificatesblock", "body");
    var table = panel + " .table";
    var dropdownBody = panel + " .table-dropdown";

    /**
     * Initialize
     */
    function init() {
        /**
         * Hide loader/
         */
        function hideLoader() {
            common.loader.hide('#certificatesblock');
        }

        if ($(panel).length) {
            // Show loader.
            common.loader.show('#certificatesblock');

            $.ajax({
                url: cfg.requestUrl,
                type: cfg.requestType,
                dataType: cfg.requestDataType,
                data: {
                    action: 'get_certificates_data_ajax',
                    sesskey: $(panel).data("sesskey")
                },
            }).done(function(response) {
                templates.render('local_edwiserreports/certificatestable', response.data)
                    .then(function(html, js) {
                        $(panelBody).empty();
                        templates.appendNodeContents(panelBody, html, js);
                        createCertificatesTable(response.data);

                        // Hide loader.
                        hideLoader();
                        return;
                    }).fail(function(ex) {
                        console.log(ex);

                        // Hide loader.
                        hideLoader();
                    });
            })
            .fail(function(error) {
                console.log(error);

                // Hide loader.
                hideLoader();
            });
        }
    }

    /**
     * Create certificate table.
     */
    function createCertificatesTable() {
        $(table).DataTable({
            oLanguage: {
                sEmptyTable: "There is no certificate created",
                sSearchPlaceholder: "Search Certificates"
            },
            initComplete: function() {
                $(dropdownBody).show();
            },
            drawCallback: function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-sm pull-right');
                $('.dataTables_filter').addClass('pagination-sm pull-right');
            },
            lengthChange: false,
            bInfo: false
        });
    }

    // Must return the init function
    return {
        init: init
    };
});
