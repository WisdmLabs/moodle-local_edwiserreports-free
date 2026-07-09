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
    'core/notification',
    './common',
    './insights',
    './defaultconfig',
    'local_edwiserreports/flatpickr',
    './block_siteaccess',
    './block_activecourses',
    './block_activeusers',
    './block_courseprogress',
    './block_courseengagement',
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
    Notification,
    common,
    insights,
    CFG,
    flatpickrLib,
    siteAccess,
    activeCourses,
    activeUsers,
    courseProgress,
    courseEngagement,
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
     * Promises.
     */
    var PROMISE = {
        /**
         * Get time period label to show in the header.
         * @param {String} timeperiod Time period.
         * @returns {Promise}
         */
        GET_TIMEPERIOD_LABEL: function(timeperiod) {
            return $.ajax({
                url: CFG.requestUrl,
                type: CFG.requestType,
                dataType: CFG.requestDataType,
                data: {
                    action: 'get_timeperiod_label_data_ajax',
                    secret: M.local_edwiserreports.secret,
                    lang: $('html').attr('lang'),
                    data: timeperiod
                }
            });
        },
    };

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
        courseEngagement,
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
     * Show time duration in header.
     * @param {String} date Time period.
     */
    function showTimeLabel(date) {
        PROMISE.GET_TIMEPERIOD_LABEL(date).done(function(response) {
            let startdate = new Date(response.startdate * 86400000);
            let enddate = new Date(response.enddate * 86400000);
            let startDay = startdate.getUTCDate();
            startDay = startDay < 10 ? '0' + startDay : startDay;
            let endDay = enddate.getUTCDate();
            endDay = endDay < 10 ? '0' + endDay : endDay;
            let startMonth = startdate.toLocaleString('default', { month: 'long', timeZone: 'UTC' });
            let startYear = startdate.getUTCFullYear();
            let endMonth = enddate.toLocaleString('default', { month: 'long', timeZone: 'UTC' });
            let endYear = enddate.getUTCFullYear();

            let customdate = startDay + ' ' + startMonth + ' ' + startYear + ' - ' +
            endDay + ' ' + endMonth + ' ' + endYear;
            let dirattr = $('html').attr('dir');
            if(dirattr == 'rtl'){
                startdate = startYear + ' ' + startMonth + ' ' + startDay;
                enddate = endYear + ' ' + endMonth + ' ' + endDay;
                customdate = enddate + '-' + startdate;
                $(SELECTOR.DATE).css({'direction':'ltr','text-align': 'right'});
                $(SELECTOR.DATEPICKERINPUT).css({'direction':'ltr','text-align': 'right'});
            }
            $(SELECTOR.DATESELECTED).html(customdate);
        }).fail(function(ex) {
            Notification.exception(ex);
        });
    }

    /**
     * Throw an event with date change data.
     * @param {String} date  Date
     * @param {String} label Date label
     */
    function throwDateEvent(date, label) {
        let dateChangeEvent = new CustomEvent('edwiserreport:datechange', {
            detail: { date: date }
        });
        document.dispatchEvent(dateChangeEvent);
        showTimeLabel(date, label);
    }

    /**
     * After Select Custom date get active users details.
     */
    function customDateSelected() {
        let date = $(SELECTOR.DATEPICKERINPUT).val();
        let dateAlternate = $(SELECTOR.DATEPICKERINPUT).next().val().replace("to", "-");
        let dirattr = $('html').attr('dir');
        let stringarr = dateAlternate.split('-');
        if(dirattr == 'rtl'){
            let startdate = stringarr[0].split(' ');
            let enddate = stringarr[1].split(' ');
            startdate = startdate[2] + ' ' + startdate[1] + ' ' + startdate[0];
            enddate = enddate[3] + ' ' + enddate[2] + ' ' + enddate[1];
            dateAlternate = enddate + '-' + startdate;
            $(SELECTOR.DATE).css({'direction':'ltr','text-align': 'right'});
            $(SELECTOR.DATEPICKERINPUT).css({'direction':'ltr','text-align': 'right'});
        }
        $(SELECTOR.DATEPICKERINPUT).next().val(dateAlternate);
        if (!date.includes(" to ")) {
            flatpickr.clear();
            return;
        }
        $(SELECTOR.DATEITEM).removeClass('active');
        $(SELECTOR.DATEITEM + '.custom').addClass('active');
        $(SELECTOR.DATE).html(dateAlternate);
        throwDateEvent(date, dateAlternate);
    }

    /**
     * Initialize flatpickr calendar.
     */
    function initFlatpickr() {
        var fp = flatpickrLib || window.flatpickr;
        if (!fp) {
            console.error('Flatpickr library not available');
            return false;
        }
        var inputElement = $(SELECTOR.DATEPICKERINPUT)[0];
        if (!inputElement) {
            console.error('Flatpickr input element not found');
            return false;
        }
        flatpickr = fp(inputElement, {
            mode: 'range',
            altInput: true,
            altFormat: "d M Y",
            dateFormat: "Y-m-d",
            maxDate: "today",
            appendTo: $(SELECTOR.DATEPICKER).get(0),
            onOpen: function() {
                var cal = this.calendarContainer;
                var $menu = $(SELECTOR.DATEMENU);
                $menu.addClass('withcalendar');
                var container = $menu.find('.dropdown-calendar')[0];
                if (container && cal) {
                    container.appendChild(cal);
                    // positionCalendar() runs after onOpen, so we override it after the call stack clears
                    setTimeout(function() {
                        cal.style.setProperty('position', 'absolute', 'important');
                        cal.style.setProperty('top', '0', 'important');
                        cal.style.setProperty('left', '0', 'important');
                        cal.style.setProperty('right', 'auto', 'important');
                        cal.style.setProperty('box-shadow', 'none', 'important');
                        cal.style.setProperty('border', '0', 'important');
                    }, 0);
                }
            },
            onClose: function() {
                $(SELECTOR.DATEMENU).removeClass('withcalendar');
                customDateSelected();
            }
        });
        return true;
    }

    /**
     * Init main.js
     */
    var init = function() {
        $(document).ready(function() {
            insights.init();
            let currentDate = $(SELECTOR.DATEITEM + '.active').data('value');
            showTimeLabel(currentDate, $(SELECTOR.DATEITEM + '.active').text());
            common.handleSearchInput();
            $('.singleselect').each(function(index, select) {
                $(select).val($(select).find('option:nth-child(1)').val());
            });
            blocks.forEach(block => {
                try {
                    block.init(validateUser);
                } catch (e) {
                    console.error(e);
                }
            });

            if (initFlatpickr()) {
                $('body').on('click', SELECTOR.DATEITEM + ":not(.custom)", function() {
                    $(SELECTOR.DATEITEM).removeClass('active');
                    $(this).addClass('active');
                    $(SELECTOR.DATE).html($(this).text());
                    if (flatpickr) {
                        flatpickr.clear();
                    }
                    throwDateEvent($(this).data('value'), $(this).text());
                });
                $('body').on('click', SELECTOR.DATEITEM + '.custom', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var input = $(this).find('.flatpickr')[0];
                    if (input && input._flatpickr) {
                        input._flatpickr.open();
                    } else if (input) {
                        input.focus();
                    }
                    return false;
                });
            }
        });
    };

    return { init: init };
});
