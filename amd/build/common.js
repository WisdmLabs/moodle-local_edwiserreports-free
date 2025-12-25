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
    'jquery',
    'core/notification',
    'core/fragment',
    'core/modal_factory',
    'core/modal_events',
    'core/ajax',
    'core/templates',
    './defaultconfig',
    './events',
    './variables',
    './selectors',
    './vendor/jspdf',
    './vendor/apexcharts',
    './select2',
    './jquery.dataTables',
    './dataTables.bootstrap4'
], function(
    $,
    Notification,
    Fragment,
    ModalFactory,
    ModalEvents,
    Ajax,
    Templates,
    CFG,
    EdwiserReportsEvents,
    v,
    selector,
    jsPDF,
    ApexCharts
) {
    // Polyfill for _typeof helper function (needed for jspdf.min.js when loaded directly)
    // This MUST be defined before jsPDF is used
    (function() {
        if (typeof window !== 'undefined' && typeof window._typeof === 'undefined') {
            window._typeof = function(obj) {
                return obj && typeof Symbol !== "undefined" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
            };
        }
        if (typeof globalThis !== 'undefined' && typeof globalThis._typeof === 'undefined') {
            globalThis._typeof = window._typeof || function(obj) {
                return obj && typeof Symbol !== "undefined" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
            };
        }
        if (typeof global !== 'undefined' && typeof global._typeof === 'undefined') {
            global._typeof = window._typeof || function(obj) {
                return obj && typeof Symbol !== "undefined" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
            };
        }
    })();

    /**
     * Selectors list.
     */
    let SELECTOR = {
        TABLE: '.edwiserreports-table',
        FILTER: '.table-filter',
        TAB: '.edwiserreports-tabs .dropdown',
        CAPEDIT: '.report-actions [data-action="editcap"]',
        DATESELECTED: '.selected-period span',
        SEARCHTABLE: '.table-search-input input',
        PAGINATION: '.table-pagination',
        PAGINATIONITEM: '.paginate_button',
        EXPORTGRAPHPDF: '.download-links button[value="pdfimage"]',
        EXPORTGRAPHPNG: '.download-links button[value="png"]',
        EXPORTGRAPHJPEG: '.download-links button[value="jpeg"]',
        EXPORTGRAPHSVG: '.download-links button[value="svg"]'
    };

    /**
     * Direction parameter.
     */
    var direction = $('html').attr('dir');

    /**
     * All promises.
     */
    var PROMISE = {
        /**
         * Get filter data.
         *
         * @param {Integer} cohortid    Cohort id
         * @param {Integer} courseid    Course id
         * @param {Integer} groupid     Group id
         * @returns {PROMISE}
         */
        GET_FILTER_DATA: function(types, cohortid, courseid, groupid) {
            return Ajax.call([{
                methodname: 'local_edwiserreports_get_filter_data',
                args: {
                    types: types,
                    cohort: cohortid,
                    course: courseid,
                    group: groupid
                }
            }], false)[0];
        },
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
        }
    };

    // Loader functions.
    var loader = {
        show: function(id, position) {
            var $class;
            if (id == undefined) {
                id = 'body';
                $class = 'position-fixed';
            } else {
                $class = 'position-absolute';
            }
            if (position != undefined) {
                $class = position;
            }
            $(id).append(`
            <div class="edwiserreports-loader ${$class}">
                <div class="animation-wrapper">
                    <div class="fa-animation">
                        <i class="fa fa-cog fa-lg fa-spin"></i>
                        <i class="fa fa-cog fa-md fa-spin spin-reverse"></i>
                        <i class="fa fa-cog fa-sm fa-spin spin-reverse"></i>
                    </div>
                    ${M.util.get_string('loading', 'moodle')}
                </div>
            </div>
            `);
        },
        hide: function(id) {
            if (id == undefined) {
                id = 'body';
            }
            $(id).find('.edwiserreports-loader').remove();
        }
    };

    /**
     * Email Shcedule Modal Related Parameters end
     */

    $(document).ready(function() {
        // Trigger graphical export function
        ['EXPORTGRAPHPDF', 'EXPORTGRAPHPNG', 'EXPORTGRAPHJPEG', 'EXPORTGRAPHSVG']
        .forEach(function(id) {
            $('body').on('click', SELECTOR[id], function() {
                let target = $(this).closest('.edwiserReport-block').data('block');
                let exportEvent = new CustomEvent(EdwiserReportsEvents[id] + '-' + target);
                setTimeout(function() {
                    document.dispatchEvent(exportEvent);
                }, 0);
            });
        });

        // Show reports page when document is ready
        $('#wdm-edwiserreports').removeClass('d-none');

        setupBlockEditing(selector.blockSettingsBtn);
        setupBlockEditing('.erp-custom-edit-settings');
        setupRtlSupport();
        setupBlockHiding($('#wdm-edwiserreports').data("editing"));
        
        // Initialize tooltips
        initTooltips();
    });
    
    /**
     * Initialize tooltips for Bootstrap 4 and Bootstrap 5
     */
    function initTooltips() {
        // Copy data-title to title if title is not present
        $('[data-title]').each(function () {
            if (!$(this).attr('title')) {
                $(this).attr('title', $(this).data('title'));
            }
        });

        // Init Bootstrap 4 tooltips
        if (typeof $.fn.tooltip !== 'undefined') {
            $('[data-toggle="tooltip"]').tooltip();
        }
        
        // Init Bootstrap 5 tooltips using core/tooltip (load dynamically)
        require(['core/tooltip'], function(Tooltip) {
            if (typeof Tooltip !== 'undefined') {
                $('[data-bs-toggle="tooltip"]').each(function() {
                    new Tooltip(this);
                });
            }
        });
    }


    /**
     * Check if RTL is enabled and then convert arrows direction
     */
    function setupRtlSupport() {

        setTimeout(function(){
            var attr = $('html').attr('dir');
            // For some browsers, `attr` is undefined; for others,
            // `attr` is false.  Check for both.
            if (typeof attr !== 'undefined' && attr !== false && attr == 'rtl') {
            $('.dropdown-item.custom').css({'direction':'ltr','text-align': 'right'});

            $('.insight-wrapper .fa-arrow-left:not(.rtl-support)').removeClass("fa-arrow-left").addClass("fa-arrow-right rtl-support");
            $('.insight-wrapper .fa-arrow-right:not(.rtl-support)').removeClass("fa-arrow-right").addClass("fa-arrow-left rtl-support");
        }
        $('.edwiserreports-table .page-item.next a').empty();
        $('.edwiserreports-table .page-item.previous a').empty();
    }, 1000);
    }

    /**
     * Setup block hiding
     * @param {Boolean} editing True if editing is on
     */
    function setupBlockHiding(editing) {
        var editForm = '<div id="editing-btn" class="d-flex flex-wrap">';
        editForm += '<div class="ml-auto d-flex">';
        var editingtxt;

        // Change editing option
        if (editing) {
            editing = 0;
            editingtxt = "Stop Customising this page";

            editForm += '<div class="singlebutton">';
            editForm += '<form method="post" action="' + M.cfg.wwwroot + '/local/edwiserreports/index.php">';
            editForm += '<input type="hidden" name="reset" value="1">';
            editForm += '<input type="hidden" name="sesskey" value="' + M.cfg.sesskey + '">';
            editForm += '<button type="submit" class="btn btn-secondary" title="">Reset Page to Default</button>';
            editForm += '</form>';
            editForm += '</div>';
        } else {
            editing = 1;
            editingtxt = "Customise this page";
        }

        editForm += '<div class="singlebutton">';
        editForm += '<form method="post" action="' + M.cfg.wwwroot + '/local/edwiserreports/index.php">';
        editForm += '<input type="hidden" name="edit" value="' + editing + '">';
        editForm += '<input type="hidden" name="sesskey" value="' + M.cfg.sesskey + '">';
        editForm += '<button type="submit" class="btn btn-secondary" title="">' + editingtxt + '</button>';
        editForm += '</form>';
        editForm += '</div>';

        editForm += '</div></div>';

        $("#page-local-edwiserreports-index #page-header .card-body").append(editForm);
    }

    /**
     * Setup block setting button
     */
    function setupBlockEditing(settingsSelector) {
        $(document).on('click', settingsSelector, function(e) {


            e.preventDefault();

            var contextid = $(e.currentTarget).data('contextid');
            var blockname = $(e.currentTarget).data('blockname');
            var action = $(e.currentTarget).data('action');

            if (action == 'edit') {
                ModalFactory.create({
                    title: M.util.get_string('editblocksetting', 'local_edwiserreports'),
                    body: Fragment.loadFragment(
                        'local_edwiserreports',
                        'get_blocksetting_form',
                        contextid, {
                            blockname: blockname
                        }
                    )
                }).done(function(modal) {
                    var root = modal.getRoot();
                    modal.modal.addClass('modal-dialog-centered');

                    root.on(ModalEvents.bodyRendered, function() {
                        var form = modal.modal.find('.block-settings-form');
                        modal.modal.find('.save-block-settings').on('click', function(event) {
                            event.preventDefault();
                            var formData = form.serializeArray();
                            var data = {};
                            $(formData).each(function($k, $d) {
                                data[$d.name] = $d.value;
                            });

                            // Set users preferences
                            setBlockPreference(blockname, data);
                        });
                    });

                    root.on(ModalEvents.hidden, function() {
                        modal.destroy();
                    });
                    modal.show();
                });
            } else if (action == "hide") {
                var hidden = $(this).data("hidden");
                var _this = $(this);
                $.ajax({
                    url: v.requestUrl,
                    type: 'GET',
                    data: {
                        action: 'toggle_hide_block_ajax',
                        sesskey: M.cfg.sesskey,
                        data: JSON.stringify({
                            'blockname': blockname,
                            'hidden': hidden
                        })
                    }
                }).done(function(response) {
                    response = JSON.parse(response);
                    if (response.success) {
                        if (hidden) {
                            _this.closest('.edwiserReport-block').removeClass('block-hidden');
                            _this.data("hidden", 0);
                            _this.html(M.util.get_string('hide', 'local_edwiserreports'));
                        } else {
                            _this.closest('.edwiserReport-block').addClass('block-hidden');
                            _this.data("hidden", 1);
                            _this.html(M.util.get_string('unhide', 'local_edwiserreports'));
                        }
                    }
                });
            } else if (action == "editcap") {
                ModalFactory.create({
                    title: M.util.get_string('editblockcapabilities', 'local_edwiserreports'),
                    body: Fragment.loadFragment(
                        'local_edwiserreports',
                        'get_blockscap_form',
                        contextid, {
                            blockname: blockname
                        }
                    )
                }).done(function(modal) {
                    var root = modal.getRoot();
                    modal.modal.addClass('modal-dialog-centered modal-lg');

                    root.on(ModalEvents.bodyRendered, function() {
                        var form = modal.modal.find('.block-cap-form');
                        modal.modal.find('#menucapabilities').on('change', function(event) {
                            event.preventDefault();
                            var formData = form.serializeArray();
                            var data = {};
                            $(formData).each(function($k, $d) {
                                data[$d.name] = $d.value;
                            });

                            Fragment.loadFragment(
                                'local_edwiserreports',
                                'block_overview_display',
                                contextid, {
                                    capvalue: data.capabilities
                                }
                                // eslint-disable-next-line no-unused-vars
                            ).done(function(html, js, css) {
                                Templates.replaceNodeContents(modal.modal.find('.cap-overview'), html, js);
                                switchCapabilitiesBlock(modal);
                            });
                        });

                        switchCapabilitiesBlock(modal);

                        form = modal.modal.find('.block-cap-form');
                        modal.modal.find('.save-block-caps').on('click', function(event) {
                            event.preventDefault();
                            var formData = form.serializeArray();
                            var data = {};
                            $(formData).each(function($k, $d) {
                                data[$d.name] = $d.value;
                            });

                            // Set block capabilities
                            setBlockCapabilities(blockname, data, function() {
                                modal.hide();
                            });
                        });
                    });

                    root.on(ModalEvents.hidden, function() {
                        modal.destroy();
                    });
                    modal.show();
                });
            }
        });
    }

    /**
     * Switch capability block.
     * @param {Object} modal Modal object
     */
    function switchCapabilitiesBlock(modal) {
        modal.modal.find('.comparisontable .switch-capability').on('click', function(event) {
            var permissions = $(event.currentTarget).find('input[type=radio]');
            var current = permissions.filter(':checked');
            var next = permissions.eq(permissions.index(current) + 1);
            if (next.length === 0) {
                next = permissions.eq(0);
            }
            next.prop('checked', true);

            var perStr = next.data('strpermission');
            var perClass = next.data('permissionclass');

            $(event.currentTarget).removeClass('inherit allow prevent prohibit');
            $(event.currentTarget).addClass(perClass);
            $(event.currentTarget).find('label').html(perStr);
        });
    }

    /**
     * Set blocks capabilities
     * @param {string} blockname
     * @param {string} data
     * @param {function} callback
     */
    function setBlockCapabilities(blockname, data, callback) {
        data.blockname = blockname;
        data = JSON.stringify(data);
        var sesskey = $('#' + blockname).data('sesskey');

        // Update users capability
        $.ajax({
            url: v.requestUrl,
            type: 'GET',
            data: {
                action: 'set_block_capability_ajax',
                sesskey: sesskey,
                data: data
            }
        }).done(function(response) {
            response = JSON.parse(response);
            if (response.success) {
                callback();
                location.reload();
            } else {
                Notification.addNotification({
                    message: "Error",
                    type: "error"
                });
            }
        }).fail(function(error) {
            console.log(error);
            Notification.addNotification({
                message: error,
                type: "error"
            });
        }).always(function() {
            callback();
            location.reload();
        });
    }

    /**
     * Set users preferences
     * @param {string} blockname Block name
     * @param {string} data Data for preference
     */
    function setBlockPreference(blockname, data) {
        data.blockname = blockname;
        data = JSON.stringify(data);
        var sesskey = $('#' + blockname).data('sesskey');

        // Set users preferences
        $.ajax({
            url: v.requestUrl,
            type: 'GET',
            data: {
                action: 'set_block_preferences_ajax',
                sesskey: sesskey,
                data: data
            }
        }).fail(function(error) {
            console.log(error);
            Notification.addNotification({
                message: error,
                type: "error"
            });
        }).always(function() {
            location.reload();
        });
    }

    /**
     * Send plain formatted time.
     * @param {Number} h Hours
     * @param {Number} m Minutes
     * @param {Number} s Seconds
     * @returns {String}
     */
    function timePlainFormat(h, m, s) {
        if (h > 0) {
            h = h < 10 ? "0" + h : h;
        } else {
            h = "00";
        }
        if (m > 0) {
            m = m < 10 ? "0" + m : m;
        } else {
            m = "00";
        }
        if (s > 0) {
            s = s < 10 ? "0" + s : s;
        } else {
            s = "00";
        }
        return h + ":" + m + ":" + s;
    }

    /**
     * Convert seconds to HH:MM:SS
     * @param {Integer} seconds Seconds
     * @param {Object} opts Options
     * @returns {String}
     */
    function timeFormatter(seconds, opts) {
        seconds = Number(seconds);
        var h = Math.floor(seconds / 3600);
        var m = Math.floor(seconds % 3600 / 60);
        var s = Math.floor(seconds % 3600 % 60);

        rtl = $('html').attr('dir') == 'rtl' ? 1 : 0;


        if (typeof opts == 'object' && opts.dataPointIndex !== undefined && opts.dataPointIndex !== -1) {
            var time = [];
            var short = opts.short !== undefined && opts.short;

            if(rtl == 1){

                if (s > 0) {
                    if (short) {
                        time.push(M.util.get_string('secondshort', 'local_edwiserreports')+ " " + s  );
                    } else {
                        time.push(M.util.get_string(s == 1 ? 'second' : 'seconds', 'local_edwiserreports')+ " " + s  );
                    }
                }

                if (m > 0) {
                    if (short) {
                        time.push(M.util.get_string('minuteshort', 'local_edwiserreports')+ " " + m);
                    } else {
                        time.push(M.util.get_string(m == 1 ? 'minute' : 'minutes', 'local_edwiserreports')+ " " + m);
                    }
                }
                if (h > 0) {
                    if (short) {
                        time.push( M.util.get_string('hourshort', 'local_edwiserreports')+ " " + h);
                    } else {
                        time.push( M.util.get_string(h == 1 ? 'hour' : 'hours', 'local_edwiserreports')+ " " + h);
                    }
                }

            } else {

                if (h > 0) {
                    if (short) {
                        time.push(h + " " + M.util.get_string('hourshort', 'local_edwiserreports'));
                    } else {
                        time.push(h + " " + M.util.get_string(h == 1 ? 'hour' : 'hours', 'local_edwiserreports'));
                    }
                }
                if (m > 0) {
                    if (short) {
                        time.push(m + " " + M.util.get_string('minuteshort', 'local_edwiserreports'));
                    } else {
                        time.push(m + " " + M.util.get_string(m == 1 ? 'minute' : 'minutes', 'local_edwiserreports'));
                    }
                }
                if (s > 0) {
                    if (short) {
                        time.push(s + " " + M.util.get_string('secondshort', 'local_edwiserreports'));
                    } else {
                        time.push(s + " " + M.util.get_string(s == 1 ? 'second' : 'seconds', 'local_edwiserreports'));
                    }
                }
            }

            if (time.length == 0) {
                time.push(0);
            }

            return time.join(', ');
        }

        return rtl == 1 ? timePlainFormat(s, m, h) : timePlainFormat(h, m, s);
    }

    /**
     * Render insight card.
     * @param {String} selector DOM selector
     * @param {Object} data     Insight data
     */
    function insight(selector, data) {
        data.insight.title = M.util.get_string(data.insight.title, 'local_edwiserreports');
        if (data.details !== undefined && data.details.data !== undefined) {
            data.details.data.forEach(function(value, index) {
                data.details.data[index].title = M.util.get_string(value.title, 'local_edwiserreports');
            });
        }
        Templates.render('local_edwiserreports/insight-placeholder', {})
            .done(function(html, js) {
                Templates.replaceNodeContents(selector, html, js);
                Templates.render('local_edwiserreports/insight', data)
                    .done(function(html, js) {
                        Templates.replaceNodeContents(selector, html, js);
                    })
                    .fail(function(ex) {
                        Notification.exception(ex);
                        $(selector).remove();
                    });
            });
    }

    /**
     * Styling pagination button of data table.
     * @param {DOM} element Table element
     */
    function stylePaginationButton(element) {
        let pagination = $(element).closest(SELECTOR.TABLE).find(SELECTOR.PAGINATION);
        if (pagination.find(SELECTOR.PAGINATIONITEM).length < 4) {
            pagination.addClass('d-none');
            return;
        }
        pagination.removeClass('d-none');
        pagination.find(SELECTOR.PAGINATIONITEM).not('.previous').not('.next').not('.active').addClass('pagination-number');
        pagination.find(SELECTOR.PAGINATIONITEM + ':not(.active) a').addClass('theme-primary-text');
        pagination.find(SELECTOR.PAGINATIONITEM + '.active a').addClass('text-dark');
        pagination.find(SELECTOR.PAGINATIONITEM + '.previous a').addClass('theme-primary-border rounded');
        pagination.find(SELECTOR.PAGINATIONITEM + '.next a').addClass('theme-primary-border rounded');
    }

    /**
     * This function calls callback function when user change the date.
     *
     * @param {function} callback Callback function to handle date change
     */
    function dateChange(callback) {
        $(document).on(EdwiserReportsEvents.DATECHANGE, function(event) {
            callback(event.detail.date);
        });
    }

    /**
     * Handling highlight of search input.
     */
    function handleSearchInput() {
        /* Table search listener */
        $('body').on('input', SELECTOR.SEARCHTABLE, function() {
            $(this).toggleClass('empty', $(this).val() === '');
        });
    }

    /**
     * Handle filters size based on visibility.
     * @param {String} PANEL Panel name
     * @returns {Void}
     */
    function handleFilterSize(PANEL) {
        // Skip if page is not dashboard.
        if (!$(PANEL).closest('body').is('#page-local-edwiserreports-index')) {
            return;
        }

        let removeClasses = ['col-md-3', 'col-md-4', 'col-md-6', 'col-md-12'];
        let addClasses = [];
        switch ($(PANEL).find('.filter-selector:not(.d-none)').length) {
            case 1:
                addClasses = removeClasses.splice(removeClasses.indexOf('col-md-12'), 1);
                break;
            case 2:
                addClasses = removeClasses.splice(removeClasses.indexOf('col-md-6'), 1);
                break;
            case 3:
                addClasses = removeClasses.splice(removeClasses.indexOf('col-md-4'), 1);
                break;
            case 4:
                addClasses = removeClasses.splice(removeClasses.indexOf('col-md-3'), 1);
                break;
        }
        $(PANEL).find('.filter-selector').addClass(addClasses.join(' ')).removeClass(removeClasses.join(' '));
    }

    /**
     * Refresh filter based on response.
     * @param {string} type Type of filter
     * @param {object} response Response from server
     * @param {sring} PANEL Panel name
     * @param {function} callback Callback function
     */
    function refreshFilter(type, response, PANEL, callback) {
        let length = response[type + 's'] !== undefined ? response[type + 's'].length : 0;
        $(PANEL).find(`.${type}-select`).closest('.filter-selector').toggleClass('d-none', length === 0);
        handleFilterSize(PANEL);
        if (length !== 0) {
            $(PANEL).find(`.${type}-select`).select2('destroy');
            Templates.render('local_edwiserreports/filters/' + type + 's_filter', response)
                .done(function(html, js) {


                    Templates.replaceNode($(PANEL).find(`.${type}-select`), html, js);

                    // Reinitialize student selector select2 instance.
                    $(PANEL).find(`.${type}-select`).select2();
                    if (callback !== undefined) {
                        callback();
                    }
                });
        } else if (callback !== undefined) {
            callback();
        }
    }

    /**
     * Refresh filter based on response.
     * @param {string} report Type of filter
     * @param {object} response Response from server
     * @param {sring} PANEL Panel name
     * @param {function} callback Callback function
     */
    function refreshSummarycard(report, response, PANEL, callback) {

        Templates.render('local_edwiserreports/summary/summary-card', response)
        .done(function(html, js) {

            Templates.replaceNode($(".summary-card"), html, js);

            // Reinitialize student selector select2 instance.
            // $(PANEL).find(`.${type}-select`).select2();
            if (callback !== undefined) {
                callback();
            }
        });
    }

    /**
     * Handling reloading of filters
     * @param {String}      PANEL       selector
     * @param {Array}       types       Types of filter fetch
     * @param {Integer}     cohortid    Cohort id
     * @param {Integer}     courseid    Course id
     * @param {Integer}     groupid     Group id
     * @param {Function}    callback    Callback function
     */
    function reloadFilter(PANEL, types, cohortid, courseid, groupid, callback) {
        PROMISE.GET_FILTER_DATA(types, cohortid, courseid, groupid)
            .done(function(response) {
                // Remove flags.
                if (types.indexOf('noallusers') !== -1) {
                    types.splice(types.indexOf('noallusers'), 1);
                }
                if (types.indexOf('noallcourses') !== -1) {
                    types.splice(types.indexOf('noallcourses'), 1);
                }

                response = JSON.parse(response);
                let i = 0;
                for (; i < types.length - 1; i++) {
                    refreshFilter(types[i], response[types[i]], PANEL, undefined);
                }
                refreshFilter(types[i], response[types[i]], PANEL, callback);
            });
    }

    /**
     * Apply precision to number. If number is whole then return as it is.
     * @param {Number} value     Value to apply precision
     * @param {Number} precision Precision to apply
     * @returns {Number}
     */
    function toPrecision(value, precision) {
        if (value % 1 === 0) {
            return value;
        }
        return value.toPrecision(precision);
    }

    /**
     * Covert filter object to string for export postfix
     * @param {String}  blockName Name of the block
     * @param {Object}  filter    Filter parameter
     * @return {String}           postfix for exporting filename
     */
    function filterToString(blockName, filter) {
        let name = [];
        name.push('block_' + blockName);
        if (typeof filter === 'object') {
            Object.entries(filter).forEach(function(item) {
                name.push(item.join('-'));
            });
        } else {
            name.push(filter);
        }
        return name.join('_');
    }

    /**
     * Export Graph in PDF format
     * @param {ApexChart} chart     ApexChart object
     * @param {Object}    filter    Filter object
     * @param {String}    filename  Name for file. Default undefined. If undefined then filename generated using filter parameter.
     */
    function exportGraphPDF(chart, filter, width, height, filename) {
        if (filename == undefined) {
            filename = filterToString(chart.opts.chart.id, filter);
        }
        chart.dataURI({
            scale: 1.5
        }).then(function(uri) {

            const imageData = uri.imgURI.replaceAll('image/png', 'image/jpeg');

            let style = "";
            if (width !== undefined && height !== undefined) {
                style = ` width="${width}" height="${height}"`;
            }

            var canvas = $(`<canvas${style}></canvas>`).get(0);
            var ctx = canvas.getContext("2d");

            var image = new Image();
            image.onload = function() {
                ctx.drawImage(image, 0, 0);
            };
            image.src = imageData;

            const margin = 15;

            const pageWidth = canvas.width;
            const pageHeight = canvas.height;

            // Ensure _typeof is available before using jsPDF
            if (typeof window._typeof === 'undefined') {
                window._typeof = function(obj) {
                    return obj && typeof Symbol !== "undefined" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
                };
            }
            
            var doc = jsPDF('l', 'px', [pageWidth + (margin * 2), pageHeight + (margin * 2)]);

            doc.addImage(imageData, 'png', margin, margin, pageWidth, pageHeight);
            doc.save(filename + '.pdf');
        });
    }

    /**
     * Export chart in JPEG format.
     * @param {ApexChart} chart     ApexChart object
     * @param {Object}    filter    Filter object
     * @param {String}    filename  Name for file. Default undefined. If undefined then filename generated using filter parameter.
     */
    function exportGraphJPEG(chart, filter, filename) {
        if (filename == undefined) {
            filename = filterToString(chart.opts.chart.id, filter);
        }
        chart.dataURI({
            scale: 1.5
        }).then(function(uri) {
            var a = document.createElement("a"); //Create <a>
            a.href = uri.imgURI.replaceAll('image/png', 'image/jpeg'); //Image Base64 Goes here
            a.download = filename + ".jpeg"; //File name Here
            a.click(); //Downloaded file
        });
    }

    /**
     * Export Graph in PNG format
     * @param {ApexChart} chart     ApexChart object
     * @param {Object}    filter    Filter object
     * @param {String}    filename  Name for file. Default undefined. If undefined then filename generated using filter parameter.
     */
    function exportGraphPNG(chart, filter, filename) {
        if (filename == undefined) {
            filename = filterToString(chart.opts.chart.id, filter);
        }
        chart.dataURI({
            scale: 1.5
        }).then(function(uri) {
            var a = document.createElement("a"); //Create <a>
            a.href = uri.imgURI; //Image Base64 Goes here
            a.download = filename + ".png"; //File name Here
            a.click(); //Downloaded file
        });
    }

    /**
     * Export Graph in SVG format
     * @param {ApexChart} chart     ApexChart object
     * @param {Object}    filter    Filter object
     * @param {String}    filename  Name for file. Default undefined. If undefined then filename generated using filter parameter.
     */
    function exportGraphSVG(chart, filter, filename) {
        if (filename == undefined) {
            filename = filterToString(chart.opts.chart.id, filter);
        }
        ApexCharts.exec(chart.opts.chart.id, 'updateOptions', {
            chart: {
                toolbar: {
                    export: {
                        svg: {
                            filename: filename
                        }
                    }
                }
            }
        }, false, true);
        chart.exports.exportToSVG();
    }

    /**
     * Header navigation dropdown handler.
     */
    function headerNavigation() {
        // Explicitly handling dropdown issue.
        $('body').on('click', function(e) {
            let button = $(e.target).closest(SELECTOR.TAB);
            $(`${SELECTOR.TAB}.show`).not(button)
                .closest(SELECTOR.TAB).removeClass('show');
            if (button.length) {
                button.closest(SELECTOR.TAB).toggleClass('show');
            }
        });
    }

    /**
     * Handling report page capabilities.
     */
    function handleReportCapability() {
        let contextid = $(SELECTOR.CAPEDIT).data('contextid');
        let reportname = $(SELECTOR.CAPEDIT).data('reportname');
        $(SELECTOR.CAPEDIT).click(function() {
            ModalFactory.create({
                title: M.util.get_string('editreportcapabilities', 'local_edwiserreports'),
                body: Fragment.loadFragment(
                    'local_edwiserreports',
                    'get_reportscap_form',
                    contextid, {
                        name: reportname
                    }
                )
            }).done(function(modal) {
                var root = modal.getRoot();
                modal.modal.addClass('modal-dialog-centered modal-lg');

                root.on(ModalEvents.bodyRendered, function() {
                    var form = modal.modal.find('.block-cap-form');
                    modal.modal.find('#menucapabilities').on('change', function(event) {
                        event.preventDefault();
                        var formData = form.serializeArray();
                        var data = {};
                        $(formData).each(function($k, $d) {
                            data[$d.name] = $d.value;
                        });

                        Fragment.loadFragment(
                            'local_edwiserreports',
                            'block_overview_display',
                            contextid, {
                                capvalue: data.capabilities
                            }
                            // eslint-disable-next-line no-unused-vars
                        ).done(function(html, js, css) {
                            Templates.replaceNodeContents(modal.modal.find('.cap-overview'), html, js);
                            switchCapabilitiesBlock(modal);
                        });
                    });

                    switchCapabilitiesBlock(modal);

                    form = modal.modal.find('.block-cap-form');
                    modal.modal.find('.save-block-caps').on('click', function(event) {
                        event.preventDefault();
                        var formData = form.serializeArray();
                        var data = {};
                        $(formData).each(function($k, $d) {
                            data[$d.name] = $d.value;
                        });

                        // Set block capabilities
                        setBlockCapabilities(reportname, data, function() {
                            modal.hide();
                        });
                    });
                });

                root.on(ModalEvents.hidden, function() {
                    modal.destroy();
                });
                modal.show();
            });
        });
    }

    /**
     * Format epoch date in given format.
     *
     * @param {Integer} date   Epoch date
     * @param {String}  format Date format
     * @param {Boolean} utc    To return in utc format
     * @returns {String}
     */
    function formatDate(date, format, utc) {
        var MMMM = ["\x00", "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
        var MMM = ["\x01", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        var dddd = ["\x02", "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
        var ddd = ["\x03", "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

        // eslint-disable-next-line require-jsdoc
        function ii(i, len) {
            var s = i + "";
            len = len || 2;
            while (s.length < len) {
                s = "0" + s;
            }
            return s;
        }

        var y = utc ? date.getUTCFullYear() : date.getFullYear();
        format = format.replace(/(^|[^\\])yyyy+/g, "$1" + y);
        format = format.replace(/(^|[^\\])yy/g, "$1" + y.toString().substr(2, 2));
        format = format.replace(/(^|[^\\])y/g, "$1" + y);

        var M = (utc ? date.getUTCMonth() : date.getMonth()) + 1;
        format = format.replace(/(^|[^\\])MMMM+/g, "$1" + MMMM[0]);
        format = format.replace(/(^|[^\\])MMM/g, "$1" + MMM[0]);
        format = format.replace(/(^|[^\\])MM/g, "$1" + ii(M));
        format = format.replace(/(^|[^\\])M/g, "$1" + M);

        var d = utc ? date.getUTCDate() : date.getDate();
        format = format.replace(/(^|[^\\])dddd+/g, "$1" + dddd[0]);
        format = format.replace(/(^|[^\\])ddd/g, "$1" + ddd[0]);
        format = format.replace(/(^|[^\\])dd/g, "$1" + ii(d));
        format = format.replace(/(^|[^\\])d/g, "$1" + d);

        var H = utc ? date.getUTCHours() : date.getHours();
        format = format.replace(/(^|[^\\])HH+/g, "$1" + ii(H));
        format = format.replace(/(^|[^\\])H/g, "$1" + H);

        // eslint-disable-next-line no-nested-ternary
        var h = H > 12 ? H - 12 : H == 0 ? 12 : H;
        format = format.replace(/(^|[^\\])hh+/g, "$1" + ii(h));
        format = format.replace(/(^|[^\\])h/g, "$1" + h);

        var m = utc ? date.getUTCMinutes() : date.getMinutes();
        format = format.replace(/(^|[^\\])mm+/g, "$1" + ii(m));
        format = format.replace(/(^|[^\\])m/g, "$1" + m);

        var s = utc ? date.getUTCSeconds() : date.getSeconds();
        format = format.replace(/(^|[^\\])ss+/g, "$1" + ii(s));
        format = format.replace(/(^|[^\\])s/g, "$1" + s);

        var f = utc ? date.getUTCMilliseconds() : date.getMilliseconds();
        format = format.replace(/(^|[^\\])fff+/g, "$1" + ii(f, 3));
        f = Math.round(f / 10);
        format = format.replace(/(^|[^\\])ff/g, "$1" + ii(f));
        f = Math.round(f / 10);
        format = format.replace(/(^|[^\\])f/g, "$1" + f);

        var T = H < 12 ? "AM" : "PM";
        format = format.replace(/(^|[^\\])TT+/g, "$1" + T);
        format = format.replace(/(^|[^\\])T/g, "$1" + T.charAt(0));

        var t = T.toLowerCase();
        format = format.replace(/(^|[^\\])tt+/g, "$1" + t);
        format = format.replace(/(^|[^\\])t/g, "$1" + t.charAt(0));

        var tz = -date.getTimezoneOffset();
        // eslint-disable-next-line no-nested-ternary
        var K = utc || !tz ? "Z" : tz > 0 ? "+" : "-";
        if (!utc) {
            tz = Math.abs(tz);
            var tzHrs = Math.floor(tz / 60);
            var tzMin = tz % 60;
            K += ii(tzHrs) + ":" + ii(tzMin);
        }
        format = format.replace(/(^|[^\\])K/g, "$1" + K);

        var day = (utc ? date.getUTCDay() : date.getDay()) + 1;
        format = format.replace(new RegExp(dddd[0], "g"), dddd[day]);
        format = format.replace(new RegExp(ddd[0], "g"), ddd[day]);

        format = format.replace(new RegExp(MMMM[0], "g"), MMMM[M]);
        format = format.replace(new RegExp(MMM[0], "g"), MMM[M]);

        format = format.replace(/\\(.)/g, "$1");

        return format;
    }

    /**
     * Update time duration in header.
     * @param {String} date Time period.
     */
    function updateTimeLabel(timeperiod) {
        if (timeperiod == 'all') {
            $(SELECTOR.DATESELECTED).html(M.util.get_string('alltime', 'local_edwiserreports'));
            return;
        }
        PROMISE.GET_TIMEPERIOD_LABEL(timeperiod).done(function(response) {
            let startdate = new Date(response.startdate * 86400000);
            let enddate = new Date(response.enddate * 86400000);
            let startDay = startdate.getDate();
            startDay = startDay < 10 ? '0' + startDay : startDay;
            let endDay = enddate.getDate();
            endDay = endDay < 10 ? '0' + endDay : endDay;

            var date = `${startDay} ${startdate.toLocaleString('default', { month: 'long' })} ${startdate.getFullYear()} -
            ${endDay} ${enddate.toLocaleString('default', { month: 'long' })} ${enddate.getFullYear()}</span>`;
            if(direction == 'rtl'){
                // format for rtl : yyyy mm dd
                startdate = startdate.getFullYear() + ' ' + startdate.toLocaleString('default', { month: 'long' }) + ' ' + startDay;
                enddate = enddate.getFullYear() + ' ' + enddate.toLocaleString('default', { month: 'long' }) + ' ' + endDay;
                date = enddate + '-' + startdate;

                // Making direction ltr for date selector and aligning text to right
                $(SELECTOR.DATESELECTED).css({'direction':'ltr','text-align': 'right'});
            }
            $(SELECTOR.DATESELECTED).html(date);

            // $(SELECTOR.DATESELECTED).html('<div style="display:flex;"><div>' + `${startDay} ${startdate.toLocaleString('default', { month: 'long' })} ${startdate.getFullYear()}` + '</div> - <div>' +
            // `${endDay} ${enddate.toLocaleString('default', { month: 'long' })} ${enddate.getFullYear()}</span>` + '</div>');
        }).fail(function(ex) {
            Notification.exception(ex);
        });
    }

    /**
     * Handle export options dropdown.
     * @param {String} target Target id
     */
    function exportDropdownHandler(target) {
        // Explicitly handling dropdown issue.
        $('body').on('click', function(e) {
            let button = $(e.target).closest(`${target} [role="button"]`);
            $(`${target}.show [role="button"]`).not(button)
                .closest(`${target}`).removeClass('show');
            if (button.length) {
                button.closest(`${target}`).toggleClass('show');
            }
        });
    }

    return {
        loader: loader,
        toPrecision: toPrecision,
        insight: insight,
        timeFormatter: timeFormatter,
        dateChange: dateChange,
        formatDate: formatDate,
        stylePaginationButton: stylePaginationButton,
        handleSearchInput: handleSearchInput,
        reloadFilter: reloadFilter,
        refreshFilter: refreshFilter,
        refreshSummarycard: refreshSummarycard,
        handleFilterSize: handleFilterSize,
        filterToString: filterToString,
        exportGraphPDF: exportGraphPDF,
        exportGraphJPEG: exportGraphJPEG,
        exportGraphPNG: exportGraphPNG,
        exportGraphSVG: exportGraphSVG,
        headerNavigation: headerNavigation,
        handleReportCapability: handleReportCapability,
        updateTimeLabel: updateTimeLabel,
        exportDropdownHandler: exportDropdownHandler,
        setupBlockEditing:setupBlockEditing
    };
});
