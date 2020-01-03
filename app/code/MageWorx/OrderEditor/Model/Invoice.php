<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Model;

use Magento\Sales\Model\Order\Invoice as OriginalInvoice;

/**
 * Class Invoice
 */
class Invoice extends OriginalInvoice
{
    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\MageWorx\OrderEditor\Model\ResourceModel\Invoice::class);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeDelete(): self
    {
        $this->deleteComments();
        $this->deleteInvoiceItems();
        $this->deleteFromGrid();

        parent::beforeDelete();

        return $this;
    }

    /**
     * @return void
     */
    protected function deleteComments()
    {
        $this->_commentCollectionFactory
            ->create()
            ->setInvoiceFilter($this->getId())
            ->walk('delete');
    }

    /**
     * @return void
     */
    protected function deleteInvoiceItems()
    {
        $this->_invoiceItemCollectionFactory
            ->create()
            ->setInvoiceFilter($this->getId())
            ->walk('delete');
    }

    /**
     * @return void
     */
    protected function deleteFromGrid()
    {
        $id = (int)$this->getId();
        if (!$id) {
            return;
        }

        $resource   = $this->getResource();
        $connection = $resource->getConnection();

        $salesInvoiceGridTable = $resource->getTable('sales_invoice_grid');
        $connection->delete($salesInvoiceGridTable, ['entity_id = ?' => $id]);
    }
}
