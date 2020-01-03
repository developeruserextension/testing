<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Plugin;

use Amasty\StoreCredit\Api\Data\SalesFieldInterface;
use Magento\Quote\Model\Quote;

class ResetStoreCreditAfterItemDelete
{
    public function afterRemoveItem(Quote $quote, Quote $result)
    {
        $result->setData(SalesFieldInterface::AMSC_USE, 0);

        return $result;
    }
}
