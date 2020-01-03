<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Api\Data;

interface HistoryInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const HISTORY_ID = 'history_id';
    const CUSTOMER_HISTORY_ID = 'customer_history_id';
    const CUSTOMER_ID = 'customer_id';
    const IS_DEDUCT = 'is_deduct';
    const DIFFERENCE = 'difference';
    const STORE_CREDIT_BALANCE = 'store_credit_balance';
    const ACTION = 'action';
    const ACTION_DATA = 'action_data';
    const MESSAGE = 'message';
    const CREATED_AT = 'created_at';
    const STORE_ID = 'store_id';
    /**#@-*/

    /**
     * @return int
     */
    public function getHistoryId();

    /**
     * @param int $historyId
     *
     * @return \Amasty\StoreCredit\Api\Data\HistoryInterface
     */
    public function setHistoryId($historyId);

    /**
     * @return int
     */
    public function getCustomerHistoryId();

    /**
     * @param int $customerHistoryId
     *
     * @return \Amasty\StoreCredit\Api\Data\HistoryInterface
     */
    public function setCustomerHistoryId($customerHistoryId);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $customerId
     *
     * @return \Amasty\StoreCredit\Api\Data\HistoryInterface
     */
    public function setCustomerId($customerId);

    /**
     * @return bool
     */
    public function isDeduct();

    /**
     * @param bool $isDeduct
     *
     * @return \Amasty\StoreCredit\Api\Data\HistoryInterface
     */
    public function setIsDeduct($isDeduct);

    /**
     * @return float
     */
    public function getDifference();

    /**
     * @param float $difference
     *
     * @return \Amasty\StoreCredit\Api\Data\HistoryInterface
     */
    public function setDifference($difference);

    /**
     * @return float
     */
    public function getStoreCreditBalance();

    /**
     * @param float $storeCreditBalance
     *
     * @return \Amasty\StoreCredit\Api\Data\HistoryInterface
     */
    public function setStoreCreditBalance($storeCreditBalance);

    /**
     * @return int
     */
    public function getAction();

    /**
     * @param int $action
     *
     * @return \Amasty\StoreCredit\Api\Data\HistoryInterface
     */
    public function setAction($action);

    /**
     * @return string
     */
    public function getActionData();

    /**
     * @param string $actionData
     *
     * @return \Amasty\StoreCredit\Api\Data\HistoryInterface
     */
    public function setActionData($actionData);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param string $message
     *
     * @return \Amasty\StoreCredit\Api\Data\HistoryInterface
     */
    public function setMessage($message);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param int $storeId
     *
     * @return \Amasty\StoreCredit\Api\Data\HistoryInterface
     */
    public function setStoreId($storeId);

    /**
     * @return int
     */
    public function getStoreId();
}
