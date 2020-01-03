<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Model\Quote;

/**
 * Class Item
 */
class Item extends \Magento\Quote\Model\Quote\Item
{
    /**
     * @var string
     */
    const PREFIX_ID = 'q';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\MageWorx\OrderEditor\Model\ResourceModel\Quote\Item::class);
    }
}
