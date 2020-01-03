
define([
    'uiRegistry'
], function (registry) {
    'use strict';

    return {
        /**
         * Validate checkout shipping step
         *
         * @returns {Boolean}
         */
        validate: function () {
            var shipping = registry.get('checkout.steps.shipping-step.shippingAddress'),
                result;

            shipping.allowedDynamicalSave = false;
            result = shipping.validateShippingInformation();
            shipping.allowedDynamicalSave = true;

            return result;
        }
    };
});
