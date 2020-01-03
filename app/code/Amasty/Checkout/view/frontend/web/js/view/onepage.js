define(
    [
        'jquery',
        'underscore',
        'uiComponent',
        'ko',
        'uiRegistry',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Amasty_Checkout/js/model/payment-validators/shipping-validator',
        'Magento_Checkout/js/action/select-billing-address',
        'Magento_Checkout/js/view/billing-address'
    ],
    function (
        $,
        _,
        Component,
        ko,
        registry,
        quote,
        paymentValidatorRegistry,
        shippingValidator,
        selectBillingAddress,
        billingAddress
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                modules: {
                    shippingComponent: 'checkout.steps.shipping-step.shippingAddress'
                },
                mappingBlockName: {
                    block_shipping_address: 'checkout.steps.shipping-step.shippingAddress',
                    block_shipping_method: 'checkout.steps.shipping-step.shippingAddress',
                    block_delivery: 'checkout.steps.shipping-step.amcheckout-delivery-date',
                    block_payment_method: 'billing-step',
                    block_order_summary: 'sidebar'
                },
                orderedBlocks: {}
            },

            /** @inheritdoc */
            initialize: function () {
                this._super();
                var blockInfo = window.checkoutConfig.quoteData.block_info;

                $.each(blockInfo, function (key, item) {
                    var sortOrder = 0;

                    if (item.hasOwnProperty('sort_order')) {
                        sortOrder = item.sort_order;
                    }

                    while (this.orderedBlocks.hasOwnProperty(sortOrder)) {
                        sortOrder++;
                    }

                    this.orderedBlocks[sortOrder] = {
                        blockCode: this.mappingBlockName[key],
                        blockKey: key
                    };
                }.bind(this));
            },

            getSortedBlock: function(index) {
                var orderedBlock = this.orderedBlocks[index];

                if (orderedBlock.blockCode === 'billing-step') {
                    return this.getChild('steps').getChild('billing-step');
                } else if (orderedBlock.blockCode === 'sidebar') {
                    return this.getChild('sidebar');
                }

                var requestComponent = this.requestComponent(orderedBlock.blockCode);

                if (orderedBlock.blockKey === 'block_shipping_address' && requestComponent()) {
                    requestComponent().template = 'Amasty_Checkout/onepage/shipping/address';
                } else if (orderedBlock.blockKey === 'block_shipping_method' && requestComponent()) {
                    requestComponent().template = 'Amasty_Checkout/onepage/shipping/methods';
                }

                return requestComponent;
            },

            getBlockIndex: function (index) {
                if (this.orderedBlocks[index].blockCode === 'sidebar') {
                    return '-summary';
                }
            },

            initObservable: function () {
                this._super();

                quote.shippingAddress.subscribe(this.shippingAddressObserver.bind(this));

                billingAddress().isAddressSameAsShipping.subscribe(function (value) {
                    this.isAddressSameAsShipping(value);
                }.bind(this));

                if (!quote.isVirtual()) {
                    paymentValidatorRegistry.registerValidator(shippingValidator);
                }
                registry.get('checkout.steps.billing-step.payment', function (component) {
                    //initialize payment information
                    component.isVisible(true);
                    component.navigate();
                }.bind(this));

                return this;
            },

            isAddressSameAsShipping: function (value) {
                var shippingComponent = this.shippingComponent;
                if (shippingComponent && shippingComponent()) {
                    shippingComponent().allowedDynamicalSave = !!value;
                }
            },

            shippingAddressObserver: function (address) {
                if (!address) {
                    return;
                }
                if (_.isNull(address.street) || _.isUndefined(address.street)) {
                    // fix: some payments (paypal) takes street.0 without checking
                    address.street = [];
                }

                // fix default "My billing and shipping address are the same" checkbox behaviour
                var methodComponent = registry.get('checkout.steps.billing-step.payment.afterMethods.billing-address-form');
                if (methodComponent && !methodComponent.isAddressSameAsShipping()) {
                    return;
                }
                var billAddrOnShippSectionComponent = registry.get('checkout.steps.shipping-step.shippingAddress.billing-address-form');
                if (billAddrOnShippSectionComponent && !billAddrOnShippSectionComponent.isAddressSameAsShipping()) {
                    return;
                }
                var paymentMethod = quote.paymentMethod();
                if (!paymentMethod || methodComponent) {
                    selectBillingAddress(address);
                } else {
                    methodComponent = registry.get('checkout.steps.billing-step.payment.payments-list.'+quote.paymentMethod().method+'-form');
                    if (!methodComponent || methodComponent.isAddressSameAsShipping()) {
                        selectBillingAddress(address);
                    }
                }
            },

            /**
             * Used in templates
             *
             * @param {string} name
             * @returns {observable}
             */
            requestComponent: function (name) {
                var observable = ko.observable();

                registry.get(name, function (summary) {
                    observable(summary);
                }.bind(this));

                return observable;
            }
        });
    }
);
