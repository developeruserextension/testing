/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define(
    [
        'jquery',
        'jquery/ui'
    ],
    function ($) {
        'use strict';

        return {

            /**
             * Create new input element with label and title for the tax rate
             *
             * @param orderItemId
             * @param rateCode
             * @param ratePercent
             * @returns {*|m.fn.init|jQuery.fn.init|n.fn.init|r.fn.init|p.fn.init}
             */
            create: function create(orderItemId, rateCode, ratePercent) {
                var id = "tax-applied-rates-" + orderItemId + "-" + rateCode + "",
                    $label = $('<label>', {"for": id}),
                    $title = $('<b>', {"text": rateCode}),
                    $br = $('<br>'),
                    $percent = $('<span>', {"text": '%', "class": "currency-span"}),
                    parsedPercent = parseFloat(ratePercent),
                    processedPercent = !isNaN(parsedPercent) && isFinite(parsedPercent) ?
                        parsedPercent.toFixed(2) :
                        0.00,
                    $input = $('<input />', {
                        "name": "item[" + orderItemId + "][tax_applied_rates][" + rateCode + "]",
                        "id": id,
                        "title": rateCode + "(" + ratePercent + "%)",
                        "class": "mw-order-editor-order-item tax-applied-rate-code",
                        "value": processedPercent
                    });

                $label.append($input).prepend($percent).prepend($br).prepend($title);

                return $label;
            },

            createForShipping: function createForShipping(shippingCode, taxRateCode, taxRatePercent)
            {
                var id = "tax-applied-rates-" + shippingCode + "-" + taxRateCode + "",
                    $label = $('<label>', {"for": id}),
                    $title = $('<b>', {"text": taxRateCode}),
                    parsedPercent = parseFloat(taxRatePercent),
                    processedPercent = !isNaN(parsedPercent) && isFinite(parsedPercent) ?
                        parsedPercent.toFixed(2) :
                        0.00,
                    $input = $('<input />', {
                        "name": "shipping_method[" + shippingCode + "][tax_applied_rates][" + taxRateCode + "]",
                        "id": id,
                        "title": taxRateCode + "(" + taxRatePercent + "%)",
                        "class": "mw-order-editor-order-shipping tax-applied-rate-code",
                        "type": "text",
                        "value": processedPercent
                    });

                $label.append($input).prepend($title);

                return $label;
            },

            /**
             * Return id of the element using order item id and tax rate code
             *
             * @param orderItemId
             * @param rateCode
             * @returns {string}
             */
            getElemId: function getElementId(orderItemId, rateCode) {
                return "tax-applied-rates-" + orderItemId + "-" + rateCode + "";
            }
        };
    }
);