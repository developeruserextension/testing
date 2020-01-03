define(
    [
        'Amasty_Checkout/js/model/resource-url-manager',
        'Magento_Checkout/js/model/totals',
        'Magento_Checkout/js/model/quote',
        'mage/storage',
        'Magento_Checkout/js/model/error-processor',
        'uiRegistry',
        'Magento_Checkout/js/model/shipping-rate-registry',
        'Magento_Checkout/js/model/shipping-rate-processor/new-address',
        'Magento_Checkout/js/model/shipping-rate-processor/customer-address',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Customer/js/customer-data'
    ],
    function (
        resourceUrlManager,
        totals,
        quote,
        storage,
        errorProcessor,
        registry,
        rateRegistry,
        defaultProcessor,
        customerAddressProcessor,
        paymentService,
        methodConverter,
        customerData
    ) {
        "use strict";

        var isJSON = function (data) {
            var dataIsJson = true;

            try {
                var parsedData = JSON.parse(data);
            } catch (e) {
                dataIsJson = false;
            }

            return dataIsJson;
        };

        return function (itemId, formData) {
            if (totals.isLoading())
                return;

            totals.isLoading(true);
            var serviceUrl = resourceUrlManager.getUrlForUpdateItem(quote);
            var shipppingAddress = quote.shippingAddress();

            //Fix for magento 2.2.2
            if (shipppingAddress.extensionAttributes
                && shipppingAddress.extensionAttributes.checkoutFields
                && Object.keys(shipppingAddress.extensionAttributes.checkoutFields).length === 0
            ) {
                shipppingAddress.extensionAttributes.checkoutFields = [];
            }

            storage.post(
                serviceUrl, JSON.stringify({
                    itemId: itemId,
                    formData: formData,
                    address: shipppingAddress
                }), false
            ).done(
                function (result) {
                    if (!result) {
                        window.location.reload();
                    }

                    if (result.image_data && isJSON(result.image_data)) {
                        registry.get('checkout.sidebar.summary.cart_items.details.thumbnail').imageData
                            = JSON.parse(result.image_data);
                    }

                    if (result.options_data && isJSON(result.options_data)) {
                        var options = JSON.parse(result.options_data);

                        result.totals.items.forEach(function (item) {
                            item.amcheckout = options[item.item_id];
                        });
                    }

                    rateRegistry.set(quote.shippingAddress().getCacheKey(), null);
                    var type = quote.shippingAddress().getType();

                    if (type === 'customer-address') {
                        customerAddressProcessor.getRates(quote.shippingAddress());
                    } else {
                        defaultProcessor.getRates(quote.shippingAddress());
                    }

                    rateRegistry.set(quote.shippingAddress().getKey(), result.shipping);
                    quote.setTotals(result.totals);

                    paymentService.setPaymentMethods(methodConverter(result.payment));
                    customerData.reload(['cart']);
                }
            ).fail(
                function (response) {
                    errorProcessor.process(response);
                }
            ).always(
                function () {
                    totals.isLoading(false);
                }
            );
        }
    }
);
