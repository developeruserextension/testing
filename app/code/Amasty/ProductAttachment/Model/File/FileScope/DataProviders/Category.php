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
use Amasty\ProductAttachment\Model\File\FileScope\ResourceModel\FileStoreCategory;
use Amasty\ProductAttachment\Model\Icon\GetIconForFile;

class Category implements FileScopeDataInterface
{
    /**
     * @var GetIconForFile
     */
    private $getIconForFile;

    /**
     * @var FileStoreCategory
     */
    private $fileStoreCategory;

    public function __construct(
        GetIconForFile $getIconForFile,
        FileStoreCategory $fileStoreCategory
    ) {
        $this->getIconForFile = $getIconForFile;
        $this->fileStoreCategory = $fileStoreCategory;
    }

    /**
     * @inheritdoc
     */
    public function execute($params)
    {
        $categoryId = $params[RegistryConstants::CATEGORY];
        $store = $params[RegistryConstants::STORE];

        $fileStoreCategories = $this->fileStoreCategory->getFilesIdsByStoreId($categoryId, $store);

        $result = [];
        if (!empty($fileStoreCategories)) {
            foreach ($fileStoreCategories as $category) {
                if (!empty($result[$category[FileScopeInterface::FILE_ID]])) {
                    continue;
                }
                $row = [];
                $row[FileScopeInterface::FILE_ID] = $row['show_file_id'] = $category[FileScopeInterface::FILE_ID];
                $row['icon'] = $this->getIconForFile->byFileExtension($category[FileInterface::EXTENSION]);
                $row[FileInterface::EXTENSION] = $category[FileInterface::EXTENSION];
                $row[FileScopeInterface::POSITION] = $category[FileScopeInterface::POSITION];
                foreach (RegistryConstants::USE_DEFAULT_FIELDS as $field) {
                    if ($category[$field] === null || ($store && empty($category[FileScopeInterface::STORE_ID]))) {
                        $row[$field . '_use_defaults'] = 1;
                        if ($category['default_' . $field] !== null) {
                            $row[$field] = $category['default_' . $field];
                        } elseif (isset($category['cat0_default_' . $field])
                            && $category['cat0_default_' . $field] !== null
                        ) {
                            $row[$field] = $category['cat0_default_' . $field];
                        } else {
                            $row[$field] = $category['super_default_' . $field];
                        }
                    } else {
                        $row[$field] = $category[$field];
                        $row[$field . '_use_defaults'] = 0;
                    }
                }

                $result[$row[FileScopeInterface::FILE_ID]] = $row;
            }
        }

        return array_merge($result);
    }
}
