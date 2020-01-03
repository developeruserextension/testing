define([
    'jquery',
    'ko',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Checkout/js/model/error-processor',
    'mage/storage',
    'Magento_Ui/js/model/messageList',
    'mage/translate',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/totals'
], function (
    $,
    ko,
    quote,
    urlBuilder,
    errorProcessor,
    storage,
    messageList,
    __,
    fullScreenLoader,
    getPaymentInformationAction,
    totals
) {
    'use strict';

    return function () {
        var result = $.Deferred();

        var message = __('Your store credit was successfully canceled');

        messageList.clear();
        fullScreenLoader.startLoader();

        storage.post(
            urlBuilder.createUrl('/carts/mine/amstorecredit/cancel', {})
        ).done(function (response) {
            if (response) {
                totals.isLoading(true);
                getPaymentInformationAction().done(function () {
                    totals.isLoading(false);
                });

                messageList.addSuccessMessage({'message': message});
                result.resolve(response);
            }
        }).fail(function (response) {
            totals.isLoading(false);
            errorProcessor.process(response);
            result.fail();
        }).always(function () {
            fullScreenLoader.stopLoader();
        });

        return result.promise();
    };
});
