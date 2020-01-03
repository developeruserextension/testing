<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\File\FileScope\DataProviders;

use Amasty\ProductAttachment\Api\Data\FileInterface;
use Amasty\ProductAttachment\Api\Data\FileScopeInterface;
use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Amasty\ProductAttachment\Model\File\FileScope\ResourceModel\FileStore;
use Amasty\ProductAttachment\Model\File\FileScope\ResourceModel\FileStoreCategory;
use Amasty\ProductAttachment\Model\File\FileScope\ResourceModel\FileStoreProduct;

class File implements FileScopeDataInterface
{
    /**
     * @var FileStore
     */
    private $fileStore;

    /**
     * @var FileStoreCategory
     */
    private $fileStoreCategory;

    /**
     * @var FileStoreProduct
     */
    private $fileStoreProduct;

    public function __construct(
        FileStore $fileStore,
        FileStoreCategory $fileStoreCategory,
        FileStoreProduct $fileStoreProduct
    ) {
        $this->fileStore = $fileStore;
        $this->fileStoreCategory = $fileStoreCategory;
        $this->fileStoreProduct = $fileStoreProduct;
    }

    /**
     * @inheritdoc
     */
    public function execute($params)
    {
        /** @var FileInterface $file */
        $file = $params[RegistryConstants::FILE];
        $store = $params[RegistryConstants::STORE];
        $fileStoreData = [];
        if ($store) {
            $fileStoreData = $this->fileStore->getByStoreId($file->getFileId(), $store);
            foreach (RegistryConstants::USE_DEFAULT_FIELDS as $field) {
                $file->setData(
                    RegistryConstants::USE_DEFAULT_PREFIX . $field,
                    (!isset($fileStoreData[$field]) || $fileStoreData[$field] === null)
                );
            }
        }

        $defaultFileStoreData = $this->fileStore->getByStoreId($file->getFileId(), 0);
        foreach (RegistryConstants::USE_DEFAULT_FIELDS as $field) {
            if (isset($fileStoreData[$field])) {
                if ($fileStoreData[$field] === null) {
                    $file->setData($field, $defaultFileStoreData[$field]);
                } else {
                    $file->setData($field, $fileStoreData[$field]);
                }
            } else {
                $file->setData($field, $defaultFileStoreData[$field]);
            }
        }

        $fileStoreCategories = $this->fileStoreCategory->getStoreCategoryIdsByStoreId($file->getFileId(), $store);
        if (empty($fileStoreCategories)) {
            $file->setData(RegistryConstants::USE_DEFAULT_PREFIX . FileInterface::CATEGORIES, true);
            $fileStoreCategories = $this->fileStoreCategory->getStoreCategoryIdsByStoreId($file->getFileId(), 0);
        }

        if (!empty($fileStoreCategories)) {
            $categoryIds = [];
            foreach ($fileStoreCategories as $fileStoreCategory) {
                $categoryIds[] = $fileStoreCategory[FileScopeInterface::CATEGORY_ID];
            }
            $file->setData(FileInterface::CATEGORIES, $categoryIds);
        }

        $fileStoreProducts = $this->fileStoreProduct->getStoreProductIdsByStoreId($file->getFileId(), $store);
        if (empty($fileStoreProducts)) {
            $file->setData(RegistryConstants::USE_DEFAULT_PREFIX . FileInterface::PRODUCTS, true);
            $fileStoreProducts = $this->fileStoreProduct->getStoreProductIdsByStoreId($file->getFileId(), 0);
        }

        if (!empty($fileStoreProducts)) {
            $productIds = [];
            foreach ($fileStoreProducts as $fileStoreProduct) {
                $productIds[] = $fileStoreProduct[FileScopeInterface::PRODUCT_ID];
            }
            $file->setData(FileInterface::PRODUCTS, $productIds);
        }

        return $file;
    }
}
