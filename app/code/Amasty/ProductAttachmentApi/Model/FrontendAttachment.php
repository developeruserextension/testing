<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachmentApi
 */


namespace Amasty\ProductAttachmentApi\Model;

use Amasty\ProductAttachmentApi\Api\FrontendAttachmentInterface;
use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Amasty\ProductAttachment\Model\File\FileScope\FileScopeDataProviderInterface;
use Amasty\ProductAttachment\Model\SourceOptions\OrderFilterType;
use Magento\Store\Model\StoreManagerInterface;

class FrontendAttachment implements FrontendAttachmentInterface
{
    /**
     * @var FileScopeDataProviderInterface
     */
    private $fileScopeDataProvider;

    /**
     * @var array
     */
    private $params = [];

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var File\FrontendFileFactory
     */
    private $frontendFileFactory;

    public function __construct(
        FileScopeDataProviderInterface $fileScopeDataProvider,
        File\FrontendFileFactory $frontendFileFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->fileScopeDataProvider = $fileScopeDataProvider;
        $this->params = [];
        $this->storeManager = $storeManager;
        $this->frontendFileFactory = $frontendFileFactory;
    }

    /**
     * @inheritdoc
     */
    public function getByProductId(
        $productId,
        $extraUrlParams = [],
        $includeInOrderOnly = false,
        $amastyCustomerGroup = 0
    ) {
        $this->params[RegistryConstants::CUSTOMER_GROUP] = $amastyCustomerGroup;
        $this->params[RegistryConstants::PRODUCT] = $productId;
        $this->params[RegistryConstants::STORE] = $this->storeManager->getStore()->getId();

        if ($includeInOrderOnly) {
            $this->params[RegistryConstants::INCLUDE_FILTER] = OrderFilterType::INCLUDE_IN_ORDER_ONLY;
        }

        if (!empty($extraUrlParams)) {
            $this->params[RegistryConstants::EXTRA_URL_PARAMS] = $extraUrlParams;
        }

        return $this->processAttachments(
            $this->fileScopeDataProvider->execute(
                $this->params,
                'frontendProduct'
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function getByCategoryId(
        $categoryId,
        $extraUrlParams = [],
        $includeInOrderOnly = false,
        $amastyCustomerGroup = 0
    ) {
        $this->params[RegistryConstants::CUSTOMER_GROUP] = $amastyCustomerGroup;
        $this->params[RegistryConstants::CATEGORY] = $categoryId;
        $this->params[RegistryConstants::STORE] = $this->storeManager->getStore()->getId();

        if ($includeInOrderOnly) {
            $this->params[RegistryConstants::INCLUDE_FILTER] = OrderFilterType::INCLUDE_IN_ORDER_ONLY;
        }

        if (!empty($extraUrlParams)) {
            $this->params[RegistryConstants::EXTRA_URL_PARAMS] = $extraUrlParams;
        }

        return $this->processAttachments(
            $this->fileScopeDataProvider->execute(
                $this->params,
                'frontendCategory'
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function getByFileIds(
        $fileIds,
        $extraUrlParams = [],
        $includeInOrderOnly = false,
        $amastyCustomerGroup = 0
    ) {
        $this->params[RegistryConstants::CUSTOMER_GROUP] = $amastyCustomerGroup;
        $this->params[RegistryConstants::FILE_IDS] = $fileIds;
        $this->params[RegistryConstants::STORE] = $this->storeManager->getStore()->getId();

        if ($includeInOrderOnly) {
            $this->params[RegistryConstants::INCLUDE_FILTER] = OrderFilterType::INCLUDE_IN_ORDER_ONLY;
        }

        if (!empty($extraUrlParams)) {
            $this->params[RegistryConstants::EXTRA_URL_PARAMS] = $extraUrlParams;
        }

        return $this->processAttachments(
            $this->fileScopeDataProvider->execute(
                $this->params,
                'fileIds'
            )
        );
    }

    /**
     * @param \Amasty\ProductAttachmentApi\Api\Data\FrontendFileInterface[] $attachments
     *
     * @return \Amasty\ProductAttachmentApi\Api\Data\FrontendFileInterface[]
     */
    private function processAttachments($attachments)
    {
        $result = [];
        /** @var \Amasty\ProductAttachment\Api\Data\FileInterface $file */
        foreach ($attachments as $file) {
            /** @var File\FrontendFile $frontEndFile */
            $frontEndFile = $this->frontendFileFactory->create();
            $frontEndFile->setData($file->getData());
            $frontEndFile->setUrl($file->getFrontendUrl());
            $frontEndFile->setFileName($file->getFileName());
            $result[] = $frontEndFile;
        }

        return $result;
    }
}
