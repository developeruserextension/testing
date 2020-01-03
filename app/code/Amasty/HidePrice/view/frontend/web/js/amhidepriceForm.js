define([
    'jquery',
    'uiClass',
    'amasty-fancyambox'
], function ($, Class, fancyambox) {
    var amHidePriceForm = Class.extend({
        products: {},
        customer: null,

        initialize: function (options) {
        },

        addProduct: function (product) {
            this.products[product['id']] = product['name'];
            this.customer = product['customer'];
            this.url = product['url'];
            this._createButtonObserver(product['id']);
        },

        _createButtonObserver: function (id) {
            var self = this;
            $('[data-amhide="AmastyHidePricePopup"][data-product-id="' + id + '"]:not(.observed)').click(function(){
                self._showPopup(this);
            }).addClass(
                'observed'
            );
            $(document).on('click', '[data-amhide-js="close-popup"]',function () {
                $.fancyambox.close();
            });
        },

        _showPopup: function (element) {
            var id = $(element).attr('data-product-id'),
                form = $('.amform-form.amhideprice-form');
            if (form.length == 0) {
                form = this._getFilledForm(id);
            } else {
                if (form.find('[name="hide_product_id"]').length === 0) {
                    form.append(
                        $('<input>', {
                            'name': 'hide_product_id',
                            'type': 'hidden',
                            'value': id
                        })
                    );
                }
            }
            $.fancyambox.open(form, {
                touch: false
            });
        },

        _getFilledForm: function (id) {
            var form = $('#amhideprice-form');
            var product = this.products[id];

            var productName = form.find('.product-name');
            if (productName && product) {
                productName.html(product);
            }

            var productId = form.find('[name="product_id"]');
            if (productId) {
                productId.val(id);
            }

            var name = form.find('[name="name"]');
            if (name && this.customer.name) {
                name.val(this.customer.name);
            }

            var phone = form.find('[name="phone"]');
            if (phone && this.customer.phone) {
                phone.val(this.customer.phone);
            }

            var email = form.find('[name="email"]');
            if (email && this.customer.email) {
                email.val(this.customer.email);
            }

            var self = this;
            form.on('submit', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                e.stopPropagation();
                var validator = $(this).validation({ radioCheckboxClosest: '.nested'});
                if (validator.valid()) {
                    self.submitForm($(this));
                }
                return false;
            });

            return form;
        },

        submitForm: function (form) {
            form = $(form);
            var self = this;

            var data = form.serialize();
            this._clearForm(form);
            $.ajax({
                url: self.url,
                data: data,
                type: 'post',
                dataType: 'json',
                beforeSend: function() {
                    $('.fancyambox-container .amhideprice-form').remove();
                    $.fancyambox.getInstance().showLoading();
                },
                success: function(response) {
                    $.fancyambox.getInstance().hideLoading();
                    var result = $('<div/>',
                        {class: 'message'}
                    );
                    var html = $('<div/>');
                    if (response.success) {
                        html.html(response.success).appendTo(result);
                        result.addClass('success am-hide-message');
                    }

                    if (response.error) {
                        html.html(response.error).appendTo(result);
                        result.addClass('error am-hide-message');
                    }

                    $.fancyambox.close();
                    $.fancyambox.open(result);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $.fancyambox.getInstance().hideLoading();
                    $.fancyambox.close();
                    $.fancyambox.open(errorThrown);
                }
            });
        },

        _clearForm: function(form) {
            var key = form.find('[name="form_key"]');
            var value = key.val();
            $(form)[0].reset();
            key.val(value);
        }
    });

    window.amHidePriceForm = amHidePriceForm();

    return window.amHidePriceForm;
});
