<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Model\History;

use Magento\Framework\Option\ArrayInterface;

class HistoryGridAction implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        foreach ($this->toArray() as $value => $label) {
            $optionArray[] = ['value' => $value, 'label' => $label];
        }
        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            MessageProcessor::ADMIN_BALANCE_CHANGE_PLUS => __('Added by Admin'),
            MessageProcessor::ADMIN_BALANCE_CHANGE_MINUS => __('Removed by Admin'),
            MessageProcessor::CREDIT_MEMO_REFUND => __('Refunded'),
            MessageProcessor::ORDER_PAY => __('Order Paid'),
            MessageProcessor::ORDER_CANCEL => __('Order Canceled'),
        ];
    }
}
