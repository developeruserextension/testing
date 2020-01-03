<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Model\ResourceModel\Invoice;

use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection as OriginalCollection;
use MageWorx\OrderEditor\Model\Invoice as OrderEditorInvoice;
use MageWorx\OrderEditor\Model\ResourceModel\Invoice as OrderEditorInvoiceResource;

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
        $this->_init(OrderEditorInvoice::class, OrderEditorInvoiceResource::class);
    }
}
