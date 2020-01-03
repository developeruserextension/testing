<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\File\FileScope\DataProviders\Frontend;

use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;

class DownloadFile implements \Amasty\ProductAttachment\Model\File\FileScope\DataProviders\FileScopeDataInterface
{
    /**
     * @var File
     */
    private $fileDataProvider;

    /**
     * @var Category
     */
    private $categoryDataProvider;

    /**
     * @var Product
     */
    private $productDataProvider;

    public function __construct(
        File $fileDataProvider,
        Category\Proxy $categoryDataProvider,
        Product\Proxy $productDataProvider
    ) {
        $this->fileDataProvider = $fileDataProvider;
        $this->categoryDataProvider = $categoryDataProvider;
        $this->productDataProvider = $productDataProvider;
    }

    public function execute($params)
    {
        /** @var \Amasty\ProductAttachment\Api\Data\FileInterface $file */
        $file = $params[RegistryConstants::FILE];
        if (!empty($params[RegistryConstants::CATEGORY])) {
            $categoryFiles = $this->categoryDataProvider->execute($params);
            foreach ($categoryFiles as $categoryFile) {
                if ($categoryFile->getFileId() === $file->getFileId()) {
                    return $this->fileDataProvider->processFileParams($categoryFile, $params);
                }
            }
        } elseif (!empty($params[RegistryConstants::PRODUCT])) {
            $productFiles = $this->productDataProvider->execute($params);
            foreach ($productFiles as $productFile) {
                if ($productFile->getFileId() === $file->getFileId()) {
                    return $this->fileDataProvider->processFileParams($productFile, $params);
                }
            }
        }

        return $this->fileDataProvider->execute($params);
    }
}
