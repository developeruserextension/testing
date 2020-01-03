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

class FileStoreProduct extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(
            CreateFileScopeTables::FILE_STORE_PRODUCT_TABLE_NAME,
            FileScopeInterface::FILE_STORE_PRODUCT_ID
        );
    }

    /**
     * @param int $productId
     * @param int $storeId
     *
     * @return array|bool
     */
    public function getFilesIdsByStoreId($productId, $storeId)
    {
        $defaultCols = [];
        $superDefaultCols = [];
        $product0DefaultCols = [];
        foreach (RegistryConstants::USE_DEFAULT_FIELDS as $field) {
            $defaultCols['default_' . $field] = $field;
            $product0DefaultCols['prod0_default_' . $field] = $field;
            $superDefaultCols['super_default_' . $field] = $field;
        }

        $select = $this->getConnection()->select()
            ->from(['fsp' => $this->getMainTable()], [
                '*'
            ])->joinLeft(
                ['fs' => $this->getTable(CreateFileScopeTables::FILE_STORE_TABLE_NAME)],
                'fs.' . FileScopeInterface::FILE_ID . ' = fsp.' . FileScopeInterface::FILE_ID
                . ' AND fs.' . FileScopeInterface::STORE_ID . ' = ' . (int)$storeId,
                $defaultCols
            );

        $fileIds = [];
        if ($storeId) {
            $select->joinLeft(
                ['fsp0' => $this->getTable(CreateFileScopeTables::FILE_STORE_PRODUCT_TABLE_NAME)],
                'fsp0.' . FileScopeInterface::FILE_ID . ' = fsp.' . FileScopeInterface::FILE_ID
                . ' AND fsp0.' . FileScopeInterface::STORE_ID . ' = 0'
                .' AND fsp0.' . FileScopeInterface::PRODUCT_ID . ' = fsp.' . FileScopeInterface::PRODUCT_ID,
                $product0DefaultCols
            );

            $selectFileIds = $this->getConnection()->select()->from(
                ['fsp' => $this->getMainTable()],
                [
                    'fsp.' . FileScopeInterface::FILE_ID,
                    new \Zend_Db_Expr(' max(fsp.' . FileScopeInterface::STORE_ID . ') as max_store_id'),
                    new \Zend_Db_Expr(' (select count(*) from ' . $this->getMainTable()
                        . ' where ' .FileScopeInterface::FILE_ID. ' = fsp.' . FileScopeInterface::FILE_ID
                        . ' and ' . FileScopeInterface::STORE_ID . ' = '.(int)$storeId.') as storesproducts'),
                ]
            )
                ->where('fsp.' . FileScopeInterface::PRODUCT_ID . ' = ?', (int)$productId)
                ->where('fsp.' . FileScopeInterface::STORE_ID . ' IN (?)', [0, $storeId])
                ->group('fsp.' . FileScopeInterface::FILE_ID)
                ->having('((max_store_id = 0 and storesproducts = 0) OR (max_store_id > 0 and storesproducts > 0))');
            if (!($fileIds = $this->getConnection()->fetchCol($selectFileIds))) {
                return [];
            }
        }

        $select->joinLeft(
            ['fs0' => $this->getTable(CreateFileScopeTables::FILE_STORE_TABLE_NAME)],
            'fs0.' . FileScopeInterface::FILE_ID . ' = fsp.' . FileScopeInterface::FILE_ID
            . ' AND fs0.' . FileScopeInterface::STORE_ID . ' = 0',
            $superDefaultCols
        )->joinLeft(
            ['f' => $this->getTable(CreateFileTable::TABLE_NAME)],
            'f.' . FileInterface::FILE_ID . ' = fsp.' . FileScopeInterface::FILE_ID,
            '*'
        )
            ->where('fsp.' . FileScopeInterface::PRODUCT_ID . ' = ?', (int)$productId)
            ->where('fsp.' . FileScopeInterface::STORE_ID . ' IN (?)', [$storeId, 0])
            ->order('fsp.' . FileScopeInterface::STORE_ID . ' DESC')
            ->order('fsp.' . FileScopeInterface::POSITION . ' ASC')
            ->order('fsp.' . FileScopeInterface::FILE_ID . ' ASC');
        if ($fileIds) {
            $select->where('fsp.' . FileScopeInterface::FILE_ID . ' IN (?)', $fileIds);
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
     * @return array
     */
    public function getStoreProductIdsByStoreId($fileId, $storeId)
    {
        $select = $this->getConnection()->select()
            ->from(['fsp' => $this->getMainTable()], [
                FileScopeInterface::PRODUCT_ID,
                FileScopeInterface::FILE_STORE_PRODUCT_ID
            ])
            ->where('fsp.' . FileScopeInterface::STORE_ID . ' = ?', (int)$storeId)
            ->where('fsp.' . FileScopeInterface::FILE_ID . ' = ?', (int)$fileId);

        if ($result = $this->getConnection()->fetchAssoc($select)) {
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
    }

    /**
     * @param array $storeProductIds
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteByStoreProductIds($storeProductIds)
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            [FileScopeInterface::FILE_STORE_PRODUCT_ID . ' IN (?)' => array_unique($storeProductIds)]
        );
    }

    /**
     * @param array $fileStoreProductData
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateFileStoreProductData($fileStoreProductData)
    {
        $fileStoreProductId = (int)$fileStoreProductData[FileScopeInterface::FILE_STORE_PRODUCT_ID];
        if ($fileStoreProductId) {
            unset($fileStoreProductData[FileScopeInterface::FILE_STORE_PRODUCT_ID]);

            $this->getConnection()->update(
                $this->getMainTable(),
                $fileStoreProductData,
                [FileScopeInterface::FILE_STORE_PRODUCT_ID . ' = ?' => $fileStoreProductId]
            );
        }
    }

    /**
     * @param array $fileStoreProductData
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function insertFileStoreProductData($fileStoreProductData)
    {
        unset($fileStoreProductData[FileScopeInterface::FILE_STORE_PRODUCT_ID]);
        $this->getConnection()->insert($this->getMainTable(), $fileStoreProductData);
    }

    /**
     * @param int $fileId
     * @param int $productId
     * @param int $storeId
     *
     * @return array|bool
     */
    public function getProductStoreFile($fileId, $productId, $storeId)
    {
        $select = $this->getConnection()->select()
            ->from(['fsc' => $this->getMainTable()])
            ->where('fsc.' . FileScopeInterface::PRODUCT_ID . ' = ?', (int)$productId)
            ->where('fsc.' . FileScopeInterface::STORE_ID . ' = ?', (int)$storeId)
            ->where('fsc.' . FileScopeInterface::FILE_ID . ' = ?', (int)$fileId);

        if ($result = $this->getConnection()->fetchRow($select)) {
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
    public function isAllStoreViewFile($fileId, $storeId)
    {
        $select = $this->getConnection()->select()->from(
            ['fcs' => $this->getMainTable()],
            [new \Zend_Db_Expr('count(*)')]
        )->where(FileScopeInterface::STORE_ID . ' = ?', (int)$storeId)
            ->where(FileScopeInterface::FILE_ID . ' = ?', (int)$fileId);

        return !(bool)$this->getConnection()->fetchOne($select);
    }

    public function saveFileStoreProduct($data)
    {
        if (!empty($data[FileScopeInterface::FILE_STORE_PRODUCT_ID])) {
            $this->updateFileStoreProductData($data);
        } else {
            $this->insertFileStoreProductData($data);
        }
    }

    /**
     * @param int $fileId
     * @param int $productId
     * @param int $storeId
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteFileByStoreProduct($fileId, $productId, $storeId)
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            [
                FileScopeInterface::STORE_ID . ' = ?' => (int)$storeId,
                FileScopeInterface::FILE_ID . ' = ?' => (int)$fileId,
                FileScopeInterface::PRODUCT_ID . ' = ?' => (int)$productId
            ]
        );
    }
}
