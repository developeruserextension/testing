define([
    'Magento_Ui/js/form/element/abstract',
    'jquery',
    'uiRegistry',
    'Magento_Ui/js/lib/spinner',
    'mage/translate'
], function (Abstract, $, registry, loader, __) {
    'use strict';

    var newLinksCounter = 1000000;

    return Abstract.extend({
        validate: function () {
            var validateStatus = this._super();
            if (this.hasChanged() && this.value() !== '') {
                if (validateStatus.valid) {
                    var formLoader = loader.get(this.ns + '.' + this.ns),
                        self = this,
                        validateUrl = $.Deferred();
                    formLoader.show();

                    $.ajax({
                        url: this.validationUrl,
                        data: {'url': this.value()},
                        dataType: 'json',
                        success: function (result) {
                            formLoader.hide();
                            if (result.status === 'success') {

                                self.source.set(self.parentScope + '.linkdata', {
                                    file_id: newLinksCounter,
                                    show_file_id: __('New Link'),
                                    filename: result.file.filename,
                                    icon_src: result.file.previewUrl,
                                    is_visible: "1",
                                    extension: result.file.file_extension,
                                    label: result.file.filename,
                                    include_in_order: "0",
                                    customer_groups: "",
                                    link: self.value()
                                });
                                validateUrl.resolve({
                                    valid: true,
                                    target: self
                                });
                                newLinksCounter++;
                            } else {
                                self.error(result.message);
                                self.bubble('error', result.message);
                                self.source.set('params.invalid', true);
                                validateUrl.resolve({
                                    valid: false,
                                    target: self
                                });
                            }
                        },
                        complete: function () {
                            formLoader.hide();
                            validateUrl.resolve({
                                valid: false,
                                target: self
                            });
                        }
                    });

                    return validateUrl.promise();
                }
            }

            return validateStatus;
        }
    });
});
