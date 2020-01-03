<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\SourceOptions;

use Magento\Framework\Option\ArrayInterface;

class DownloadSource implements ArrayInterface
{
    const PRODUCT = 1;
    const CATEGORY = 2;
    const ORDER = 3;
    const OTHER = 4;

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
            self::PRODUCT => __('Product'),
            self::CATEGORY => __('Category'),
            self::ORDER => __('Order'),
            self::OTHER => __('Other'),
        ];
    }
}
