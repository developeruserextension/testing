<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\File\ResourceModel;

use Amasty\ProductAttachment\Api\Data\FileInterface;
use Amasty\ProductAttachment\Setup\Operation\CreateFileTable;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class File extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(CreateFileTable::TABLE_NAME, FileInterface::FILE_ID);
    }
}
