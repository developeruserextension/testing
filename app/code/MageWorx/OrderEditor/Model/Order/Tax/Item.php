<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Model\Order\Tax;

use Magento\Sales\Model\Order\Tax\Item as OriginalTaxItem;

/**
 * Class Item
 *
 * @method float getAmount()
 * @method float getBaseAmount()
 * @method float getRealBaseAmount()
 * @method float getPercent()
 */
class Item extends OriginalTaxItem
{
    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magento\Sales\Model\ResourceModel\Order\Tax\Item::class);
    }

    /**
     * Alias for real getter, because in the core class Tax it is mixed
     *
     * @return float
     */
    public function getBaseRealAmount()
    {
        return $this->getRealBaseAmount();
    }
}
