<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\SourceOptions;

use Magento\Framework\Option\ArrayInterface;

class OrderFilterType implements ArrayInterface
{
    const INCLUDE_IN_ORDER_ONLY = 0;
    const ALL_ATTACHMENTS = 1;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        foreach ($this->toArray() as $widgetType => $label) {
            $optionArray[] = ['value' => $widgetType, 'label' => $label];
        }
        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::INCLUDE_IN_ORDER_ONLY => __('`Include In Order` Only'),
            self::ALL_ATTACHMENTS => __('All Product Attachments'),
        ];
    }
}
