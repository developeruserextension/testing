define(
    [
        'mage/utils/wrapper'
    ],
    function (wrapper) {
        'use strict';

        return function (target) {
            /**
             * Override for avoid full screen on dynamic save
             */
            target.startLoader =  wrapper.wrapSuper(target.startLoader, function() {
                if (!window.loaderIsNotAllowed) {
                    this._super();
                }
            });

            return target;
        };
    }
);

