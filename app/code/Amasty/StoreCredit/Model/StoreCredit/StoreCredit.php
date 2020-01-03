<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Model\StoreCredit;

use Amasty\StoreCredit\Api\Data\StoreCreditInterface;
use Magento\Framework\Model\AbstractModel;

class StoreCredit extends AbstractModel implements StoreCreditInterface
{
    public function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\StoreCredit\Model\StoreCredit\ResourceModel\StoreCredit::class);
        $this->setIdFieldName(StoreCreditInterface::STORE_CREDIT_ID);
    }

    /**
     * @return int
     */
    public function getStoreCreditId()
    {
        return (int)$this->_getData(StoreCreditInterface::STORE_CREDIT_ID);
    }

    /**
     * @param int $storeCreditId
     *
     * @return \Amasty\StoreCredit\Api\Data\StoreCreditInterface
     */
    public function setStoreCreditId($storeCreditId)
    {
        return $this->setData(StoreCreditInterface::STORE_CREDIT_ID, (int)$storeCreditId);
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return (int)$this->_getData(StoreCreditInterface::CUSTOMER_ID);
    }

    /**
     * @param int $customerId
     *
     * @return \Amasty\StoreCredit\Api\Data\StoreCreditInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(StoreCreditInterface::CUSTOMER_ID, (int)$customerId);
    }

    /**
     * @return float
     */
    public function getStoreCredit()
    {
        return (float)$this->_getData(StoreCreditInterface::STORE_CREDIT);
    }

    /**
     * @param float $storeCredit
     *
     * @return \Amasty\StoreCredit\Api\Data\StoreCreditInterface
     */
    public function setStoreCredit($storeCredit)
    {
        return $this->setData(StoreCreditInterface::STORE_CREDIT, (float)$storeCredit);
    }
}
