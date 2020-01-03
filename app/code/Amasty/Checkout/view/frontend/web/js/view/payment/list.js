define([], function () {
    'use strict';

    return function (Component) {
        return Component.extend({
            getGroupTitle: function (newValue) {
                if (newValue().index === 'methodGroup'
                    && window.checkoutConfig.quoteData.block_info.block_payment_method
                ) {
                    return window.checkoutConfig.quoteData.block_info.block_payment_method['value'];
                }

                return this._super(newValue);
            }
        });
    }
});
