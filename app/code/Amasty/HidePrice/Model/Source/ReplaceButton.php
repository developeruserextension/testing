<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Model\Source;

use Magento\Framework\Module\Manager;
use Magento\Framework\Option\ArrayInterface;

class ReplaceButton implements ArrayInterface
{
    const REDIRECT_URL = 0;
    const HIDE_PRICE_POPUP = 1;
    const CUSTOM_FORM = 2;

    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(Manager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::REDIRECT_URL,
                'label' => __('Link to Custom URL')
            ],
            [
                'value' => self::HIDE_PRICE_POPUP,
                'label' => __('Amasty Hide Price Popup')
            ],
            [
                'value' => self::CUSTOM_FORM,
                'label' => __('Popup with Custom Form')
            ]
        ];
        if ($this->moduleManager->isEnabled('Amasty_Customform') === false) {
            $options[self::CUSTOM_FORM]['label'] .= ' (' . __('not installed') . ')';
        }

        return $options;
    }
}
