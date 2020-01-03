<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */


namespace Amasty\Paction\Block\Adminhtml\Product\Edit\Action\Attribute\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Catalog\Block\Adminhtml\Form;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

class TierPrice extends Form implements TabInterface
{
    const TIER_PRICE_CHANGE_CHECKBOX_NAME = 'tier_price_checkbox';

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        ProductMetadataInterface $productMetadata,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->productMetadata = $productMetadata;
    }

    /**
     * Tab settings
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return $this->getTitleDependFromVersion();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return $this->getTitleDependFromVersion();
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

    /**
     * @return \Magento\Framework\Phrase
     */
    private function getLegend()
    {
        return $this->getTitleDependFromVersion();
    }

    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setFieldNameSuffix('attributes');
        $fieldset = $form->addFieldset('tiered_price', ['legend' => $this->getLegend()]);

        $fieldset->addField(
            'tier_price',
            'text',
            [
                'name' => 'tier_price',
                'class' => 'requried-entry',
                'label' => $this->getTabLabel(),
                'title' => $this->getTabTitle()
            ]
        );

        $form->getElement(
            'tier_price'
        )->setRenderer(
            $this->getLayout()
                ->createBlock(
                    \Amasty\Paction\Block\Adminhtml\Product\Edit\Action\Attribute\Tab\TierPrice\Content::class
                )
        );

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    private function getTitleDependFromVersion()
    {
        return version_compare($this->productMetadata->getVersion(), '2.2', '>=') ? __('Advanced Pricing') : __('Tier Prices');
    }

    /**
     * @return string
     */
    private function getAfterHtml()
    {
        $elementName = self::TIER_PRICE_CHANGE_CHECKBOX_NAME;
        $elementId = $this->getId();
        $dataCheckboxName = "toggle_" . "{$elementId}";
        $checkboxLabel = __('Delete Previously Saved Advanced Pricing');
        $html = <<<HTML
<div class="field" style="margin-left: 20%;">
    <span class="attribute-change-checkbox">
        <input type="checkbox" id="$dataCheckboxName" name="$elementName" class="checkbox" />
        <label class="label" for="$dataCheckboxName">
            {$checkboxLabel}
        </label>
    </span>
</div>
HTML;

        return $html;
    }

    /**
     * @param string $html
     * @return string
     */
    protected function _afterToHtml($html)
    {
        return parent::_afterToHtml($html) . $this->getAfterHtml();
    }
}
