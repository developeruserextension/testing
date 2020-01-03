<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\File\FileScope\SaveProcessors;

use Amasty\ProductAttachment\Api\Data\FileScopeInterface;
use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Amasty\ProductAttachment\Model\File\FileScope\ResourceModel\FileStoreCategoryProduct;

class ProductCategories implements FileScopeSaveProcessorInterface
{
    /**
     * @var FileStoreCategoryProduct
     */
    private $fileStoreCategoryProduct;

    public function __construct(
        FileStoreCategoryProduct $fileStoreCategoryProduct
    ) {
        $this->fileStoreCategoryProduct = $fileStoreCategoryProduct;
    }

    /**
     * @inheritdoc
     */
    public function execute($params)
    {
        $storeId = $params[RegistryConstants::STORE];

        if ($files = $params[RegistryConstants::FILES]) {
            foreach ($files as $file) {
                $fileStoreProductCategory = $this->fileStoreCategoryProduct->getProductCategoryStoreFile(
                    $file[FileScopeInterface::FILE_ID],
                    $params[RegistryConstants::PRODUCT],
                    $file[FileScopeInterface::CATEGORY_ID],
                    $storeId
                );
                if (!$fileStoreProductCategory) {
                    $fileStoreProductCategory = [];
                }

                foreach (RegistryConstants::USE_DEFAULT_FIELDS as $field) {
                    if ($file[$field . '_use_defaults'] === 'true' || $file[$field . '_use_defaults'] === '1') {
                        $fileStoreProductCategory[$field] = null;
                    } elseif ($field === 'customer_groups') {
                        $fileStoreProductCategory[$field] = $file[$field . '_output'];
                    } else {
                        $fileStoreProductCategory[$field] = $file[$field];
                    }
                }
                $fileStoreProductCategory[FileScopeInterface::FILE_ID] = (int)$file[FileScopeInterface::FILE_ID];
                $fileStoreProductCategory[FileScopeInterface::POSITION] = isset($file[FileScopeInterface::POSITION]) ?
                    (int)$file[FileScopeInterface::POSITION]
                    : 0;
                $fileStoreProductCategory[FileScopeInterface::PRODUCT_ID] = $params[RegistryConstants::PRODUCT];
                $fileStoreProductCategory[FileScopeInterface::PRODUCT_ID] = $params[RegistryConstants::PRODUCT];
                $fileStoreProductCategory[FileScopeInterface::CATEGORY_ID] = $file[FileScopeInterface::CATEGORY_ID];
                $fileStoreProductCategory[FileScopeInterface::STORE_ID] = $storeId;

                $this->fileStoreCategoryProduct->saveProductCategoryStoreFile($fileStoreProductCategory);
            }
        }
    }
}
