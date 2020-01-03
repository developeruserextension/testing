<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Report\ResourceModel;

use Amasty\ProductAttachment\Model\Report\Item as ItemModel;
use Amasty\ProductAttachment\Setup\Operation\CreateReportTable;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Item extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(CreateReportTable::TABLE_NAME, ItemModel::ITEM_ID);
    }

    public function clear()
    {
        $this->getConnection()->truncateTable($this->getMainTable());
    }
}
