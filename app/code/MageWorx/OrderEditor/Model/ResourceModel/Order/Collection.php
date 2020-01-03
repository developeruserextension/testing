<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Model\ResourceModel\Order;

use Magento\Sales\Model\ResourceModel\Order\Collection as OriginalOrderCollection;
use MageWorx\OrderEditor\Model\Order as OrderEditorOrder;
use MageWorx\OrderEditor\Model\ResourceModel\Order as OrderEditorOrderResource;

/**
 * Class Collection
 */
class Collection extends OriginalOrderCollection
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
        $this->_init(OrderEditorOrder::class, OrderEditorOrderResource::class);
    }
}
