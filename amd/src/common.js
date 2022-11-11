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
    'core/templates',
    './variables',
    './selectors',
    './select2',
    './jquery.dataTables',
    './dataTables.bootstrap4'
], function(
    $,
    Notification,
    Fragment,
    ModalFactory,
    ModalEvents,
    Templates,
    v,
    selector
) {
    /**
     * Selectors list.
     */
    let SELECTOR = {
        TABLE: '.edwiserreports-table',
        FILTER: '.table-filter',
        TAB: '.edwiserreports-tabs .dropdown',
        SEARCHTABLE: '.table-search-input input',
        PAGINATION: '.table-pagination',
        PAGINATIONITEM: '.paginate_button'
    };

    Templates.render('local_edwiserreports/insight-placeholder', {});
    Templates.render('local_edwiserreports/insight', {});

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
            $(id + ' > .edwiserreports-loader').remove();
        }
    };

    $(document).ready(function() {
        // Show reports page when document is ready
        $('#wdm-edwiserreports').removeClass('d-none');

        setupBlockEditing();

        setupBlockHiding($('#wdm-edwiserreports').data("editing"));
    });

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
    function setupBlockEditing() {
        $(document).on('click', selector.blockSettingsBtn, function(e) {
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

        if (typeof opts == 'object' && opts.dataPointIndex !== undefined && opts.dataPointIndex !== -1) {
            var time = [];
            var short = opts.short !== undefined && opts.short;
            if (h > 0) {
                if (short) {
                    time.push(h + " " + "h.");
                } else {
                    time.push(h + " " + (h == 1 ? M.util.get_string('hour', 'local_edwiserreports') :
                        M.util.get_string('hours', 'local_edwiserreports')));
                }
            }
            if (m > 0) {
                if (short) {
                    time.push(m + " " + "min.");
                } else {
                    time.push(m + " " + (m == 1 ? M.util.get_string('minute', 'local_edwiserreports') :
                        M.util.get_string('minutes', 'local_edwiserreports')));
                }
            }
            if (s > 0) {
                if (short) {
                    time.push(s + " " + "sec.");
                } else {
                    time.push(s + " " + (s == 1 ? M.util.get_string('second', 'local_edwiserreports') :
                        M.util.get_string('seconds', 'local_edwiserreports')));
                }
            }
            if (time.length == 0) {
                time.push(0);
            }
            return time.join(', ');
        }
        return timePlainFormat(h, m, s);
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
        pagination.find(SELECTOR.PAGINATIONITEM).addClass(v.datatableClasses.buttonSpacing);
        pagination.find(SELECTOR.PAGINATIONITEM + ' a').addClass(v.datatableClasses.buttonSize);
        pagination.find(SELECTOR.PAGINATIONITEM + '.active a').addClass(v.datatableClasses.buttonActive);
        pagination.find(SELECTOR.PAGINATIONITEM + ':not(.active) a').addClass(v.datatableClasses.buttonInactive);
        pagination.find(SELECTOR.PAGINATIONITEM + '.previous a').addClass(v.datatableClasses.prevNextSpacing);
        pagination.find(SELECTOR.PAGINATIONITEM + '.next a').addClass(v.datatableClasses.prevNextSpacing);
        // Different margin.
        pagination.find(SELECTOR.PAGINATIONITEM + '.previous').removeClass(v.datatableClasses.buttonsSpacing)
            .addClass('mx-4');
        pagination.find(SELECTOR.PAGINATIONITEM + '.next').removeClass(v.datatableClasses.buttonsSpacing)
            .addClass('mx-4');

    }

    /**
     * This function calls callback function when user change the date.
     *
     * @param {function} callback Callback function to handle date change
     */
    function dateChange(callback) {
        $(document).on('edwiserreport:datechange', function(event) {
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
     * Handle filters size based on visibility.
     * @param {String} PANEL Panel name
     */
    function handleFilterSize(PANEL) {
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
            return;
        });
    }

    return {
        loader: loader,
        toPrecision: toPrecision,
        insight: insight,
        timeFormatter: timeFormatter,
        dateChange: dateChange,
        stylePaginationButton: stylePaginationButton,
        handleSearchInput: handleSearchInput,
        headerNavigation: headerNavigation,
        handleFilterSize: handleFilterSize
    };
});