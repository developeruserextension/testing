define([
    'jquery',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer',
    'Magento_Catalog/js/price-utils',
    'Amasty_StoreCredit/js/action/apply-store-credit',
    'Amasty_StoreCredit/js/action/cancel-store-credit',
    'underscore'
], function ($, Component, quote, customer, priceUtils, applyStoreCredit, cancelStoreCredit, _) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Amasty_StoreCredit/checkout/payment/storecredit',
            isVisible: window.checkoutConfig.amastyStoreCredit.isVisible,
            available: window.checkoutConfig.amastyStoreCredit.amStoreCreditAmountAvailable,
            amount: window.checkoutConfig.amastyStoreCredit.amStoreCreditAmount,
            appliedAmount: 0,
            isApplied: !!window.checkoutConfig.amastyStoreCredit.amStoreCreditUsed
        },

        initObservable: function () {
            if (this.isApplied) {
                this.available = this.available - this.amount;
            }

            this.appliedAmount = parseFloat(this.amount);
            var priceFormat = _.clone(quote.getPriceFormat());
            priceFormat.pattern = '%s';
            this.amount = priceUtils.formatPrice(this.amount,  priceFormat, false);

            this._super();
            this.observe(['isVisible', 'available', 'isApplied', 'amount']);

            return this;
        },
        getFormatAmount: function () {
            return priceUtils.formatPrice(this.amount(),  quote.getPriceFormat(), false);
        },
        getStoreCreditLeft: function () {
            return priceUtils.formatPrice(this.available(),  quote.getPriceFormat(), false);
        },
        applyStoreCredit: function () {
            applyStoreCredit(this.amount())
                .done(function (response) {
                    this.available(this.available() - parseFloat(response));
                    this.amount(response);
                    this.appliedAmount = this.amount();
                    this.isApplied(true);
                }.bind(this))
                .fail(function () {

                });
        },
        cancelStoreCredit: function () {
            cancelStoreCredit()
                .done(function (response) {
                    this.available(this.available() + this.appliedAmount);
                    this.amount(response);
                    this.isApplied(false);
                }.bind(this));
        }
    });
});
