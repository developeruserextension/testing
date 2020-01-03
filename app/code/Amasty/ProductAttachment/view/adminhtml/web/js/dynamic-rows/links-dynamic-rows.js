define([
    'Magento_Ui/js/dynamic-rows/dynamic-rows',
    'uiRegistry',
    'underscore'
], function (Dynamicrows, registry, _) {
    return Dynamicrows.extend({
        saveLinks: function () {
            registry.async('index = files')(function (filesContainer) {
                var data = _.clone(filesContainer.cacheGridData);
                _.each(this.recordData(), function (record) {
                    if (!_.isUndefined(record.linkdata)) {
                        data[data.length] = record.linkdata;
                    }
                }.bind(this));
                this.recordData([]);
                this.reload();
                this.showSpinner(false);
                filesContainer.insertData(data);
            }.bind(this));
        }
    });
});