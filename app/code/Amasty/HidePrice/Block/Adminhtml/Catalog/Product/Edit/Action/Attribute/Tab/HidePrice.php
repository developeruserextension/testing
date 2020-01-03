<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Block\Adminhtml\Catalog\Product\Edit\Action\Attribute\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;

class HidePrice extends \Magento\Catalog\Block\Adminhtml\Form implements TabInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Eav\Model\Config $eavConfig,
        array $data = []
    ) {
        $this->_productFactory = $productFactory;
        parent::__construct($context, $registry, $formFactory, $data);
        $this->eavConfig = $eavConfig;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setShowGlobalIcon(true);
    }

    /**
     * @return $this|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('fields', ['legend' => __('Hide Price')]);
        $attributes = $this->getAttributes();
        /**
         * Initialize product object as form property
         * for using it in elements generation
         */
        $form->setDataObject($this->_productFactory->create());
        $this->_setFieldset($attributes, $fieldset, []);
        $form->setFieldNameSuffix('attributes');
        $this->setForm($form);
    }

    /**
     * Retrieve attributes for product mass update
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttributes()
    {
        $attributes = $this->eavConfig->getEntityType(
            \Magento\Catalog\Model\Product::ENTITY
        )->getAttributeCollection()->clear()
            ->addFieldToFilter('attribute_code', ['in' => ['am_hide_price_customer_gr', 'am_hide_price_mode']]);

        foreach ($attributes as $attribute) {
            $attribute->setIsVisible(true);
            if ($attribute->getAttributeCode() == 'am_hide_price_mode') {
                $attribute->setSourceModel(\Amasty\HidePrice\Model\Source\PriceMode::class);
            }
        }

        return $attributes;
    }

    /**
     * Custom additional element html
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getAdditionalElementHtml($element)
    {
        // Add name attribute to checkboxes that correspond to multiselect elements
        $nameAttributeHtml = $element->getExtType() === 'multiple' ? 'name="' . $element->getId() . '_checkbox"' : '';
        $elementId = $element->getId();
        $dataAttribute = "data-disable='{$elementId}'";
        $dataCheckboxName = "toggle_" . "{$elementId}";
        $checkboxLabel = __('Change');
        $html = <<<HTML
<span class="attribute-change-checkbox">
    <input type="checkbox" id="$dataCheckboxName" name="$dataCheckboxName" class="checkbox" $nameAttributeHtml onclick="toogleFieldEditMode(this, '{$elementId}')" $dataAttribute />
    <label class="label" for="$dataCheckboxName">
        {$checkboxLabel}
    </label>
</span>
HTML;

        return $html;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Hide Price');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Hide Price');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
