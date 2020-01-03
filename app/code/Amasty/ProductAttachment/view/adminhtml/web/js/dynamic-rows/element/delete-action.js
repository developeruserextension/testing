define(
    [
        "Magento_Ui/js/form/element/abstract",
        "uiRegistry",
        "underscore"
    ],
    function (DeleteAction, registry, _) {
        return DeleteAction.extend({
            defaults: {
                links: {
                    value: false
                }
            },

            initObservable: function () {
                this._super();

                this.checkState();

                return this;
            },
            checkState: function () {
                registry.async(this.provider)(function (provider) {
                    if (provider.get(this.dataScope + '.not_removable')) {
                        this.disabled(true);
                    } else {
                        this.disabled(false);
                    }
                }.bind(this));
            },
            deleteRecord: function (index, id) {
                if (!this.disabled()) {
                    this.source.set('data.attachments.delete.' + id, true);
                    if (!_.isUndefined(this.containers[0].containers[0].mappingSettings)) {
                        this.bubble('deleteRecord', index, id);
                    } else {
                        this.containers[0].containers[0].processingDeleteRecord(index, id);
                    }
                }
            }
        });
    }
);
