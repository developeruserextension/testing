define(
    [
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/action/select-shipping-method',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/action/select-payment-method',
        'Amasty_Checkout/js/model/vault-payment-resolver',
        'uiRegistry',
        'underscore',
        'mage/utils/wrapper'
    ],
    function (
        quote,
        checkoutData,
        selectShippingMethodAction,
        paymentService,
        selectPaymentMethodAction,
        vaultResolver,
        registry,
        _,
        wrapper
    ) {
        'use strict';

        return function (target) {
            var mixin = {
                /**
                 * @param {Function} original
                 * @param {Object} ratesData
                 */
                resolveShippingRates: function (original, ratesData) {
                    if (!ratesData || ratesData.length === 0) {
                        selectShippingMethodAction(null);

                        return;
                    }

                    if (ratesData.length === 1) {
                        //set shipping rate if we have only one available shipping rate
                        selectShippingMethodAction(ratesData[0]);

                        return;
                    }
                    var selectedShippingRate = checkoutData.getSelectedShippingRate(),
                        availableRate = false;

                    if (quote.shippingMethod()) {
                        availableRate = _.find(ratesData, function (rate) {
                            return rate['carrier_code'] == quote.shippingMethod()['carrier_code'] && //eslint-disable-line
                                rate['method_code'] == quote.shippingMethod()['method_code']; //eslint-disable-line eqeqeq
                        });
                    }

                    if (!availableRate && selectedShippingRate) {
                        availableRate = _.find(ratesData, function (rate) {
                            return rate['carrier_code'] + '_' + rate['method_code'] === selectedShippingRate;
                        });
                    }

                    if (!availableRate && window.checkoutConfig.selectedShippingMethod) {
                        availableRate = _.find(ratesData, function (rate) {
                            return rate['carrier_code'] + '_' + rate['method_code'] === window.checkoutConfig.selectedShippingMethod;
                        });
                    }

                    if (!availableRate) {
                        var provider = registry.get('checkoutProvider');
                        if (provider && provider.defaultShippingMethod) {
                            availableRate = _.find(ratesData, function (rate) {
                                return rate['carrier_code'] + '_' + rate['method_code'] === provider.defaultShippingMethod;
                            });
                        }
                    }

                    //preselect first rate
                    if (availableRate) {
                        selectShippingMethodAction(availableRate);
                    }
                },

                /**
                 * Resolve payment method. Used local storage
                 * @param {Function} original
                 */
                resolvePaymentMethod: function (original) {
                    original();
                    if (quote.paymentMethod()) {
                        return;
                    }
                    var paymentMethod = checkoutData.getSelectedPaymentMethod();
                    if (vaultResolver.isSavedVaultPayment(paymentMethod) && vaultResolver.resolve(paymentMethod)) {
                        return;
                    }
                    var provider = registry.get('checkoutProvider');
                    if (provider && provider.defaultPaymentMethod) {
                        var availablePaymentMethods = paymentService.getAvailablePaymentMethods();
                        availablePaymentMethods.some(function (payment) {
                            if (payment.method == provider.defaultPaymentMethod) {
                                selectPaymentMethodAction(payment);
                                return true;
                            }
                        });
                    }
                }
            };

            wrapper._extend(target, mixin);
            return target;
        };
    }
);