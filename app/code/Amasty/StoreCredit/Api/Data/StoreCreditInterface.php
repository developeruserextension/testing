<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Api\Data;

interface StoreCreditInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const STORE_CREDIT_ID = 'store_credit_id';
    const CUSTOMER_ID = 'customer_id';
    const STORE_CREDIT = 'store_credit';
    /**#@-*/

    /**#@+
     * Constants defined for form keys
     */
    const ADD_OR_SUBTRACT = 'add_or_subtract';
    const ADMIN_COMMENT = 'amstorecredit_comment';
    /**#@-*/

    /**
     * @return int
     */
    public function getStoreCreditId();

    /**
     * @param int $storeCreditId
     *
     * @return \Amasty\StoreCredit\Api\Data\StoreCreditInterface
     */
    public function setStoreCreditId($storeCreditId);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $customerId
     *
     * @return \Amasty\StoreCredit\Api\Data\StoreCreditInterface
     */
    public function setCustomerId($customerId);

    /**
     * @return float
     */
    public function getStoreCredit();

    /**
     * @param float $storeCredit
     *
     * @return \Amasty\StoreCredit\Api\Data\StoreCreditInterface
     */
    public function setStoreCredit($storeCredit);
}
