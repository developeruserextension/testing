<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Model\History\ResourceModel;

use Amasty\StoreCredit\Api\Data\HistoryInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            \Amasty\StoreCredit\Model\History\History::class,
            History::class
        );
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    public function setDashboarFilters($customerId)
    {
        $this->addFieldToFilter('main_table.' . HistoryInterface::CUSTOMER_ID, $customerId);
    }
}
