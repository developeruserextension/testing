/*global define*/
define(
    [
        'jquery',
        'mage/utils/wrapper'
    ],
    function (
        $,
        wrapper
    ) {
        'use strict';
        return function (target) {
            var mixin = {
                /**
                 * @return {*}
                 */
                postcodeValidation: function (original) {
                    original();

                    return true;
                }
            };

            wrapper._extend(target, mixin);
            return target;
        };
    }
);
