<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Model\History;

use Magento\Framework\Option\ArrayInterface;

class MessageProcessor implements ArrayInterface
{
    /** %1 - add N store credits. %2 - new store credit balance */
    const ADMIN_BALANCE_CHANGE_PLUS = 1;
    /** %1 - remove N store credits. %2 - new store credit balance */
    const ADMIN_BALANCE_CHANGE_MINUS = 2;
    /** %1 - add N store credits. %2 - new store credit balance. %3 - order increment id */
    const CREDIT_MEMO_REFUND = 3;
    /** %1 - remove N store credits. %2 - new store credit balance. %3 - order id */
    const ORDER_PAY = 4;
    /** %1 - add N store credits. %2 - new store credit balance. %3 - order increment id */
    const ORDER_CANCEL = 5;

    /**
     * @var array
     */
    public static $actionsFull = [
        self::ADMIN_BALANCE_CHANGE_PLUS => 'Administrator added %1 store credits to your balance',
        self::ADMIN_BALANCE_CHANGE_MINUS => 'Administrator removed %1 store credits from your balance',
        self::CREDIT_MEMO_REFUND  => 'You order #%3 was refunded on %1',
        self::ORDER_PAY => 'Order #%3 was payed with %1 store credits',
        self::ORDER_CANCEL => 'Order #%3 was canceled. Returned %1 store credits',
    ];

    public static $actionsSmall = [
        self::ADMIN_BALANCE_CHANGE_PLUS => 'Changed By Admin',
        self::ADMIN_BALANCE_CHANGE_MINUS => 'Changed By Admin',
        self::CREDIT_MEMO_REFUND  => 'Refunded #%3',
        self::ORDER_PAY => 'Order Paid #%3',
        self::ORDER_CANCEL => 'Order Canceled #%3'
    ];

    /**
     * @var array
     */
    public static $actionsMail = [
        self::ADMIN_BALANCE_CHANGE_PLUS => 'Administrator adds to store credit balance',
        self::ADMIN_BALANCE_CHANGE_MINUS => 'Administrator removes from store credit balance',
        self::CREDIT_MEMO_REFUND  => 'Order refund (paid with store credit)',
        self::ORDER_PAY => 'Order place (paid with store credit)',
        self::ORDER_CANCEL => 'Order cancelation (paid with store credit)',
    ];

    public function addToI18n()
    {
        __('Administrator add %1 store credits to your balance');
        __('Administrator remove %1 store credits from your balance');
        __('You order %1 was refunded on %2');
        __('Order %1 was payed by %2 store credits');
        __('Changed By Admin');
        __('Refunded #%3');
        __('Order Paid #%3');
        __('On Administrator add store credits to balance');
        __('On Administrator remove store credits to balance');
        __('On order refund');
        __('On Order payed by store credits');
    }

    /**
     * @param int $action
     * @param array $data
     *
     * @return \Magento\Framework\Phrase|string
     */
    public static function processFull($action, array $data)
    {
        if (isset(self::$actionsFull[$action])) {

            return __(self::$actionsFull[$action], ...$data);
        }

        return '';
    }

    /**
     * @param int $action
     * @param array $data
     *
     * @return \Magento\Framework\Phrase|string
     */
    public static function processSmall($action, array $data)
    {
        if (isset(self::$actionsSmall[$action])) {

            return __(self::$actionsSmall[$action], ...$data);
        }

        return '';
    }

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
        return self::$actionsMail;
    }
}
