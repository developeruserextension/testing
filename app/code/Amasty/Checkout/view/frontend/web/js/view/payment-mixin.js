define([], function () {
    'use strict';

    return function (Component) {
        return Component.extend({
            hasShippingMethod: function () {
                return true;
            }
        });
    }
});
