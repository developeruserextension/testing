define(
    [
        'jquery',
        'underscore',
        'Amasty_Checkout/js/action/save-password',
        'Magento_Customer/js/model/customer'
    ],
    function ($, _, savePassword, customer) {
        'use strict';

        var startPlaceOrder = function (selector) {
            if (selector) {
                $(selector).click();
            } else {
                var toolBar = $('.payment-method._active .actions-toolbar');
                if (toolBar.length > 1) {
                    _.each(toolBar, function (element) {
                        if (element.style.display !== 'none') {
                            toolBar = $(element);
                            return; //break
                        }
                    })
                }
                toolBar.find('.action.primary').click();
            }
        };

        return function (selector) {
            if (!customer.isLoggedIn() && window.checkoutConfig.quoteData.additional_options.create_account === '2') {
                savePassword().always(function () {
                    startPlaceOrder(selector);
                });
            } else {
                startPlaceOrder(selector);
            }
        };
    }
);
