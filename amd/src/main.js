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
define([
    'jquery',
    'core/modal_factory',
    './common',
    './insights',
    './block_siteaccess',
    './block_activecourses',
    './block_activeusers',
    './block_courseprogress',
    './block_inactiveusers',
    './block_realtimeusers',
    './block_todaysactivity',
    './block_grade',
    './block_visitsonsite',
    './block_timespentonsite',
    './block_timespentoncourse',
    './block_courseactivitystatus',
    './block_learnercourseprogress',
    './block_learnertimespentonsite'
], function(
    $,
    ModalFactory,
    common,
    insights,
    siteAccess,
    activeCourses,
    activeUsers,
    courseProgress,
    inactiveUsers,
    realTimeUsers,
    todaysActivity,
    grade,
    visitsonsite,
    timespentonsite,
    timespentoncourse,
    courseactivitystatus,
    learnercourseprogress,
    learnertimespentonsite
) {

    /**
     * Selector list.
     */
    var SELECTOR = {
        ROOT: '#wdm-edwiserreports',
        DATESELECTED: '.selected-period',
        DATE: '.edwiserreports-calendar',
        DATEMENU: '.edwiserreports-calendar + .dropdown-menu',
        DATEITEM: '.edwiserreports-calendar + .dropdown-menu .dropdown-item',
        DATEPICKER: '.edwiserreports-calendar + .dropdown-menu .dropdown-calendar',
        DATEPICKERINPUT: '.edwiserreports-calendar + .dropdown-menu .flatpickr'
    };

    /**
     * Blocks list.
     */
    var blocks = [
        siteAccess,
        activeCourses,
        activeUsers,
        courseProgress,
        inactiveUsers,
        realTimeUsers,
        todaysActivity,
        grade,
        visitsonsite,
        timespentonsite,
        timespentoncourse,
        courseactivitystatus,
        learnercourseprogress,
        learnertimespentonsite
    ];

    /**
     * Flat picker custom date.
     */
    let flatpickr = null;

    /**
     * This function will show validation error in block card.
     * @param {String} blockid Block id
     * @param {Object} response User validation response
     */
    function validateUser(blockid, response) {
        $(`#${blockid} .panel-body`).html(response.exception.message);
    }

    /**
     * Throw an event with date change data.
     * @param {String} date  Date
     * @param {String} label Date label
     */
    function throwDateEvent(date, label) {
        let dateChangeEvent = new CustomEvent('edwiserreport:datechange', {
            detail: {
                date: date
            }
        });
        document.dispatchEvent(dateChangeEvent);
        $(SELECTOR.DATESELECTED).html(label);
    }

    /**
     * After Select Custom date get active users details.
     */
    function customDateSelected() {
        let date = $(SELECTOR.DATEPICKERINPUT).val(); // Y-m-d format
        let dateAlternate = $(SELECTOR.DATEPICKERINPUT).next().val().replace("to", "-"); // Date d M Y format.
        $(SELECTOR.DATEPICKERINPUT).next().val(dateAlternate);

        /* If correct date is not selected then return false */
        if (!date.includes(" to ")) {
            flatpickr.clear();
            return;
        }

        // Set active class to custom date selector item.
        $(SELECTOR.DATEITEM).removeClass('active');
        $(SELECTOR.DATEITEM + '.custom').addClass('active');

        // Show custom date to dropdown button.
        $(SELECTOR.DATE).html(dateAlternate);

        // Throw date change event.
        throwDateEvent(date, dateAlternate);
    }

    /**
     * Init main.js
     */
    var init = function() {
        $(document).ready(function() {

            insights.init();

            common.handleSearchInput();

            blocks.forEach(block => {
                block.init(validateUser);
            });

            flatpickr = $(SELECTOR.DATEPICKERINPUT).flatpickr({
                mode: 'range',
                altInput: true,
                altFormat: "d M Y",
                dateFormat: "Y-m-d",
                maxDate: "today",
                appendTo: $(SELECTOR.DATEPICKER).get(0),
                onOpen: function() {
                    $(SELECTOR.DATEMENU).addClass('withcalendar');
                    $(SELECTOR.DATE).dropdown('update');
                },
                onClose: function() {
                    $(SELECTOR.DATEMENU).removeClass('withcalendar');
                    customDateSelected();
                }
            });

            /* Date selector listener */
            $('body').on('click', SELECTOR.DATEITEM + ":not(.custom)", function() {
                // Set custom selected item as active.
                $(SELECTOR.DATEITEM).removeClass('active');
                $(this).addClass('active');

                // Show selected item on dropdown button.
                $(SELECTOR.DATE).html($(this).text());

                // Clear custom date.
                flatpickr.clear();

                // Throw date change event.
                throwDateEvent($(this).data('value'), $(this).text());
            });
        });
    };

    // Must return the init function
    return {
        init: init
    };
});
