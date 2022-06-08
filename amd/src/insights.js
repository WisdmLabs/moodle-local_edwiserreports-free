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
     * Filter object.
     */
    let filter = 'weekly';

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
        M.util.set_user_preference('local_edwiserreports_insights_order', JSON.stringify(order));
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
                switch (id) {
                    case 'timespentoncourses':
                    case 'timespentonsite':
                        response.value = common.timeFormatter(response.value, {
                            dataPointIndex: 0,
                            'short': true
                        }).replaceAll(', ', ' ');
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
            let prev = $(this).closest(SELECTOR.INSIGHT).prev();
            $(this).closest(SELECTOR.INSIGHT).detach().insertBefore(prev);
            updateOrder();
        });

        // Move Right.
        $('body').on('click', SELECTOR.MOVERIGHT, function() {
            let next = $(this).closest(SELECTOR.INSIGHT).next();
            $(this).closest(SELECTOR.INSIGHT).detach().insertAfter(next);
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
                        $(SELECTOR.INSIGHT_ADD_CARD).before(html);
                        Templates.runTemplateJS(js);
                        $(SELECTOR.INSIGHT_ITEM + '[data-id="' + id + '"]').remove();
                        updateOrder();
                        updateInsight(id);
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
