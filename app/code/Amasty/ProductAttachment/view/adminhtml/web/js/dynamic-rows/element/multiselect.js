define([
    'Magento_Ui/js/form/element/multiselect',
    'uiRegistry',
    'underscore'
], function (Multiselect, registry, _) {

    return Multiselect.extend({
        defaults: {
            serviceTemplate: 'ui/form/element/helper/service'
        },
        realHasService: true,

        initialize: function() {
            this._super();
            this.size = 4;
        },

        initObservable: function () {
            this._super();

            this.observe({realHasService : this.realHasService});
            registry.async(this.provider)(function (provider) {
                var useDefaults = provider.get(this.dataScope + '_use_defaults');
                if (typeof useDefaults !== 'undefined' && useDefaults !== '') {
                    if (registry.get(this.provider).get(this.dataScope + '_use_defaults')) {
                        this.disabled(true);
                    }
                } else {
                    this.realHasService(false);
                }
            }.bind(this));

            return this;
        },
        checkState: function () {
            registry.async(this.provider)(function (provider) {
                var useDefaults = provider.get(this.dataScope + '_use_defaults');
                if (typeof useDefaults !== 'undefined' && useDefaults !== '') {
                    if (registry.get(this.provider).get(this.dataScope + '_use_defaults')) {
                        if (!this.disabled()) {
                            this.isUseDefault(true);
                        }
                    } else {
                        if (this.disabled()) {
                            this.isUseDefault(false);
                        }
                    }
                    this.realHasService(true);
                } else {
                    this.realHasService(false);
                }
            }.bind(this));
        },
        toggleUseDefault: function (state) {
            this._super();
            registry.async(this.provider)(function (provider) {
                var useDefaults = provider.get(this.dataScope + '_use_defaults');
                if (typeof useDefaults !== 'undefined' && useDefaults !== '') {
                    provider.set(this.dataScope + '_use_defaults', state);
                }
            }.bind(this));
        },
        validate: function () {
            if (!this.disabled()) {
                this.source.set(this.dataScope + '_output', this.value().join(','));
            }
        }
    });
});
