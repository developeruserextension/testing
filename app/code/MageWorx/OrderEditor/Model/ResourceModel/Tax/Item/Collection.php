<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Model\ResourceModel\Tax\Item;

/**
 * Class Collection
 */
class Collection extends \Magento\Sales\Model\ResourceModel\Collection\AbstractCollection
{
    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \MageWorx\OrderEditor\Model\Order\Tax\Item::class,
            \Magento\Sales\Model\ResourceModel\Order\Tax\Item::class
        );
    }

    /**
     * @param int $orderItemId
     * @return $this
     */
    public function addOrderItemIdFilter(int $orderItemId): self
    {
        $this->addFilter('item_id', $orderItemId);

        return $this;
    }

    /**
     * @return $this
     */
    public function addTaxCodeColumn(): self
    {
        $this->join(['sot' => $this->getTable('sales_order_tax')], 'sot.tax_id = main_table.tax_id', ['code']);

        return $this;
    }
}
