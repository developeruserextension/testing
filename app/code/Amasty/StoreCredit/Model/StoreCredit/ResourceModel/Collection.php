<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Model\StoreCredit\ResourceModel;

use Amasty\StoreCredit\Api\Data\StoreCreditInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            \Amasty\StoreCredit\Model\StoreCredit\StoreCredit::class,
            \Amasty\StoreCredit\Model\StoreCredit\ResourceModel\StoreCredit::class
        );
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    /**
     * @param $customerId
     *
     * @return bool|StoreCreditInterface
     */
    public function getByCustomerId($customerId)
    {
        $this->addFieldToFilter(StoreCreditInterface::CUSTOMER_ID, (int)$customerId)
            ->setCurPage(1)
            ->setPageSize(1);
        if ($items = $this->getItems()) {
            return end($items);
        }

        return false;
    }
}
