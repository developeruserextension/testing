<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\File\FileScope\DataProviders\Frontend;

use Amasty\ProductAttachment\Api\Data\FileScopeInterface;
use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Amasty\ProductAttachment\Model\ConfigProvider;
use Amasty\ProductAttachment\Model\File\FileScope\DataProviders\Product as ProductDataProvider;
use Amasty\ProductAttachment\Model\File\FileScope\DataProviders\ProductCategories;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Amasty\ProductAttachment\Model\File\Repository;

class Product implements \Amasty\ProductAttachment\Model\File\FileScope\DataProviders\FileScopeDataInterface
{
    /**
     * @var ProductDataProvider
     */
    private $productDataProvider;

    /**
     * @var File
     */
    private $fileDataProvider;

    /**
     * @var ProductCategories
     */
    private $productCategoriesDataProvider;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var Repository
     */
    private $fileRepository;

    public function __construct(
        ProductDataProvider $productDataProvider,
        ProductCategories\Proxy $productCategoriesDataProvider,
        ProductRepositoryInterface $productRepository,
        File $fileDataProvider,
        Repository\Proxy $fileRepository,
        ConfigProvider $configProvider
    ) {
        $this->productDataProvider = $productDataProvider;
        $this->fileDataProvider = $fileDataProvider;
        $this->productCategoriesDataProvider = $productCategoriesDataProvider;
        $this->configProvider = $configProvider;
        $this->productRepository = $productRepository;
        $this->fileRepository = $fileRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute($params)
    {
        $result = [];
        $fileIds = [];
        if ($productFiles = $this->productDataProvider->execute($params)) {
            foreach ($productFiles as $productFile) {
                /** @var \Amasty\ProductAttachment\Model\File\File $file */
                $file = $this->fileRepository->getById($productFile[FileScopeInterface::FILE_ID]);
                $file->addData($productFile);
                $fileIds[] = $file->getFileId();
                if ($file = $this->fileDataProvider->processFileParams($file, $params)) {
                    $result[] = $file;
                }
            }
        }

        if ($this->configProvider->addCategoriesFilesToProducts()) {
            $params[RegistryConstants::EXCLUDE_FILES] = $fileIds;
            if ($product = $this->productRepository->getById($params[RegistryConstants::PRODUCT])) {
                $params[RegistryConstants::PRODUCT_CATEGORIES] = $product->getCategoryIds();
                if ($productCategoriesFiles = $this->productCategoriesDataProvider->execute($params)) {
                    foreach ($productCategoriesFiles as $productCategoryFile) {
                        /** @var \Amasty\ProductAttachment\Model\File\File $file */
                        $file = $this->fileRepository->getById($productCategoryFile[FileScopeInterface::FILE_ID]);
                        $file->addData($productCategoryFile);
                        if ($file = $this->fileDataProvider->processFileParams($file, $params)) {
                            $result[] = $file;
                        }
                    }
                }
            }
        }

        return $result;
    }
}
