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
    'core/templates',
    'local_edwiserreports/defaultconfig',
    './common'
], function($, templates, cfg, common) {
    var panel = cfg.getPanel("#siteaccessblock");
    var panelBody = cfg.getPanel("#siteaccessblock", "body");
    var table = cfg.getPanel("#siteaccessblock", "table");
    var loader = cfg.getPanel("#siteaccessblock", "loader");
    var accessDesc = "#accessinfodesctable";

    /**
     * Initialize
     * @param {function} notifyListner Callback function
     */
    function init(notifyListner) {
        $(document).ready(function() {
            generateAccessInfoGraph();
        });

        /**
         * Generate access info graph
         */
        function generateAccessInfoGraph() {

            if ($(panel).length) {
                // Show loader.
                common.loader.show('#siteaccessblock');
                $.ajax({
                    url: cfg.requestUrl,
                    type: cfg.requestType,
                    dataType: cfg.requestDataType,
                    data: {
                        action: 'get_siteaccess_data_ajax',
                        sesskey: $(panel).data("sesskey")
                    },
                })
                .done(function(response) {
                    // eslint-disable-next-line promise/catch-or-return
                    templates.render(cfg.getTemplate("siteaccessblock"), response.data)
                    .then(function(html, js) {
                        templates.replaceNodeContents(panelBody, html, js);
                        return;
                    })
                    .fail(function(ex) {
                        console.log(ex);
                    })
                    .always(function() {
                        $(accessDesc).show();
                        $(loader).remove();
                        $(table).removeClass("d-none");
                        $(panel + ' [data-toggle="tooltip"]').tooltip();

                        // Hide loader.
                        common.loader.hide('#siteaccessblock');
                    });
                })
                .fail(function(error) {
                    console.log(error);
                }).always(function() {
                    notifyListner("accessInfo");

                    // Hide loader.
                    common.loader.hide('#siteaccessblock');
                });
            } else {
                notifyListner("accessInfo");
            }
        }
    }

    // Must return the init function
    return {
        init: init
    };
});
