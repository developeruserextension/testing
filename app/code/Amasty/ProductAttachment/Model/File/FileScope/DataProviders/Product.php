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
use Amasty\ProductAttachment\Model\File\FileScope\ResourceModel\FileStoreProduct;
use Amasty\ProductAttachment\Model\Icon\GetIconForFile;

class Product implements FileScopeDataInterface
{
    /**
     * @var GetIconForFile
     */
    private $getIconForFile;

    /**
     * @var FileStoreProduct
     */
    private $fileStoreProduct;

    public function __construct(
        GetIconForFile $getIconForFile,
        FileStoreProduct $fileStoreProduct
    ) {
        $this->getIconForFile = $getIconForFile;
        $this->fileStoreProduct = $fileStoreProduct;
    }

    /**
     * @inheritdoc
     */
    public function execute($params)
    {
        $productId = $params[RegistryConstants::PRODUCT];
        $store = $params[RegistryConstants::STORE];

        $fileStoreProducts = $this->fileStoreProduct->getFilesIdsByStoreId($productId, $store);

        $result = [];
        if (!empty($fileStoreProducts)) {
            foreach ($fileStoreProducts as $product) {
                if (!empty($result[$product[FileScopeInterface::FILE_ID]])) {
                    continue;
                }
                $row = [];
                $row[FileScopeInterface::FILE_ID] = $row['show_file_id'] = $product[FileScopeInterface::FILE_ID];
                $row['icon'] = $this->getIconForFile->byFileExtension($product[FileInterface::EXTENSION]);
                $row[FileInterface::EXTENSION] = $product[FileInterface::EXTENSION];
                foreach (RegistryConstants::USE_DEFAULT_FIELDS as $field) {
                    if ($product[$field] === null || ($store && empty($product[FileScopeInterface::STORE_ID]))) {
                        $row[$field . '_use_defaults'] = 1;
                        if ($product['default_' . $field] !== null) {
                            $row[$field] = $product['default_' . $field];
                        } elseif (isset($product['prod0_default_' . $field])
                            && $product['prod0_default_' . $field] !== null
                        ) {
                            $row[$field] = $product['prod0_default_' . $field];
                        } else {
                            $row[$field] = $product['super_default_' . $field];
                        }
                    } else {
                        $row[$field] = $product[$field];
                        $row[$field . '_use_defaults'] = 0;
                    }
                }

                $result[$row[FileScopeInterface::FILE_ID]] = $row;
            }
        }

        return array_merge($result);
    }
}
