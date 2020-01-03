<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Model\StoreCredit\ResourceModel;

use Amasty\StoreCredit\Api\Data\StoreCreditInterface;
use Amasty\StoreCredit\Setup\Operation\CreateStoreCreditTable;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class StoreCredit extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(CreateStoreCreditTable::TABLE_NAME, StoreCreditInterface::STORE_CREDIT_ID);
    }
}
