//TODO rewrite
define([
    'jquery',
    'uiRegistry',
    'jquery/ui',
    'prototype',
    'form',
    'validation'
], function (jQuery) {
    'use strict';

    return function (config) {
        var attributeOption = {
            table: $('attribute-options-table'),
            rendered: 0,
            template: _.template(config.template),
            isReadOnly: config.isReadOnly,
            add: function (data, render) {
                var element;

                element = this.template({
                    data: data
                });

                this.elements += element;

                if (render) {
                    this.render();
                }
            },
            remove: function (event) {
                $(Event.findElement(event, 'tr')).remove();
            },
            bindRemoveButtons: function () {
                //update
                jQuery('#swatch-visual-options-panel').on('click', '.delete-option', this.remove.bind(this));
            },
            render: function () {
                Element.insert($$('[data-role=options-container]')[0], this.elements);
                this.elements = '';
            },
            renderWithDelay: function (data, from, step, delay) {
                var arrayLength = data.length,
                    len;

                for (len = from + step; from < len && from < arrayLength; from++) {
                    this.add(data[from]);
                }
                this.render();

                if (from === arrayLength) {
                    this.rendered = 1;
                    jQuery('body').trigger('processStop');

                    return true;
                }
                setTimeout(this.renderWithDelay.bind(this, data, from, step, delay), delay);
            }
        };

        function updateValue()
        {
            var files = {};
            jQuery('#manage-options-panel').find('[data-file-id]').each(function(){
                files[jQuery(this).attr('data-file-id')] = jQuery(this).val();
            });
            jQuery('#' + config.uniqId + 'value').val(JSON.stringify(files).replace(/\"/g, '|'));
        }

        jQuery(document).on('click', '#addSelectedFiles', function () {
            jQuery('#' + config.uniqId).find('[name=in_files]:checked').each(function() {
                attributeOption.add({
                    'file_id': jQuery(this).val(),
                    'filename': jQuery(this).closest('tr').find('td')[2].innerText,
                    'label': jQuery(this).closest('tr').find('td')[3].innerText,
                    'order': ''
                }, true);
            });
            updateValue();
            jQuery(this).prev().click();
        });

        $('manage-options-panel').on('click', '.action-delete', function (event) {
            attributeOption.remove(event);
            updateValue();
        });

        jQuery('#manage-options-panel').on('render', function () {
            if (attributeOption.rendered) {
                return false;
            }
            jQuery('body').trigger('processStart');
            attributeOption.renderWithDelay(config.filesData, 0, 100, 300);
            attributeOption.bindRemoveButtons();
        });

        if (config.isSortable) {
            jQuery(function ($) {
                $('[data-role=options-container]').sortable({
                    distance: 8,
                    tolerance: 'pointer',
                    cancel: 'input, button',
                    axis: 'y',
                    update: function () {
                        $('[data-role=options-container] [data-role=order]').each(function (index, element) {
                            $(element).val(index + 1);
                        });
                        updateValue();
                    }
                });
            });
        }
        jQuery(function() {
            jQuery('#manage-options-panel').trigger('render');
        });
    };
});
