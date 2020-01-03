<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Import\ResourceModel;

use Amasty\ProductAttachment\Model\Import\Import as ImportModel;
use Amasty\ProductAttachment\Setup\Operation\CreateImportTable;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Import extends AbstractDb
{
    public function _construct()
    {
        $this->_init(CreateImportTable::TABLE_NAME, ImportModel::IMPORT_ID);
    }
}
