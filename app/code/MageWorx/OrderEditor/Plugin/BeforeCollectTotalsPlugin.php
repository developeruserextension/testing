<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Plugin;

use Magento\Quote\Model\Quote;

/**
 * Class BeforeCollectTotalsPlugin
 */
class BeforeCollectTotalsPlugin extends AbstractPlugin
{
    /**
     * @param \Magento\GiftCardAccount\Model\Plugin\TotalsCollector $object
     * @param callable $proceed
     * @param Quote\TotalsCollector $subject
     * @param Quote $quote
     */
    public function aroundBeforeCollect(
        \Magento\GiftCardAccount\Model\Plugin\TotalsCollector $object,
        callable $proceed,
        \Magento\Quote\Model\Quote\TotalsCollector $subject,
        Quote $quote
    ) {
        if ($this->isOrderEdit()) {
            $quote->setBaseGiftCardsAmountUsed(0);
            $quote->setGiftCardsAmountUsed(0);
        } else {
            $proceed($subject, $quote);
        }
    }
}
