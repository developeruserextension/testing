define([
    'underscore',
    'Magento_Ui/js/form/form',
    'uiRegistry'
], function (_, Form, registry) {
    return Form.extend({
        setAdditionalData: function () {
            this._super();
            var generalDataFieldSet = registry.get(this.name + '.general');
            if (typeof generalDataFieldSet !== 'undefined') {
                _.each(generalDataFieldSet.elems(), function (elem) {
                    if (_.isFunction(elem.disabled) && elem.disabled()) {
                        if (elem.dataType === 'multiselect') {
                            this.source.set(elem.dataScope + '_output', null);
                        } else {
                            this.source.set(elem.dataScope, null);
                        }
                    } else if (_.isFunction(elem.elems) && elem.elems()) {
                        _.each(elem.elems(), function (elem) {
                            if (_.isFunction(elem.disabled) && elem.disabled()) {
                                this.source.set(elem.dataScope, null);
                            }
                        }, this);
                    }
                }, this);
            }

            return this;
        }
    });
});
