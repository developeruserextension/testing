<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Model\ResourceModel\Quote;

use Magento\Quote\Model\ResourceModel\Quote\Collection as OriginalQuoteCollection;
use MageWorx\OrderEditor\Model\Quote as OrderEditorQuote;
use MageWorx\OrderEditor\Model\ResourceModel\Quote as OrderEditorQuoteResource;

/**
 * Class Collection
 */
class Collection extends OriginalQuoteCollection
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
        $this->_init(OrderEditorQuote::class, OrderEditorQuoteResource::class);
    }
}
