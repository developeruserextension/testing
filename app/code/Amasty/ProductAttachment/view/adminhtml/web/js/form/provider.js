define([
    'Magento_Ui/js/form/provider'
], function (Provider) {
    return Provider.extend({
        save: function (options) {
            var data = this.get('data');
            this.client.save({'filesData': JSON.stringify(data)}, options);

            return this;
        }
    });
});
