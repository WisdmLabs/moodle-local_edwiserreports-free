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
    'local_edwiserreports/jquery.dataTables',
    'local_edwiserreports/dataTables.bootstrap4'
], function($, templates, cfg) {
    var panel = cfg.getPanel("#f2fsessionsblock");
    var panelBody = cfg.getPanel("#f2fsessionsblock", "body");

    /**
     * Initialize
     */
    function init() {
        $.ajax({
            url: cfg.requestUrl,
            type: cfg.requestType,
            dataType: cfg.requestDataType,
            data: {
                action: 'get_f2fsession_data_ajax',
                sesskey: $(panel).data("sesskey"),
                data: JSON.stringify({
                })
            },
        })
        .done(function(response) {
            templates.render(cfg.getTemplate('f2fsessiontable'), response.data)
                .then(function(html, js) {
                    $(panelBody).empty();
                    templates.appendNodeContents(panelBody, html, js);
                    return;
                }).fail(function(ex) {
                    console.log(ex);
                });
        })
        .fail(function(error) {
            console.log(error);
        });
    }

    // Must return the init function
    return {
        init: init
    };
});
