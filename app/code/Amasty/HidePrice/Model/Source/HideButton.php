<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Model\Source;

class HideButton implements \Magento\Framework\Option\ArrayInterface
{
    const SHOW = 0;
    const HIDE = 1;
    const REPLACE_WITH_NEW_ONE = 2;
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::SHOW,
                'label' => __('No')
            ],
            [
                'value' => self::HIDE,
                'label' => __('Yes')
            ],
            [
                'value' => self::REPLACE_WITH_NEW_ONE,
                'label' => __('Replace with custom button')
            ]
        ];

        return $options;
    }
}
