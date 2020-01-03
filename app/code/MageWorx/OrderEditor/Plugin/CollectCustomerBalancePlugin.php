<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Plugin;

/**
 * Class CollectCustomerBalancePlugin
 */
class CollectCustomerBalancePlugin extends AbstractPlugin
{
    /**
     * @param \Magento\CustomerBalance\Model\Total\Quote\Customerbalance $subject
     * @param callable $proceed
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return \Magento\CustomerBalance\Model\Total\Quote\Customerbalance
     */
    public function aroundCollect(
        \Magento\CustomerBalance\Model\Total\Quote\Customerbalance $subject,
        callable $proceed,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ): \Magento\CustomerBalance\Model\Total\Quote\Customerbalance {
        if (!$this->isOrderEdit()) {
            return $proceed($quote, $shippingAssignment, $total);
        }

        return $subject;
    }
}
