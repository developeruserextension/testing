define([
    'jquery',
    'mage/utils/wrapper',
    'Amasty_Checkout/js/model/shipping-registry',
    'Magento_Checkout/js/model/totals'
], function ($, wrapper, shippingRegistry, totals) {
    'use strict';

    return function (setShippingInformationAction) {
        return wrapper.wrap(setShippingInformationAction, function (original) {
            shippingRegistry.register();
            totals.isLoading(true);
            return original().always(function(){
                totals.isLoading(false);
            });
        });
    };
});
