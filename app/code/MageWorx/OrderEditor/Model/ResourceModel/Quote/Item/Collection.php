<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Model\ResourceModel\Quote\Item;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ResourceModel\Quote\Item\Collection as OriginalQuoteItemCollection;
use MageWorx\OrderEditor\Model\Quote\Item as OrderEditorQuoteItem;
use MageWorx\OrderEditor\Model\ResourceModel\Quote\Item as OrderEditorQuoteItemResource;

/**
 * Class Collection
 */
class Collection extends OriginalQuoteItemCollection
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
        $this->_init(OrderEditorQuoteItem::class, OrderEditorQuoteItemResource::class);
    }

    /**
     * Set Quote object to Collection.
     *
     * @important Method must be rewritten with that return type because of error in the interceptor
     * (return :self)
     *
     * @param Quote $quote
     * @return $this
     */
    public function setQuote($quote): OriginalQuoteItemCollection
    {
        return parent::setQuote($quote);
    }

    /**
     * Reset the collection and inner join it to quotes table.
     *
     * Optionally can select items with specified product id only
     *
     * @important Method must be rewritten with that return type because of error in the interceptor
     * (return :self)
     *
     * @param string $quotesTableName
     * @param int $productId
     * @return $this
     */
    public function resetJoinQuotes($quotesTableName, $productId = null): OriginalQuoteItemCollection
    {
        return parent::resetJoinQuotes($quotesTableName, $productId);
    }
}
