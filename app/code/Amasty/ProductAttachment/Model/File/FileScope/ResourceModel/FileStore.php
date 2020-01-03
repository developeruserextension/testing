<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\File\FileScope\ResourceModel;

use Amasty\ProductAttachment\Api\Data\FileScopeInterface;
use Amasty\ProductAttachment\Setup\Operation\CreateFileScopeTables;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class FileStore extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(CreateFileScopeTables::FILE_STORE_TABLE_NAME, FileScopeInterface::FILE_STORE_ID);
    }

    /**
     * @param int $fileId
     * @param int $storeId
     *
     * @return array|bool
     */
    public function getByStoreId($fileId, $storeId)
    {
         $select = $this->getConnection()->select()
            ->from(['fs' => $this->getMainTable()])
            ->where('fs.' . FileScopeInterface::STORE_ID . ' = ?', (int)$storeId)
            ->where('fs.' . FileScopeInterface::FILE_ID . ' = ?', (int)$fileId);

        if ($result = $this->getConnection()->fetchRow($select)) {
            return $result;
        }

         return false;
    }

    public function saveFileStoreData($fileStoreData)
    {
        if (!empty($fileStoreData[FileScopeInterface::FILE_STORE_ID])) {
            $fileStoreId = (int)$fileStoreData[FileScopeInterface::FILE_STORE_ID];
            unset($fileStoreData[FileScopeInterface::FILE_STORE_ID]);
            $this->getConnection()->update(
                $this->getMainTable(),
                $fileStoreData,
                [FileScopeInterface::FILE_STORE_ID . ' = ?' => $fileStoreId]
            );

            return $fileStoreId;
        } else {
            $this->getConnection()->insert($this->getMainTable(), $fileStoreData);

            return $this->getConnection()->lastInsertId();
        }
    }

    /**
     * @param int $fileId
     * @param int $storeId
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectFileStoreByStore($fileId, $storeId)
    {
        return $this->getConnection()->select()
            ->from(['fs' => $this->getMainTable()])
            ->where('fs.' . FileScopeInterface::STORE_ID . ' = ?', (int)$storeId)
            ->where('fs.' . FileScopeInterface::FILE_ID . ' = ?', (int)$fileId)
            ->where(
                '(fs.' . FileScopeInterface::CATEGORY_ID . ' IS NULL OR '
                . 'fs.' . FileScopeInterface::PRODUCT_ID . ' IS NULL)'
            );
    }

    /**
     * @param int $fileId
     * @param int $storeId
     *
     * @return array
     */
    public function getFileStoreByStore($fileId, $storeId)
    {
        return $this->getConnection()->fetchAll($this->getSelectFileStoreByStore($fileId, $storeId));
    }

    /**
     * @param array $fileStoreIds
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteMultiple($fileStoreIds)
    {
        if (empty($fileStoreIds)) {
            return;
        }
        foreach ($fileStoreIds as &$fileStoreId) {
            $fileStoreId = (int)$fileStoreId;
        }
        $this->getConnection()->delete(
            $this->getMainTable(),
            [FileScopeInterface::FILE_STORE_ID . ' IN (?)' => array_unique($fileStoreIds)]
        );
    }

    /**
     * @param array $fileStoreData
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateFileStoreData($fileStoreData)
    {
        $fileStoreId = (int)$fileStoreData[FileScopeInterface::FILE_STORE_ID];
        if ($fileStoreId) {
            unset($fileStoreData[FileScopeInterface::FILE_STORE_ID]);

            $this->getConnection()->update(
                $this->getMainTable(),
                $fileStoreData,
                [FileScopeInterface::FILE_STORE_ID . ' = ?' => $fileStoreId]
            );
        }
    }

    /**
     * @param array $fileStoreData
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function insertFileStoreData($fileStoreData)
    {
        unset($fileStoreData[FileScopeInterface::FILE_STORE_ID]);
        $this->getConnection()->insert($this->getMainTable(), $fileStoreData);
    }
}
