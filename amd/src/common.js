/* eslint-disable no-console */
define([
    'jquery',
    'core/notification',
    'core/fragment',
    'core/modal_factory',
    'core/modal_events',
    'core/templates',
    'core/str',
    './variables',
    './selectors',
    './templateselector',
    './jspdf',
    './select2',
    './jquery.dataTables',
    './dataTables.bootstrap4'
], function(
    $,
    notif,
    Fragment,
    ModalFactory,
    ModalEvents,
    Templates,
    str,
    v,
    selector,
    tempSelector,
    jsPDF
) {


    /**
     * Selectors list.
     */
    let SELECTOR = {
        TABLE: '.edwiserreports-table',
        FILTER: '.table-filter',
        SEARCHTABLE: '.table-search-input input',
        PAGINATION: '.table-pagination',
        PAGINATIONITEM: '.paginate_button'
    };

    Templates.render('local_edwiserreports/insight-placeholder', {});
    Templates.render('local_edwiserreports/insight', {});

    // Loader functions.
    var loader = {
        show: function(id) {
            var $class;
            if (id == undefined) {
                id = 'body';
                $class = 'position-fixed';
            } else {
                $class = 'position-absolute';
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
        var exportPdf = '.download-links button[value="pdf"]';

        // Export data in pdf
        $(document).on("click", exportPdf, function(e) {
            e.preventDefault();

            var _this = this;
            var form = $(_this).closest("form");
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: {
                    'type': $(_this).val(),
                    'block': form.find('input[name="block"]').val(),
                    'filter': form.find('input[name="filter"]').val(),
                    'cohortid': form.find('input[name="cohortid"]').val(),
                    'region': form.find('input[name="region"]').val()
                }
            }).done(function(response) {
                response = JSON.parse(response);
                var pdf = jsPDF('p', 'pt', 'a4');
                var margins = {
                    top: 40,
                    bottom: 30,
                    left: 10,
                    width: "100%"
                };

                pdf.setFontSize(10);

                // ID selector for either ID or node name. ("#iAmID", "div", "span" etc.)
                // There is no support for any other type of selectors
                // (class, of compound) at this time.
                var specialElementHandlers = {
                    // Element with id of "bypass" - jQuery style selector
                    // eslint-disable-next-line no-unused-vars
                    '#bypassme': function(element, renderer) {
                        // True = "handled elsewhere, bypass text extraction"
                        return true;
                    }
                };
                // All coords and widths are in jsPDF instance's declared units
                // 'inches' in this case
                pdf.fromHTML(
                    response.data.html, // HTML string or DOM elem ref.
                    margins.left, // X coord
                    margins.top, { // Y coord
                        'width': margins.width, // Max width of content on PDF
                        'elementHandlers': specialElementHandlers
                    },
                    // eslint-disable-next-line no-unused-vars
                    function(dispose) {
                        // Dispose: object with X, Y of the last line add to the PDF
                        //          this allow the insertion of new lines after html
                        pdf.save(response.data.filename);
                    }, margins);
            }).always(function() {
                $(document).find('#cover-spin').hide();
            });
        });

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
                    title: 'Edit Block Setting',
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
                    title: 'Edit Block Capabilities',
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
                                modal.modal.find('.cap-overview').html(html);
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
                notif.addNotification({
                    message: "Error",
                    type: "error"
                });
            }
        }).fail(function(error) {
            console.log(error);
            notif.addNotification({
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
            notif.addNotification({
                message: error,
                type: "error"
            });
        }).always(function() {
            location.reload();
        });
    }

    /**
     * Convert seconds to HH:MM:SS
     * @param {Integer} seconds Seconds
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
                    time.push(h + " " + (h == 1 ? "hour" : "hours"));
                }
            }
            if (m > 0) {
                if (short) {
                    time.push(m + " " + "min.");
                } else {
                    time.push(m + " " + (m == 1 ? "minute" : "minutes"));
                }
            }
            if (s > 0) {
                if (short) {
                    if (time.length === 0) {
                        time.push(s + " " + "sec.");
                    }
                } else {
                    time.push(s + " " + (s == 1 ? "second" : "seconds"));
                }
            }
            if (time.length == 0) {
                time.push(0);
            }
            return time.join(', ');
        }
        return [
            h > 0 ? (h < 10 ? "0" + h : h) : "00",
            m > 0 ? (m < 10 ? "0" + m : m) : "00",
            s > 0 ? (s < 10 ? "0" + s : s) : "00",
        ].join(':');
    }

    /**
     * Render insight card.
     * @param {String} selector DOM selector
     * @param {Object} data     Insight data
     */
    function insight(selector, data) {
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
        pagination.find(SELECTOR.PAGINATIONITEM).addClass(v.datatableClasses.buttonSpacing);
        pagination.find(SELECTOR.PAGINATIONITEM + ' a').addClass(v.datatableClasses.buttonSize);
        pagination.find(SELECTOR.PAGINATIONITEM + '.active a').addClass(v.datatableClasses.buttonActive);
        pagination.find(SELECTOR.PAGINATIONITEM + ':not(.active) a').addClass(v.datatableClasses.buttonInactive);
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

    return {
        loader: loader,
        insight: insight,
        timeFormatter: timeFormatter,
        dateChange: dateChange,
        stylePaginationButton: stylePaginationButton,
        handleSearchInput: handleSearchInput
    };
});
