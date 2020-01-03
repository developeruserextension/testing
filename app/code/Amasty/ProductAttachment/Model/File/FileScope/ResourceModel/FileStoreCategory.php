<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\File\FileScope\ResourceModel;

use Amasty\ProductAttachment\Api\Data\FileInterface;
use Amasty\ProductAttachment\Api\Data\FileScopeInterface;
use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Amasty\ProductAttachment\Setup\Operation\CreateFileScopeTables;
use Amasty\ProductAttachment\Setup\Operation\CreateFileTable;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class FileStoreCategory extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(
            CreateFileScopeTables::FILE_STORE_CATEGORY_TABLE_NAME,
            FileScopeInterface::FILE_STORE_CATEGORY_ID
        );
    }

    /**
     * @param int $fileId
     * @param int $storeId
     *
     * @return array|bool
     */
    public function getStoreCategoryIdsByStoreId($fileId, $storeId)
    {
        $select = $this->getConnection()->select()
            ->from(['fsc' => $this->getMainTable()], [
                FileScopeInterface::CATEGORY_ID,
                FileScopeInterface::FILE_STORE_CATEGORY_ID
            ])
            ->where('fsc.' . FileScopeInterface::STORE_ID . ' = ?', (int)$storeId)
            ->where('fsc.' . FileScopeInterface::FILE_ID . ' = ?', (int)$fileId);

        if ($result = $this->getConnection()->fetchAssoc($select)) {
            return $result;
        }

        return [];
    }

    /**
     * @param int $fileId
     * @param int $categoryId
     * @param int $storeId
     *
     * @return array|bool
     */
    public function getCategoryStoreFile($fileId, $categoryId, $storeId)
    {
        $select = $this->getConnection()->select()
            ->from(['fsc' => $this->getMainTable()])
            ->where('fsc.' . FileScopeInterface::CATEGORY_ID . ' = ?', (int)$categoryId)
            ->where('fsc.' . FileScopeInterface::STORE_ID . ' = ?', (int)$storeId)
            ->where('fsc.' . FileScopeInterface::FILE_ID . ' = ?', (int)$fileId);

        if ($result = $this->getConnection()->fetchRow($select)) {
            return $result;
        }

        return [];
    }

    public function saveFileStoreCategory($data)
    {
        if (!empty($data[FileScopeInterface::FILE_STORE_CATEGORY_ID])) {
            $this->updateFileStoreCategoryData($data);
        } else {
            $this->insertFileStoreCategoryData($data);
        }
    }

    /**
     * @param int $categoryId
     * @param int $storeId
     *
     * @return array|bool
     */
    public function getFilesIdsByStoreId($categoryId, $storeId)
    {
        $defaultCols = [];
        $superDefaultCols = [];
        $category0DefaultCols = [];
        foreach (RegistryConstants::USE_DEFAULT_FIELDS as $field) {
            $defaultCols['default_' . $field] = $field;
            $category0DefaultCols['cat0_default_' . $field] = $field;
            $superDefaultCols['super_default_' . $field] = $field;
        }

        $select = $this->getConnection()->select()
            ->from(['fsc' => $this->getMainTable()], [
                '*'
            ])->joinLeft(
                ['fs' => $this->getTable(CreateFileScopeTables::FILE_STORE_TABLE_NAME)],
                'fs.' . FileScopeInterface::FILE_ID . ' = fsc.' . FileScopeInterface::FILE_ID
                . ' AND fs.' . FileScopeInterface::STORE_ID . ' = ' . (int)$storeId,
                $defaultCols
            );

        $fileIds = [];
        if ($storeId) {
            $select->joinLeft(
                ['fsc0' => $this->getTable(CreateFileScopeTables::FILE_STORE_CATEGORY_TABLE_NAME)],
                'fsc0.' . FileScopeInterface::FILE_ID . ' = fsc.' . FileScopeInterface::FILE_ID
                . ' AND fsc0.' . FileScopeInterface::STORE_ID . ' = 0'
                .' AND fsc0.' . FileScopeInterface::CATEGORY_ID . ' = fsc.' . FileScopeInterface::CATEGORY_ID,
                $category0DefaultCols
            );

            $selectFileIds = $this->getConnection()->select()->from(
                ['fsc' => $this->getMainTable()],
                [
                    'fsc.' . FileScopeInterface::FILE_ID,
                    new \Zend_Db_Expr(' max(fsc.' . FileScopeInterface::STORE_ID . ') as max_store_id'),
                    new \Zend_Db_Expr(' (select count(*) from ' . $this->getMainTable()
                        . ' where ' .FileScopeInterface::FILE_ID. ' = fsc.' . FileScopeInterface::FILE_ID
                        . ' and ' . FileScopeInterface::STORE_ID . ' = '.(int)$storeId.') as storescategories'),
                ]
            )
            ->where('fsc.' . FileScopeInterface::CATEGORY_ID . ' = ?', (int)$categoryId)
            ->where('fsc.' . FileScopeInterface::STORE_ID . ' IN (?)', [0, $storeId])
            ->group('fsc.' . FileScopeInterface::FILE_ID)
            ->having('((max_store_id = 0 and storescategories = 0) OR (max_store_id > 0 and storescategories > 0))');
            if (!($fileIds = $this->getConnection()->fetchCol($selectFileIds))) {
                return [];
            }
        }

        $select->joinLeft(
            ['fs0' => $this->getTable(CreateFileScopeTables::FILE_STORE_TABLE_NAME)],
            'fs0.' . FileScopeInterface::FILE_ID . ' = fsc.' . FileScopeInterface::FILE_ID
            . ' AND fs0.' . FileScopeInterface::STORE_ID . ' = 0',
            $superDefaultCols
        )->joinLeft(
            ['f' => $this->getTable(CreateFileTable::TABLE_NAME)],
            'f.' . FileInterface::FILE_ID . ' = fsc.' . FileScopeInterface::FILE_ID,
            '*'
        )
        ->where('fsc.' . FileScopeInterface::CATEGORY_ID . ' = ?', (int)$categoryId)
        ->where('fsc.' . FileScopeInterface::STORE_ID . ' IN (?)', [$storeId, 0])
        ->order('fsc.' . FileScopeInterface::STORE_ID . ' DESC')
        ->order('fsc.' . FileScopeInterface::POSITION . ' ASC')
        ->order('fsc.' . FileScopeInterface::FILE_ID . ' ASC');
        if ($fileIds) {
            $select->where('fsc.' . FileScopeInterface::FILE_ID . ' IN (?)', $fileIds);
        }

        if ($result = $this->getConnection()->fetchAll($select)) {
            return $result;
        }

        return [];
    }

    /**
     * @param int $fileId
     * @param int $storeId
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteAllByStore($fileId, $storeId)
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            [
                FileScopeInterface::STORE_ID . ' = ?' => (int)$storeId,
                FileScopeInterface::FILE_ID . ' = ?' => (int)$fileId
            ]
        );

        $conditions = [];
        foreach (RegistryConstants::USE_DEFAULT_FIELDS as $field) {
            $conditions[] = $field . ' IS NULL';
        }
        $conditions[FileScopeInterface::STORE_ID . ' = ?'] = (int)$storeId;
        $conditions[FileScopeInterface::FILE_ID . ' = ?'] = (int)$fileId;

        $this->getConnection()->delete(
            $this->getMainTable(),
            $conditions
        );
    }

    /**
     * @param int $fileId
     * @param int $storeId
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isAllStoreViewFile($fileId, $storeId)
    {
        $select = $this->getConnection()->select()->from(
            ['fcs' => $this->getMainTable()],
            [new \Zend_Db_Expr('count(*)')]
        )->where(FileScopeInterface::STORE_ID . ' = ?', (int)$storeId)
        ->where(FileScopeInterface::FILE_ID . ' = ?', (int)$fileId);

        return !(bool)$this->getConnection()->fetchOne($select);
    }

    /**
     * @param int $fileId
     * @param int $categoryId
     * @param int $storeId
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteFileByStoreCategory($fileId, $categoryId, $storeId)
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            [
                FileScopeInterface::STORE_ID . ' = ?' => (int)$storeId,
                FileScopeInterface::FILE_ID . ' = ?' => (int)$fileId,
                FileScopeInterface::CATEGORY_ID . ' = ?' => (int)$categoryId
            ]
        );
    }

    /**
     * @param array $storeCategoriesIds
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteByStoreCategoryIds($storeCategoriesIds)
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            [FileScopeInterface::FILE_STORE_CATEGORY_ID . ' IN (?)' => array_unique($storeCategoriesIds)]
        );
    }

    /**
     * @param array $fileStoreCategoryData
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateFileStoreCategoryData($fileStoreCategoryData)
    {
        $fileStoreCategoryId = (int)$fileStoreCategoryData[FileScopeInterface::FILE_STORE_CATEGORY_ID];
        if ($fileStoreCategoryId) {
            unset($fileStoreCategoryData[FileScopeInterface::FILE_STORE_CATEGORY_ID]);

            $this->getConnection()->update(
                $this->getMainTable(),
                $fileStoreCategoryData,
                [FileScopeInterface::FILE_STORE_CATEGORY_ID . ' = ?' => $fileStoreCategoryId]
            );
        }
    }

    /**
     * @param array $fileStoreCategoryData
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function insertFileStoreCategoryData($fileStoreCategoryData)
    {
        unset($fileStoreCategoryData[FileScopeInterface::FILE_STORE_CATEGORY_ID]);
        $this->getConnection()->insert($this->getMainTable(), $fileStoreCategoryData);
    }
}
