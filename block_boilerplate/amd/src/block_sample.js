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
 * Block service call and rendering defined in this file.
 *
 * @package     local_edwiserreports
 * @copyright   2021 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/* eslint-disable no-console */
define([
    'jquery',
    'local_edwiserreports/defaultconfig',
    './common'
], function($, cfg, common) {
    /**
     * Initialize
     * @param {function} invalidUser Callback function
     */
    function init(invalidUser) {

        var panel = cfg.getPanel("#sampleblock");

        if ($(panel).length) {
            // Show loader.
            common.loader.show('#sampleblock');

            /* Ajax request to get data for new block */
            $.ajax({
                    url: cfg.requestUrl,
                    type: cfg.requestType,
                    dataType: cfg.requestDataType,
                    data: {
                        action: 'get_sample_data_ajax',
                        secret: M.local_edwiserreports.secret,
                        lang: $('html').attr('lang')
                    },
                }).done(function(response) {
                    if (response.error === true && response.exception.errorcode === 'invalidsecretkey') {
                        invalidUser('sampleblock', response);
                        return;
                    }
                    // Hide loader.
                    common.loader.hide('#sampleblock');
                })
                .fail(function(error) {
                    // console.log(error);
                })
                .always(function() {
                    // Hide loader.
                    common.loader.hide('#sampleblock');
                });
        }

    }

    // Must return the init function
    return {
        init: init
    };
});
