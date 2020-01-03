define(
    [
        'underscore',
        'mage/translate',
        'mage/utils/wrapper',
        'Amasty_Checkout/js/action/focus-first-error',
        'Amasty_Checkout/js/action/start-place-order',
        'Magento_Checkout/js/action/set-shipping-information',
        'Amasty_Checkout/js/model/amalert',
        'Magento_Checkout/js/model/quote',
        'Amasty_Checkout/js/model/payment-validators/login-form-validator',
        'Magento_Checkout/js/model/payment/method-list'
    ],
    function (_, $t, wrapper, focusFirstError, startOrderPlace, saveShipping, amalert, quote, loginFormValidator) {
        'use strict';

        return function (target) {
            var isShippingSaveAllowed = !quote.isVirtual();
            /**
             * Focus first error after validation
             * and save shipping information
             */
            target.validate = wrapper.wrapSuper(target.validate, function () {
                var currentPaymentMethod = quote.paymentMethod();

                if (!loginFormValidator.validate()) {
                    focusFirstError();

                    return false;
                }

                var result = this._super();
                if (!result) {
                    isShippingSaveAllowed = !quote.isVirtual();
                    focusFirstError();
                } else if (isShippingSaveAllowed) {
                    var canContinue = true;
                    isShippingSaveAllowed = false;

                    var paymentSubscribe = quote.paymentMethod.subscribe(
                        function (newPaymentMethod) {
                            //alert if during shipping save payment method was changed
                            if (canContinue && !_.isEqual(currentPaymentMethod.method, newPaymentMethod.method)) {
                                amalert({content: $t('Selected payment method is not available anymore')});
                                console.info('For Admin: You should change System Configuration Option in Admin Area "Display Billing Address On" to "Payment Page" for update payment method list by billing address');
                                canContinue = false;
                            }
                        }
                    );
                    // save shipping information because of extensions which can mixin on shipping save
                    saveShipping().always(function () {
                        paymentSubscribe.dispose();
                        if (canContinue) {
                            startOrderPlace();
                        } else {
                            isShippingSaveAllowed = !quote.isVirtual();
                        }
                    });

                    return false;
                }

                return result;
            });

            return target;
        };
    }
);
