<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Block;

use Amasty\HidePrice\Helper\Data;
use Magento\Framework\View\Element\Template;
use Amasty\HidePrice\Model\Source\ReplaceButton;
use Amasty\HidePrice\Model\Source\HideButton;

class Form extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Data
     */
    private $helper;

    public function __construct(
        Template\Context $context,
        Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        $html = '';
        $isPopupContentGenerated = false;

        if ($this->helper->getModuleConfig('information/hide_button') == HideButton::REPLACE_WITH_NEW_ONE) {
            switch ($this->helper->getModuleConfig('information/replace_with')) {
                case ReplaceButton::HIDE_PRICE_POPUP:
                    $isPopupContentGenerated = true;
                    $html = $this->getDefaultPopup();
                    break;
                case ReplaceButton::CUSTOM_FORM:
                    if ($this->helper->isCustomFormOn()) {
                        $isPopupContentGenerated = true;
                        $html = $this->getLayout()->createBlock(
                            \Amasty\Customform\Block\Init::class,
                            '',
                            [
                                'data' => [
                                    'form_id' => $this->helper->getModuleConfig('information/custom_form')
                                ]
                            ]
                        )->addAdditionalClass('amhideprice-form no-display')->toHtml();
                    }
                    break;
            }
        }
        if (!$isPopupContentGenerated && $this->isPopupEnabled()) {
            $html .= $this->getDefaultPopup();
        }


        return $html;
    }

    /**
     * @return string
     */
    private function getDefaultPopup()
    {
        $this->setTemplate('Amasty_HidePrice::form.phtml');
        return parent::toHtml();
    }

    /**
     * Check if GDPR consent enabled
     *
     * @return bool
     */
    public function isGDPREnabled()
    {
        return $this->helper->isGDPREnabled();
    }

    /**
     * Get text for GDPR
     *
     * @return string
     */
    public function getGDPRText()
    {
        return $this->helper->getGDPRText();
    }

    /**
     * @return bool
     */
    private function isPopupEnabled()
    {
        return $this->helper->getModuleConfig('frontend/link') == Data::HIDE_PRICE_POPUP_IDENTIFICATOR;
    }
}
