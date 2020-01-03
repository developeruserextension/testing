<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Import\ResourceModel;

use Amasty\ProductAttachment\Model\Import\ImportFile as ImportFileModel;
use Amasty\ProductAttachment\Setup\Operation\CreateImportFileTable;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ImportFile extends AbstractDb
{
    public function _construct()
    {
        $this->_init(CreateImportFileTable::TABLE_NAME, ImportFileModel::IMPORT_FILE_ID);
    }

    /**
     * @param int $importId
     * @param array $importFileIds
     */
    public function deleteFiles($importId, $importFileIds) {
        $this->getConnection()->delete(
            $this->getMainTable(),
            [
                ImportFileModel::IMPORT_FILE_ID . ' IN (?)' => array_unique($importFileIds),
                ImportFileModel::IMPORT_ID => (int)$importId
            ]
        );
    }
}
