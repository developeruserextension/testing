<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Model\ResourceModel\Order\Item;

use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OriginalOrderItemCollection;
use MageWorx\OrderEditor\Model\Order\Item as OrderEditorOrderItem;
use MageWorx\OrderEditor\Model\ResourceModel\Order\Item as OrderEditorOrderItemResource;

/**
 * Class Collection
 */
class Collection extends OriginalOrderItemCollection
{
    /**
     * Model initialization.
     * Change classes to own.
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(OrderEditorOrderItem::class, OrderEditorOrderItemResource::class);
    }
}
