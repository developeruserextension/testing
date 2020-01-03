<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Api;

interface StoreCreditRepositoryInterface
{
    /**
     * @param int $customerId
     *
     * @return \Amasty\StoreCredit\Api\Data\StoreCreditInterface
     */
    public function getByCustomerId($customerId);
}
