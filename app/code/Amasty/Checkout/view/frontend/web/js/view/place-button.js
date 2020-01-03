define(
    [
        'jquery',
        'uiComponent',
        'uiRegistry',
        'Magento_Checkout/js/model/quote',
        'Amasty_Checkout/js/action/start-place-order',
        'Amasty_Checkout/js/model/amalert',
        'mage/translate',
        'Amasty_Checkout/js/action/focus-first-error',
        'Amasty_Checkout/js/model/payment-validators/login-form-validator'
    ],
    function (
        $,
        Component,
        registry,
        quote,
        startPlaceOrderAction,
        alert,
        $t,
        focusFirstError,
        loginFormValidator
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Amasty_Checkout/onepage/place-order',
                defaultLabel: $t('Place Order'),
                modules: {
                    createAcc: 'checkout.sidebar.additional.register'
                }
            },
            initObservable: function () {
                this._super().observe({
                    label: this.defaultLabel,
                    isPlaceOrderActionAllowed: false
                });
                quote.paymentMethod.subscribe(this.updateLabel.bind(this));
                if (quote.paymentMethod()) {
                    this.updateLabel(quote.paymentMethod());
                }

                return this;
            },

            updateLabel: function (payment) {
                this.isPlaceOrderActionAllowed(!!payment);
                if (payment) {
                    // selected payment is don't have class `_active` yet
                    var button = jQuery('#' + payment.method).parents('.payment-method')
                        .find('.actions-toolbar:not([style*="display: none"]) .action.primary');
                    if (button.length) {
                        if (button.text() && button.text().trim() !== "") {
                            this.label(button.text());
                            return;
                        }
                        if (button.attr('title')) {
                            this.label(button.attr('title'));
                            return;
                        }
                    }
                }

                this.label(this.defaultLabel);
            },

            placeOrder: function () {
                var errorMessage = '';

                if (!quote.paymentMethod()) {
                    errorMessage = $.mage.__('No payment method selected');
                    alert({content: errorMessage});
                    return;
                }

                if (!quote.shippingMethod() && !quote.isVirtual()) {
                    errorMessage = $.mage.__('No shipping method selected');
                    alert({content: errorMessage});
                    return;
                }

                var validateBillingAddress = this.updateBillingAddress(quote);
                var validateShippingAddress = this.updateShippingAddress(quote);

                if (loginFormValidator.validate() && !validateBillingAddress && !validateShippingAddress) {
                    startPlaceOrderAction();
                } else {
                    focusFirstError();
                }
            },

            updateBillingAddress: function (quote) {
                var billingAddress = null;

                if (window.checkoutConfig.displayBillingOnPaymentMethod) {
                    billingAddress =
                        registry.get('checkout.steps.billing-step.payment.payments-list.'
                            + quote.paymentMethod().method
                            + '-form');
                } else {
                    billingAddress =
                        registry.get("checkout.steps.billing-step.payment.afterMethods.billing-address-form");

                    if (!billingAddress) {
                        billingAddress =
                            registry.get("checkout.steps.shipping-step.shippingAddress.billing-address-form");
                    }
                }

                if (!billingAddress || billingAddress.isAddressSameAsShipping() || billingAddress.isAddressDetailsVisible()) {
                    return false;
                } else {
                    billingAddress.updateAddress();

                    return billingAddress.source.get('params.invalid');
                }
            },

            updateShippingAddress: function (quote) {
                var shippingAddress = registry.get("checkout.steps.shipping-step.shippingAddress.address-list");

                if (shippingAddress && typeof shippingAddress.updateAddress !== "undefined") {
                    shippingAddress.updateAddress();

                    return shippingAddress.source.get('params.invalid');
                }

                return false;
            },
        });
    }
);