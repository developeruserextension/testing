<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Api;

interface ManageCustomerStoreCreditInterface
{
    /**
     * @param int $customerId
     * @param float $amount
     * @param int $action
     * @param array $actionData
     * @param int $storeId
     * @param string $message
     *
     * @return \Amasty\StoreCredit\Api\Data\StoreCreditInterface
     */
    public function addOrSubtractStoreCredit(
        $customerId,
        $amount,
        $action,
        $actionData = [],
        $storeId = 0,
        $message = ''
    );
}
