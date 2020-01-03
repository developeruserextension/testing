<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Model\ResourceModel\Shipment;

use Magento\Sales\Model\ResourceModel\Order\Shipment\Collection as OriginalCollection;
use MageWorx\OrderEditor\Model\Shipment as OrderEditorShipment;
use MageWorx\OrderEditor\Model\ResourceModel\Shipment as OrderEditorShipmentResource;

/**
 * Class Collection
 */
class Collection extends OriginalCollection
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
        $this->_init(OrderEditorShipment::class, OrderEditorShipmentResource::class);
    }
}
