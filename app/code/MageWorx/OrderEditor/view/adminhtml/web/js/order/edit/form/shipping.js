/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define(
    [
        'jquery',
        'mageworxOrderEditorTaxRateFactory',
        'MageWorx_OrderEditor/js/order/edit/form/base',
        'jquery/ui'
    ],
    function ($, taxRateFactory) {
        'use strict';

        $.widget('mage.mageworxOrderEditorShipping', $.mage.mageworxOrderEditorBase, {
            params: {
                updateUrl: '',
                loadFormUrl: '',

                cancelButtonId: '#shipping-method-cancel',
                submitButtonId: '#shipping-method-submit',
                editLinkId: '#ordereditor-shipping-link',

                blockId: 'shipping_method',
                formId: '#shipping-form',
                blockContainerId: '.admin__page-section-item-content',
                formContainerId: '.order-shipping-method',
                linkContainerId: '.admin__page-section-item-title',

                shippingMethodBlockId: '#order-shipping-method-choose'
            },

            init: function (params) {
                this.params = this._mergeParams(this.params, params);
                this._initParams(params);
                this._initActions();
                this.initInput();
            },

            getLoadFormParams: function () {
                var orderId = this.getCurrentOrderId();
                var blockId = this.params.blockId;
                return {'form_key': FORM_KEY, 'order_id': orderId, 'block_id': blockId};
            },

            /**
             * Form validation
             *
             * @returns {boolean}
             */
            validateForm: function () {
                return $(this.params.shippingMethodBlockId)
                    .find('input[name="order[shipping_method]"]:checked')
                    .length == 1;
            },

            getConfirmUpdateData: function () {
                var self = this;
                var orderId = this.getCurrentOrderId();
                var shippingMethod = self.getActiveShippingMethodCode();

                var params = {'form_key': FORM_KEY, 'shipping_method': shippingMethod, 'order_id': orderId};

                $(['price_excl_tax', 'price_incl_tax', 'tax_percent', 'description', 'discount_amount']).each(function (j, i) {
                    params[i] = $(self.params.shippingMethodBlockId)
                        .find('input[name="shipping_method[' + shippingMethod + '][' + i + ']"]')
                        .val();
                });

                var taxRatesSelected = $(self.params.shippingMethodBlockId)
                    .find('select[name^="shipping_method[' + shippingMethod + '][tax_rates]"]')
                    .val();

                params['tax_rates'] = {};
                $(taxRatesSelected).each(function (i,e){
                    params['tax_rates'][i] = {
                        'code': e,
                        'percent': $(self.params.shippingMethodBlockId)
                            .find('input[name="shipping_method[' + shippingMethod + '][tax_applied_rates][' + e + ']"]')
                            .val()
                    }
                });

                return params;
            },

            /**
             * Makes input fields responsible for initiating changes in the whole form.
             * Trigger display of the shipping method edit form.
             */
            initInput: function () {
                var self = this;
                var input = this.params.shippingMethodBlockId + ' input[type="text"]';
                var radio = this.params.shippingMethodBlockId + ' input[type="radio"]';
                var select = this.params.shippingMethodBlockId + ' select';

                $(document).off('change', input);
                $(document).on('change', input, function () {
                    self.calculateInputValues($(this));
                    self.calculateNewTotals();
                });

                $(document).off('change', select);
                $(document).on('change', select, function () {
                    self.changeTaxRates();
                    self.calculateInputValues($(this));
                    self.calculateNewTotals();
                });

                $(document).off('change', radio);
                $(document).on('change', radio, function () {
                    self.showEditForm($(this));
                    $(this).parent().find('input[type="text"]').change();
                    self.calculateNewTotals();
                });

                $(document).off('keypress', input);
                $(document).on('keypress', input, function (e) {
                    if (e.which == 13 || e.which == 8) {
                        return 1;
                    }
                    var letters = '1234567890.,+-';
                    return (letters.indexOf(String.fromCharCode(e.which)) != -1);
                });
            },

            showEditForm: function (radio) {
                var id = $(radio).val();
                $(this.params.shippingMethodBlockId).find('.edit_price_form').hide();
                $('#edit_price_form_' + id).show();
            },

            calculateInputValues: function (input) {
                var VRegExp = new RegExp(/shipping_method\[(\w+)\]\[(\w+)\]/);
                var VResult = $(input).attr('name').match(VRegExp);
                var id = VResult[1];
                var code = VResult[2];

                var $form = $(this.params.shippingMethodBlockId);

                var $priceExclTaxInput       = $form.find('input[name="shipping_method[' + id + '][price_excl_tax]"]');
                var $priceInclTaxInput       = $form.find('input[name="shipping_method[' + id + '][price_incl_tax]"]');
                var $taxPercentInput         = $form.find('input[name="shipping_method[' + id + '][tax_percent]"]');
                var $shippingDiscountInput   = $form.find('input[name="shipping_method[' + id + '][discount_amount]"]');

                var priceExclTax = 0,
                    taxPercent = 0,
                    taxAmount = 0,
                    priceInclTax = 0,
                    discountAmount = 0;

                if (code === 'price_incl_tax') {
                    priceInclTax    = this.getInputValue($priceInclTaxInput, 0);
                    taxPercent      = parseFloat(this.calculateShippingTaxPercent());
                    taxAmount       = (priceInclTax / (100 + taxPercent)) * taxPercent;
                    priceExclTax    = priceInclTax - taxAmount;
                    discountAmount  = parseFloat(this.getInputValue($shippingDiscountInput, 0));
                } else {
                    priceExclTax    = parseFloat(this.getInputValue($priceExclTaxInput, 0));
                    taxPercent      = parseFloat(this.calculateShippingTaxPercent());
                    taxAmount       = priceExclTax * (taxPercent / 100);
                    priceInclTax    = parseFloat(priceExclTax + taxAmount);
                    discountAmount  = parseFloat(this.getInputValue($shippingDiscountInput, 0));
                }

                // Setting the tax percent
                $taxPercentInput.val(taxPercent.toFixed(2));

                // Setting the discount amount
                $shippingDiscountInput.val(discountAmount.toFixed(2));

                // Setting the price excluding tax, discount does not affects that value
                $priceExclTaxInput.val(priceExclTax.toFixed(2));

                // Setting the price including tax, discount does not affects that value
                $priceInclTaxInput.val(priceInclTax.toFixed(2));

                $('#ordereditor-shipping-amount').val(priceExclTax.toFixed(2));
                $('#ordereditor-shipping-tax-amount').val(taxAmount.toFixed(2));
            },

            getInputValue: function (item, defaultValue) {
                var val = $(item).val();
                val = parseFloat(val);
                if (isNaN(val)) {
                    return defaultValue;
                }
                return val;
            },

            calculateNewTotals: function () {
                var subtotal = parseFloat($('#ordereditor-subtotal-amount').val());
                var subtotalTax = parseFloat($('#ordereditor-subtotal-tax-amount').val());
                $('#ordereditor-new-total-subtotal span').text(subtotal.toFixed(2));
                $('#ordereditor-new-total-subtotal-incl-tax span').text(
                    (subtotal + subtotalTax).toFixed(2)
                );

                var shippingExclTax = 0;
                shippingExclTax += parseFloat($('#ordereditor-shipping-amount').val());
                $('#ordereditor-new-total-shipping-shipping span').text(shippingExclTax.toFixed(2));

                { // Complex new totals tax calculation. Must be in specified order.
                    var tax = 0;
                    var shippingInclTax = parseFloat(shippingExclTax);
                    tax += parseFloat($('#ordereditor-shipping-tax-amount').val());
                    tax += parseFloat($('#ordereditor-discount-tax-compensation-amount').val());
                    shippingInclTax += tax;
                    $('#ordereditor-new-total-shipping-shipping-incl-tax').text(shippingInclTax.toFixed(2));

                    tax += parseFloat($('#ordereditor-subtotal-tax-amount').val());
                    $('#ordereditor-new-total-tax span').text(tax.toFixed(2));
                }

                var discount = 0;
                discount += parseFloat($('#ordereditor-discount-amount').val());
                discount += parseFloat($('#ordereditor-shipping-discount-amount').val());
                discount -= parseFloat($("input[name*='[discount_amount]']:visible").val());
                $('#ordereditor-new-total-discount span').text(discount.toFixed(2));

                var grandTotal = 0;
                grandTotal += parseFloat($('#ordereditor-subtotal-amount').val());
                grandTotal += parseFloat($('#ordereditor-subtotal-tax-amount').val());
                grandTotal += parseFloat($('#ordereditor-shipping-amount').val());
                grandTotal += parseFloat($('#ordereditor-shipping-tax-amount').val());
                grandTotal += parseFloat($('#ordereditor-discount-tax-compensation-amount').val());
                grandTotal += parseFloat($('#ordereditor-new-total-discount span').text());
                if ($('#ordereditor-new-total-giftcardaccount span').length) {
                    grandTotal += parseFloat($('#ordereditor-new-total-giftcardaccount span').text());
                }
                if ($('#ordereditor-new-total-store-credit span').length) {
                    grandTotal -= parseFloat($('#ordereditor-new-total-store-credit span').text());
                }
                $('#ordereditor-new-total-grand_total span').text(grandTotal.toFixed(2));

                $('#ordereditor_new_totals').show();
            },

            /**
             * Calculating tax percent based on the selected tax rates
             *
             * @returns {number}
             */
            calculateShippingTaxPercent: function calculateShippingTax () {
                var shippingMethod = this.getActiveShippingMethodCode(),
                    $rateInputs = $('#order-shipping-applied-rates-container-' + shippingMethod).find('input.tax-applied-rate-code'),
                    amountPercent = 0,
                    ratePercent,
                    $form = $(this.params.shippingMethodBlockId),
                    $taxPercentInput = $form.find('input[name="shipping_method[' + shippingMethod + '][tax_percent]"]');

                $rateInputs.each(function (i, e) {
                    ratePercent = $(this).val();
                    amountPercent += parseFloat(ratePercent);
                });

                $taxPercentInput.val(amountPercent.toFixed(2));

                return parseFloat(amountPercent.toFixed(2));
            },

            /**
             * Change tax percent based on the tax rates selection
             */
            changeTaxRates: function () {
                var $form = $(this.params.shippingMethodBlockId),
                    shippingMethod = this.getActiveShippingMethodCode(),
                    $rates = $form.find('select[name^="shipping_method[' + shippingMethod + '][tax_rates]"]'),
                    $optionsSelected = $rates.find('option:selected'),
                    $optionsNotSelected = $rates.find('option:not(:selected)');

                $optionsNotSelected.each(function (i, e) {
                    var rateCode = $(this).val(),
                        elemId = taxRateFactory.getElemId(shippingMethod, rateCode),
                        $elem = $('label[for="' + elemId + '"]');

                    if ($elem.length > 0) {
                        $elem.remove();
                    }
                });

                $optionsSelected.each(function (i, e) {
                    var rateCode = $(this).val(),
                        elemId = taxRateFactory.getElemId(shippingMethod, rateCode),
                        $elem = $('label[for="' + elemId + '"]'),
                        ratePercent = $(this).data('percent'),
                        $elemsContainer = $('#order-shipping-applied-rates-container-' + shippingMethod);

                    if ($elem.length == 0) {
                        $elem = taxRateFactory.createForShipping(shippingMethod, rateCode, ratePercent);
                        $elemsContainer.append($elem);
                    }
                });

                // Renew event handlers
                this.initInput();
            },

            /**
             * Get active shipping method code (selected method code)
             *
             * @returns {*|jQuery}
             */
            getActiveShippingMethodCode: function () {
                return $(this.params.shippingMethodBlockId)
                        .find('input[name="order[shipping_method]"]:checked')
                        .val();
            }
        });

        return $.mage.mageworxOrderEditorShipping;
    }
);

