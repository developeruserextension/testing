<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\SourceOptions;

/**
 * Class OrderStatus
 */
class OrderStatus extends \Magento\Sales\Model\Config\Source\Order\Status
{
    public function toOptionArray()
    {
        //remove Please Select option
        return array_slice(parent::toOptionArray(), 1);
    }
}
