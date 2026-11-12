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
 * Page top insight management js.
 *
 * @copyright   2021 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/* eslint-disable no-console */
define([
    'jquery',
    'core/notification',
    'core/templates',
    './common',
    './defaultconfig'
], function(
    $,
    Notification,
    Templates,
    common,
    CFG
) {

    /**
     * Selector for the insights.
     */
    let SELECTOR = {
        CONTAINER: '.top-insights',
        INSIGHT: '.top-insights .insight',
        ONLYINSIGHT: '.top-insights .insight:not(.add-insight)',
        INSIGHT_WRAP: '.insight-wrap',
        MOVELEFT: '.top-insights .card-editing .move-left',
        MOVERIGHT: '.top-insights .card-editing .move-right',
        HIDE: '.top-insights .card-editing .edit-hide',
        INSIGHT_ADD_CARD: '.top-insights .insight.add-insight',
        INSIGHT_DROPDOWNMENU: '.top-insights .add-new-insight .dropdown-menu',
        INSIGHT_ITEM: '.top-insights .add-new-insight .dropdown-item'
    };

    /**
     * Date filter selector matching the dropdown in edwiserreports.mustache.
     */
    let DATE_FILTER_SELECTOR = '.edwiserreports-calendar + .dropdown-menu .dropdown-item.active';

    /**
     * Filter object. Default will be read from dropdown on init.
     */
    let filter = 'last7days';

    /**
     * Promise list.
     */
    let PROMISE = {
        /**
         * Get insight card data to render insight card.
         * @param {String} id Insight id
         * @returns {Promise}
         */
        GET_INSIGHT_CARD_CONTEXT: function(id) {
            return $.ajax({
                url: CFG.requestUrl,
                type: CFG.requestType,
                dataType: CFG.requestDataType,
                data: {
                    action: 'get_insight_card_context_ajax',
                    secret: M.local_edwiserreports.secret,
                    lang: $('html').attr('lang'),
                    data: JSON.stringify({
                        id: id
                    })
                },
            });
        },

        /**
         * Get insight card data to render insight details.
         * @param {String} id Insight id
         * @return {Promise}
         */
        GET_INSIGHT_CARD_DATA: function(id) {
            return $.ajax({
                url: CFG.requestUrl,
                type: CFG.requestType,
                dataType: CFG.requestDataType,
                data: {
                    action: 'get_insight_card_data_ajax',
                    secret: M.local_edwiserreports.secret,
                    lang: $('html').attr('lang'),
                    data: JSON.stringify({
                        id: id,
                        filter: filter
                    })
                },
            });
        }
    };

    /**
     * Update insight order in the user preference.
     */
    function updateOrder() {
        let order = [];
        common.loader.show(SELECTOR.CONTAINER);
        $(SELECTOR.ONLYINSIGHT).each(function(index, insight) {
            order.push($(insight).data('id'));
        });
        // eslint-disable-next-line no-undef
        if (edwrnewpreferenceapply) {
            require(['core_user/repository'], function(UserRepository) {
                UserRepository.setUserPreference('local_edwiserreports_insights_order', JSON.stringify(order));
            });
        } else {
            M.util.set_user_preference('local_edwiserreports_insights_order', JSON.stringify(order));
        }
        common.loader.hide(SELECTOR.CONTAINER);
    }

    /**
     * Update insight card details.
     * @param {String} id Insight id
     */
    function updateInsight(id) {
        common.loader.show(SELECTOR.CONTAINER + ' [data-id="' + id + '"] .insight-wrapper');
        PROMISE.GET_INSIGHT_CARD_DATA(id, filter)
            .done(function(response) {
                var rtl = $('html').attr('dir') == 'rtl' ? 1 : 0;
                switch (id) {
                    case 'timespentoncourses':
                        response.value = rtl ? '4 h &#x202B; 48 min&#x202C; 20 s' : '4 h 48 min 20 s';

                    case 'timespentonsite':
                        // response.value = common.timeFormatter(response.value, {
                        //     dataPointIndex: 0,
                        //     'short': true
                        // }, rtl).replaceAll(', ', ' ');

                        response.value = rtl ? '3 h &#x202B; 38 min&#x202C; 20 s' : '3 h 38 min 20 s';

                        break;
                }
                Templates.render('local_edwiserreports/insights/content', response)
                    .done(function(html, js) {
                        Templates.replaceNode($(SELECTOR.INSIGHT + '[data-id="' + id + '"]')
                            .find(SELECTOR.INSIGHT_WRAP), html, js);
                        common.loader.hide(SELECTOR.CONTAINER + ' [data-id="' + id + '"] .insight-wrapper');
                    });
            });
    }

    /**
     * Initialize events.
     */
    function initEvents() {
        // Date selector listener.
        common.dateChange(function(date) {
            filter = date;
            $(SELECTOR.ONLYINSIGHT).each(function(index, insight) {
                updateInsight($(insight).data('id'));
            });
        });

        // Move left.
        $('body').on('click', SELECTOR.MOVELEFT, function() {
            let insight = $(this).closest(SELECTOR.INSIGHT);
            let attr = $('html').attr('dir');
            // In RTL mode, move-left button visually points right, so move right in DOM
            if (typeof attr !== 'undefined' && attr !== false && attr == 'rtl') {
                let next = insight.next();
                insight.detach().insertAfter(next);
            } else {
                let prev = insight.prev();
                insight.detach().insertBefore(prev);
            }
            updateOrder();
        });

        // Move Right.
        $('body').on('click', SELECTOR.MOVERIGHT, function() {
            let insight = $(this).closest(SELECTOR.INSIGHT);
            let attr = $('html').attr('dir');
            // In RTL mode, move-right button visually points left, so move left in DOM
            if (typeof attr !== 'undefined' && attr !== false && attr == 'rtl') {
                let prev = insight.prev();
                insight.detach().insertBefore(prev);
            } else {
                let next = insight.next();
                insight.detach().insertAfter(next);
            }
            updateOrder();
        });

        // Hide insight.
        $('body').on('click', SELECTOR.HIDE, function() {
            let insight = $(this).closest(SELECTOR.INSIGHT);
            $(SELECTOR.INSIGHT_DROPDOWNMENU)
                .prepend(`<a class="dropdown-item" href="#" data-id="${
                    insight.data('id')
                }">${insight.find('.insight-title').text()}</a>`);
            insight.remove();
            updateOrder();
        });

        // Add insight.
        $('body').on('click', SELECTOR.INSIGHT_ITEM, function() {
            let id = $(this).data('id');
            common.loader.hide(SELECTOR.CONTAINER);
            PROMISE.GET_INSIGHT_CARD_CONTEXT(id).done(function(response) {
                if (response.present == true) {
                    response.editing = true;
                    Templates.render('local_edwiserreports/insights/insight', response).done(function(html, js) {
                        var $newInsight = $(html);
                        var attr = $('html').attr('dir');
                        var isRtl = (typeof attr !== 'undefined' && attr !== false && attr == 'rtl');
                        
                        // Function to apply RTL support to an element
                        var applyRtlToElement = function($element) {
                            if (!isRtl || !$element || $element.length === 0) {
                                return;
                            }
                            // Find all arrow icons - search more broadly to catch all cases
                            $element.find('i.fa-arrow-left').each(function() {
                                var $icon = $(this);
                                if (!$icon.hasClass('rtl-support')) {
                                    $icon.removeClass("fa-arrow-left").addClass("fa-arrow-right rtl-support");
                                }
                            });
                            $element.find('i.fa-arrow-right').each(function() {
                                var $icon = $(this);
                                if (!$icon.hasClass('rtl-support')) {
                                    $icon.removeClass("fa-arrow-right").addClass("fa-arrow-left rtl-support");
                                }
                            });
                        };
                        
                        // Apply RTL support to HTML before inserting into DOM
                        applyRtlToElement($newInsight);
                        
                        $(SELECTOR.INSIGHT_ADD_CARD).before($newInsight);
                        Templates.runTemplateJS(js);
                        $(SELECTOR.INSIGHT_ITEM + '[data-id="' + id + '"]').remove();
                        
                        // Apply RTL support again after DOM insertion (using DOM element)
                        // Use both the jQuery reference and DOM selector to be sure
                        applyRtlToElement($newInsight);
                        var $domElement = $(SELECTOR.CONTAINER).find('.insight[data-id="' + id + '"]');
                        if ($domElement.length > 0) {
                            applyRtlToElement($domElement);
                        }
                        
                        // Also ensure all other insights have RTL support applied
                        if (isRtl) {
                            $(SELECTOR.CONTAINER).find('i.fa-arrow-left').each(function() {
                                var $icon = $(this);
                                if (!$icon.hasClass('rtl-support')) {
                                    $icon.removeClass("fa-arrow-left").addClass("fa-arrow-right rtl-support");
                                }
                            });
                            $(SELECTOR.CONTAINER).find('i.fa-arrow-right').each(function() {
                                var $icon = $(this);
                                if (!$icon.hasClass('rtl-support')) {
                                    $icon.removeClass("fa-arrow-right").addClass("fa-arrow-left rtl-support");
                                }
                            });
                        }
                        
                        // Final check after a micro-delay to catch any async DOM updates
                        if (isRtl) {
                            setTimeout(function() {
                                var $finalElement = $(SELECTOR.CONTAINER).find('.insight[data-id="' + id + '"]');
                                if ($finalElement.length > 0) {
                                    applyRtlToElement($finalElement);
                                }
                                // Force apply RTL support one more time to all insights
                                $(SELECTOR.CONTAINER).find('i.fa-arrow-left').not('.rtl-support')
                                    .removeClass("fa-arrow-left").addClass("fa-arrow-right rtl-support");
                                $(SELECTOR.CONTAINER).find('i.fa-arrow-right').not('.rtl-support')
                                    .removeClass("fa-arrow-right").addClass("fa-arrow-left rtl-support");
                            }, 50);
                        }
                        
                        updateOrder();
                        updateInsight(id);
                        
                        // Reload page after adding to ensure RTL support is properly applied
                        // This ensures all arrows are correctly displayed and all functionality works
                        setTimeout(function() {
                            window.location.reload();
                        }, 500);
                    });
                }
            }).fail(Notification.exception);
        });
    }

    /**
     * Initialize.
     */
    function init() {
        $(document).ready(function() {
            // Read the active date filter from the dropdown so default data matches the UI.
            var activeFilter = $(DATE_FILTER_SELECTOR).data('value');
            if (activeFilter) {
                filter = activeFilter;
            }
            initEvents();
            $(SELECTOR.ONLYINSIGHT).each(function(index, insight) {
                updateInsight($(insight).data('id'));
            });
            $(SELECTOR.CONTAINER).find('.overflow-hidden').removeClass('overflow-hidden');
        });
    }
    return {
        init: init
    };
});
