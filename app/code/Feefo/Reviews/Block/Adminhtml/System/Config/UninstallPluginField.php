<?php

namespace Feefo\Reviews\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Widget\Button as ButtonWidget;
use Magento\Config\Block\System\Config\Form\Field as FormField;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class UninstallPluginField
 */
class UninstallPluginField extends FormField
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'Feefo_Reviews::system/config/uninstall_plugin_field.phtml';

    /**
     * Unset some non-related element parameters
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($element);
    }

    /**
     * Get action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->_urlBuilder->getUrl('feefo/system/uninstallPlugin');
    }

    /**
     * Get the button and scripts contents
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $this->addData(
            [
                'button_label' => $element->getData('field_config/button_label'),
                'html_id' => $element->getData('html_id'),
            ]
        );

        return $this->_toHtml();
    }
}