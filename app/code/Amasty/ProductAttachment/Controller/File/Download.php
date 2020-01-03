<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Controller\File;

use Amasty\ProductAttachment\Api\Data\FileInterface;
use Amasty\ProductAttachment\Api\FileRepositoryInterface;
use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Amasty\ProductAttachment\Model\ConfigProvider;
use Amasty\ProductAttachment\Model\File\FileScope\FileScopeDataProvider;
use Amasty\ProductAttachment\Model\Filesystem\Directory;
use Amasty\ProductAttachment\Model\Report\ItemFactory;
use Amasty\ProductAttachment\Model\SourceOptions\DownloadSource;
use Amasty\ProductAttachment\Model\SourceOptions\OrderFilterType;
use Amasty\ProductAttachment\Model\SourceOptions\UrlType;
use Magento\Customer\Model\Session;
use Magento\Downloadable\Helper\Download as DownloadHelper;
use Magento\Framework\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\StoreManagerInterface;

class Download extends Action\Action
{
    /**
     * @var FileScopeDataProvider
     */
    private $fileScopeDataProvider;

    /**
     * @var FileRepositoryInterface
     */
    private $fileRepository;

    /**
     * @var DownloadHelper
     */
    private $downloadHelper;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var ItemFactory
     */
    private $reportItemFactory;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        FileRepositoryInterface $fileRepository,
        FileScopeDataProvider $fileScopeDataProvider,
        DownloadHelper $downloadHelper,
        ConfigProvider $configProvider,
        StoreManagerInterface $storeManager,
        ItemFactory $reportItemFactory,
        Session $customerSession,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->fileScopeDataProvider = $fileScopeDataProvider;
        $this->fileRepository = $fileRepository;
        $this->downloadHelper = $downloadHelper;
        $this->configProvider = $configProvider;
        $this->reportItemFactory = $reportItemFactory;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
    }

    /**
     * @param FileInterface $file
     */
    public function processFile($file)
    {
        if ($file->getAttachmentType() == \Amasty\ProductAttachment\Model\SourceOptions\AttachmentType::FILE) {
            $this->downloadHelper->setResource(
                Directory::DIRECTORY_CODES[Directory::ATTACHMENT] . DIRECTORY_SEPARATOR . $file->getFilePath(),
                DownloadHelper::LINK_TYPE_FILE
            );
        } else {
            $this->downloadHelper->setResource(
                $file->getLink(),
                DownloadHelper::LINK_TYPE_URL
            );
        }
        if ($this->configProvider->detectMimeType() && !empty($file->getMimeType())) {
            $contentType = $file->getMimeType();
        } else {
            $contentType = 'application/octet-stream';
        }

        $this->getResponse()->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true);

        if ($file->getAttachmentType() == \Amasty\ProductAttachment\Model\SourceOptions\AttachmentType::FILE) {
            $this->getResponse()->setHeader('Content-Length', $file->getFileSize());
        } else {
            if ($fileSize = $this->downloadHelper->getFileSize()) {
                $this->getResponse()->setHeader('Content-Length', $fileSize);
            }
        }

        if ($contentDisposition = $this->downloadHelper->getContentDisposition()) {
            $this->getResponse()->setHeader(
                'Content-Disposition',
                $contentDisposition . '; filename=' . $file->getFileName() . '.' . $file->getFileExtension()
            );
        }

        $this->getResponse()->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
        $this->getResponse()->clearBody();
        $this->getResponse()->sendHeaders();
        $this->downloadHelper->output();
        /** @codingStandardsIgnoreStart */
        exit(0);
        /** @codingStandardsIgnoreEnd */
    }

    public function execute()
    {
        $fileId = $this->getRequest()->getParam('file', 0);
        if ($fileId) {
            try {
                if ($this->configProvider->getUrlType() === UrlType::ID) {
                    $file = $this->fileRepository->getById((int)$fileId);
                } else {
                    $file = $this->fileRepository->getByHash($fileId);
                }

                $params = [
                    RegistryConstants::STORE => $this->storeManager->getStore()->getId(),
                    RegistryConstants::FILE => $file,
                    RegistryConstants::INCLUDE_FILTER => OrderFilterType::ALL_ATTACHMENTS
                ];
                if ($categoryId = $this->getRequest()->getParam('category')) {
                    $params[RegistryConstants::CATEGORY] = (int)$categoryId;
                } elseif ($productId = $this->getRequest()->getParam('product')) {
                    $params[RegistryConstants::PRODUCT] = (int)$productId;
                }
                $file = $this->fileScopeDataProvider->execute($params, 'downloadFile');

                if ($file) {
                    $this->saveStat();
                    try {
                        $this->processFile($file);
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        null;
                    }
                }
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                null;
            }
        }

        return $this->resultFactory->create(ResultFactory::TYPE_FORWARD)->forward('noroute');
    }

    public function saveStat()
    {
        /** @var \Amasty\ProductAttachment\Model\Report\Item $reportItem */
        $reportItem = $this->reportItemFactory->create();
        $reportItem->setFileId($this->getRequest()->getParam('file'))
            ->setStoreId($this->storeManager->getStore()->getId());

        if ($this->getRequest()->getParam('category')) {
            $reportItem->setCategoryId($this->getRequest()->getParam('category'))
                ->setDownloadSource(DownloadSource::CATEGORY);
        } elseif ($this->getRequest()->getParam('product')) {
            $reportItem->setProductId($this->getRequest()->getParam('product'))
                ->setDownloadSource(DownloadSource::PRODUCT);
        } elseif ($this->getRequest()->getParam('order')) {
            $reportItem->setOrderId($this->getRequest()->getParam('order'))
                ->setDownloadSource(DownloadSource::ORDER);
        } else {
            $reportItem->setDownloadSource(DownloadSource::OTHER);
        }
        $reportItem->setCustomerId($this->customerSession->getCustomerId());

        $reportItem->save();
    }
}
