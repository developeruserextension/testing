define([
    'ko',
    'underscore',
    'Magento_Ui/js/form/form',
    'Magento_Customer/js/model/customer',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/create-shipping-address',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/action/set-shipping-information',
    'Magento_Ui/js/model/messageList',
    'mage/translate',
    'jquery',
    'uiRegistry'
],
function (
    ko,
    _,
    Component,
    customer,
    addressList,
    quote,
    createShippingAddress,
    selectShippingAddress,
    checkoutData,
    checkoutDataResolver,
    customerData,
    setShippingAddressAction,
    globalMessageList,
    $t,
    $,
    registry
) {
    'use strict';

    var lastSelectedShippingAddress = null,
        newAddressOption = {
            /**
             * Get new address label
             * @returns {String}
             */
            getAddressInline: function () {
                return $t('New Address');
            },
            customerAddressId: null
        },
        countryData = customerData.get('directory-data'),
        addressOptions = addressList().filter(function (address) {
            return address.getType() == 'customer-address'; //eslint-disable-line eqeqeq
        });

    addressOptions.push(newAddressOption);

    return Component.extend({
        defaults: {
            template: 'Amasty_Checkout/shipping-address/shipping-address',

            modules: {
                shippingAddressComponent: '${ $.parentName }'
            }
        },
        currentShippingAddress: quote.shippingAddress,
        addressOptions: addressOptions,
        customerHasAddresses: addressOptions.length > 1,

        /**
         * Init component
         */
        initialize: function () {
            this._super();
            quote.paymentMethod.subscribe(function () {
                checkoutDataResolver.resolveShippingAddress();
            }, this);
        },

        /**
         * @return {exports.initObservable}
         */
        initObservable: function () {
            this._super()
                .observe({
                    selectedAddress: null,
                    isAddressDetailsVisible: quote.shippingAddress() != null,
                    isAddressFormVisible: !customer.isLoggedIn() || addressOptions.length === 1,
                    saveInAddressBook: 1,
                    isAddressListVisible: null,
                    isNewAddressVisible: null
                });

            quote.shippingAddress.subscribe(function (newAddress) {

                if (newAddress != null && newAddress.saveInAddressBook !== undefined) {
                    this.saveInAddressBook(newAddress.saveInAddressBook);
                    this.isNewAddressVisible(true);
                } else {
                    this.saveInAddressBook(1);
                }
                if (!this.selectedAddress() || this.selectedAddress() != newAddressOption) { //eslint-disable-line eqeqeq
                    this.isAddressDetailsVisible(true);
                    this.isAddressListVisible(false);
                }
            }, this);

            return this;
        },

        /**
         * @param {Object} address
         * @return {*}
         */
        addressOptionsText: function (address) {
            return address.getAddressInline();
        },

        /**
         * @return {Boolean}
         */
        useShippingAddress: function () {
            lastSelectedShippingAddress = quote.shippingAddress();
            quote.shippingAddress(null);
            this.isAddressDetailsVisible(false);
            checkoutData.setSelectedShippingAddress(null);

            return true;
        },

        /**
         * Update address action
         */
        updateAddress: function () {
            var addressData, newShippingAddress;

            this.source.set('params.invalid', false);
            if (this.isAddressFormVisible() || this.isAddressListVisible()) {
                if (this.selectedAddress() && this.selectedAddress() != newAddressOption) { //eslint-disable-line eqeqeq
                    selectShippingAddress(this.selectedAddress());
                    this.isAddressFormVisible(false);
                    this.isAddressListVisible(false);
                    this.isNewAddressVisible(false);
                    this.isAddressDetailsVisible(true);
                    checkoutData.setSelectedShippingAddress(this.selectedAddress().getKey());
                } else {
                    this.source.trigger(this.dataScopePrefix + '.data.validate');

                    if (this.source.get(this.dataScopePrefix + '.custom_attributes')) {
                        this.source.trigger(this.dataScopePrefix + '.custom_attributes.data.validate');
                    }

                    if (!this.source.get('params.invalid')) {
                        addressData = this.source.get(this.dataScopePrefix);

                        if (customer.isLoggedIn() && !this.customerHasAddresses) { //eslint-disable-line max-depth
                            this.saveInAddressBook(1);
                        }
                        addressData['save_in_address_book'] = this.saveInAddressBook() ? 1 : 0;
                        newShippingAddress = createShippingAddress(addressData);

                        selectShippingAddress(newShippingAddress);
                        this.isNewAddressVisible(true);
                        this.isAddressFormVisible(false);
                        this.isAddressListVisible(false);
                        this.isAddressDetailsVisible(true);
                        checkoutData.setSelectedShippingAddress(newShippingAddress.getKey());
                        checkoutData.setNewCustomerShippingAddress(addressData);
                    }
                }
            }
        },

        /**
         * Edit address action
         */
        editAddress: function () {
            lastSelectedShippingAddress = quote.shippingAddress();
            if (quote.paymentMethod._latestValue) {
                var paymentPlaceButtonComponent = registry.get('checkout.steps.billing-step.payment.payments-list.' +
                    quote.paymentMethod._latestValue.method);
                paymentPlaceButtonComponent.isPlaceOrderActionAllowed(false);
            }

            this.isAddressListVisible(true);
            this.isAddressDetailsVisible(false);
            if (this.isNewAddressVisible() == true) {
                this.isAddressFormVisible(true);
            }
        },

        /**
         * Cancel address edit action
         */
        cancelAddressEdit: function () {
            this.restoreShippingAddress();

            if (quote.shippingAddress()) {
                this.isAddressDetailsVisible(true);
            }
            this.isAddressFormVisible(false);
            this.isAddressListVisible(false);
            if (this.selectedAddress() && this.selectedAddress() == newAddressOption) { //eslint-disable-line eqeqeq
                this.isNewAddressVisible(true);
            }
        },

        /**
         * Restore shipping address
         */
        restoreShippingAddress: function () {
            if (lastSelectedShippingAddress != null) {
                selectShippingAddress(lastSelectedShippingAddress);
            }
        },

        /**
         * @param {Object} address
         */
        onAddressChange: function (address) {
            this.isAddressFormVisible(address == newAddressOption); //eslint-disable-line eqeqeq
            this.shippingAddressComponent().isNewAddressAdded(true);
        },

        /**
         * @param {Number} countryId
         * @return {*}
         */
        getCountryName: function (countryId) {
            return countryData()[countryId] != undefined ? countryData()[countryId].name : ''; //eslint-disable-line
        }
    });
});
