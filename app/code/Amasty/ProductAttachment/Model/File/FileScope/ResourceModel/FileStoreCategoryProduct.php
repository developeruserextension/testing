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

class FileStoreCategoryProduct extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(
            CreateFileScopeTables::FILE_STORE_CATEGORY_PRODUCT_TABLE_NAME,
            FileScopeInterface::FILE_STORE_CATEGORY_PRODUCT_ID
        );
    }

    /**
     * @param array $fileIds
     * @param int $productId
     * @param int $categoryId
     * @param int $store
     */
    public function getFilesProductCategoryData($fileIds, $productId, $categoryId, $store)
    {
        $select = $this->getConnection()->select()->from(
            ['fspc' => $this->getMainTable()]
        )
            ->where('fspc.' . FileScopeInterface::FILE_ID . ' IN (?)', $fileIds)
            ->where('fspc.' . FileScopeInterface::PRODUCT_ID . ' = ?', $productId)
            ->where('fspc.' . FileScopeInterface::CATEGORY_ID . ' = ?', $categoryId)
            ->where('fspc.' . FileScopeInterface::STORE_ID . ' = ?', $store);

        if ($result = $this->getConnection()->fetchAll($select)) {
            return $result;
        }

        return [];
    }

    public function getProductCategoryStoreFile($fileId, $productId, $categoryId, $store)
    {
        $select = $this->getConnection()->select()->from(
            ['fspc' => $this->getMainTable()]
        )
            ->where('fspc.' . FileScopeInterface::FILE_ID . ' = ?', $fileId)
            ->where('fspc.' . FileScopeInterface::PRODUCT_ID . ' = ?', $productId)
            ->where('fspc.' . FileScopeInterface::CATEGORY_ID . ' = ?', $categoryId)
            ->where('fspc.' . FileScopeInterface::STORE_ID . ' = ?', $store);

        if ($result = $this->getConnection()->fetchRow($select)) {
            return $result;
        }

        return [];
    }

    public function saveProductCategoryStoreFile($data)
    {
        if (!empty($data[FileScopeInterface::FILE_STORE_CATEGORY_PRODUCT_ID])) {
            $this->updateFileStoreProductCategoryData($data);
        } else {
            $this->insertFileStoreProductCategoryData($data);
        }
    }

    public function updateFileStoreProductCategoryData($data)
    {
        $productCategoryId = (int)$data[FileScopeInterface::FILE_STORE_CATEGORY_PRODUCT_ID];
        if ($productCategoryId) {
            unset($data[FileScopeInterface::FILE_STORE_CATEGORY_PRODUCT_ID]);

            $this->getConnection()->update(
                $this->getMainTable(),
                $data,
                [FileScopeInterface::FILE_STORE_CATEGORY_PRODUCT_ID. ' = ?' => $productCategoryId]
            );
        }
    }

    public function insertFileStoreProductCategoryData($data)
    {
        unset($data[FileScopeInterface::FILE_STORE_CATEGORY_PRODUCT_ID]);
        $this->getConnection()->insert($this->getMainTable(), $data);
    }
}
