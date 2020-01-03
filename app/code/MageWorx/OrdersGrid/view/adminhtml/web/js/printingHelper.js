/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'underscore',
    'uiRegistry',
    'mageUtils',
    'uiComponent',
    'mage/url',
    'jquery',
    'jquery/ui'
], function (_, registry, utils, uiComponent, url, $) {
    'use strict';

    return uiComponent.extend({

        /**
         * Initializes model instance.
         *
         * @returns {Element} Chainable.
         */
        initialize: function () {
            this._super();
            try {
                this.printInvoices();
            } catch (e) {
                console.log(e);
            }

            return this;
        },

        printInvoices: function () {
            var self = this;
            if (window.location.pathname.search(/\/print_invoices\/1\//i) !== -1) {
                $.ajax(this.print_url, {
                    data: {'check': true},
                    method: 'POST',
                    success: function (data) {
                        if (typeof data.success != 'undefined' && data.success) {
                            $('<iframe></iframe>', {
                                'id': 'mw-download-frame',
                                'style': 'display:none;'
                            }).appendTo($('body'));
                            document.getElementById('mw-download-frame').src = self.print_url;
                        }
                    },
                    error: function (error) {
                        console.log('Error:');
                        console.log(error);
                    }
                });
            }
        }
    });
});
