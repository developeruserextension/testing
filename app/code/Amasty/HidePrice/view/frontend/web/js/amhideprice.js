define([
    "jquery",
    "jquery/ui"
], function ($) {
    $.widget('mage.amhideprice', {
        options: {},
        _create: function () {
            if (this.element) {
                var parent = this.element.parents(this.options['parent']);
                if (!parent) {
                    return;
                }

                var button = parent.find(this.options['button']),
                    addButton = parent.find('.actions-primary form button');
                if (button && button[0]
                    && (this.options['hide_addtocart'] === '1' || this.options['hide_addtocart'] === '2')
                ) {
                    button[0].outerHTML = this.options['html'];
                    eval($('<div>').html(this.options['html']).find('script').html());
                }

                if (addButton.length > 0 && this.options['hide_addtocart'] === '1') {
                    addButton.remove();
                }

                if (this.options['hide_addtocart'] === '1') {
                    $('[data-role="all-tocart"]').remove();
                }

                if (this.options['hide_compare'] === '1') {
                    parent.find('a.tocompare').remove();
                }

                if (this.options['hide_wishlist'] === '1') {
                    parent.find('a.towishlist').remove();
                }
            }
        }
    });

    return $.mage.amhideprice;
});
