define([
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'Amasty_Checkout/js/model/vault-payment-resolver'
],function (wrapper, quote, vaultResolver) {
    'use strict';
    return function (target) {
        /**
         * Fix unselection of saved vault payment method
         */
        target.setPaymentMethods = wrapper.wrapSuper(target.setPaymentMethods, function (methods) {
            if (methods && quote.paymentMethod()) {
                var selectedMethod = quote.paymentMethod().method;
                if (vaultResolver.isVaultMethodAvailable(selectedMethod, methods)) {
                    methods.push({
                        method: selectedMethod
                    });
                }
            }

            this._super(methods);
        });

        return target;
    };
});
