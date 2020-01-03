<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Product\DataProvider\Modifiers;

use Amasty\ProductAttachment\Api\Data\FileScopeInterface;
use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Amasty\ProductAttachment\Model\ConfigProvider;
use Amasty\ProductAttachment\Model\File\FileScope\FileScopeDataProviderInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;

class Data
{
    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var FileScopeDataProviderInterface
     */
    private $fileScopeDataProvider;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        LocatorInterface $locator,
        FileScopeDataProviderInterface $fileScopeDataProvider,
        ConfigProvider $configProvider
    ) {
        $this->locator = $locator;
        $this->fileScopeDataProvider = $fileScopeDataProvider;
        $this->configProvider = $configProvider;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function execute(array $data)
    {
        $data[$this->locator->getProduct()->getId()]['attachments']['files'] = $this->fileScopeDataProvider->execute(
            [
                RegistryConstants::STORE => $this->locator->getStore()->getId(),
                RegistryConstants::PRODUCT => $this->locator->getProduct()->getId()
            ],
            'product'
        );
        if ($this->configProvider->addCategoriesFilesToProducts()) {
            $productFileIds = [];
            foreach ($data[$this->locator->getProduct()->getId()]['attachments']['files'] as $file) {
                $productFileIds[] = $file[FileScopeInterface::FILE_ID];
            }

            $data[$this->locator->getProduct()->getId()]['categories_attachments']['categories_files'] =
                $this->fileScopeDataProvider->execute(
                    [
                        RegistryConstants::STORE => $this->locator->getStore()->getId(),
                        RegistryConstants::PRODUCT => $this->locator->getProduct()->getId(),
                        RegistryConstants::PRODUCT_CATEGORIES => $this->locator->getProduct()->getCategoryIds(),
                        RegistryConstants::EXCLUDE_FILES => $productFileIds
                    ],
                    'productCategories'
                );
        }

        return $data;
    }
}
