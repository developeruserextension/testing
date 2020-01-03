define([
    'Magento_Ui/js/form/element/file-uploader',
    'underscore',
    'uiRegistry',
    'jquery',
    'mage/translate',
    'prototype'
], function (fileUploader, _, registry, $, __) {
    return fileUploader.extend({
        newFilesCounter: 500000,
        getFilePreviewType: function (file) {
            if (!_.isUndefined(file.previewUrl)) {
                return 'image';
            }

            return this._super();
        },
        getFileLink: function(file) {
            return file.url;
        },
        getFilePreview: function (file) {
            if (!_.isUndefined(file.previewUrl)) {
                return file.previewUrl;
            }

            return file.url;
        },
        addFile: function (file) {
            registry.async('index = files')(function (filesContainer) {
                var data = _.clone(filesContainer.cacheGridData);

                data[data.length] = {
                    file_id: this.newFilesCounter,
                    show_file_id: __('New File'),
                    filename: file.filename,
                    icon_src: file.previewUrl,
                    is_visible: "1",
                    extension: file.file_extension,
                    label: file.filename,
                    include_in_order: "0",
                    customer_groups: "",
                    file: file.file
                };

                filesContainer.insertData(data);
                this.newFilesCounter += 1;
            }.bind(this));

            this._super();
        },
        open: function () {
            $('#' + this.uid).click();
        }
    })
});
