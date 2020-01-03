<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachmentApi
 */


namespace Amasty\ProductAttachmentApi\Model;

use Amasty\ProductAttachment\Api\FileRepositoryInterface;
use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Amasty\ProductAttachmentApi\Model\File\FileContentFactory;
use Amasty\ProductAttachment\Model\File\FileScope\FileScopeDataProviderInterface;
use Amasty\ProductAttachment\Model\SourceOptions\OrderFilterType;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Amasty\ProductAttachment\Model\Filesystem\Directory;
use Magento\Store\Model\StoreManagerInterface;

class DownloadFile implements \Amasty\ProductAttachmentApi\Api\DownloadFileInterface
{
    /**
     * @var FileScopeDataProviderInterface
     */
    private $fileScopeDataProvider;

    /**
     * @var FileRepositoryInterface
     */
    private $fileRepository;

    /**
     * @var FileContentFactory
     */
    private $fileContentFactory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    private $mediaDirectory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        FileScopeDataProviderInterface $fileScopeDataProvider,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        FileRepositoryInterface $fileRepository,
        FileContentFactory $fileContentFactory
    ) {
        $this->fileScopeDataProvider = $fileScopeDataProvider;
        $this->fileRepository = $fileRepository;
        $this->fileContentFactory = $fileContentFactory;

        $this->mediaDirectory = $filesystem->getDirectoryRead(
            \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
        );
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function download($fileId, $productId = 0, $categoryId = 0, $amastyCustomerGroup = 0)
    {
        $file = $this->fileRepository->getById($fileId);

        $params = [
            RegistryConstants::CUSTOMER_GROUP => $amastyCustomerGroup,
            RegistryConstants::STORE => $this->storeManager->getStore()->getId(),
            RegistryConstants::FILE => $file,
            RegistryConstants::INCLUDE_FILTER => OrderFilterType::ALL_ATTACHMENTS
        ];
        if ($categoryId) {
            $params[RegistryConstants::CATEGORY] = $categoryId;
        } elseif ($productId) {
            $params[RegistryConstants::PRODUCT] = $productId;
        }
        /** @var \Amasty\ProductAttachment\Api\Data\FileInterface $file */
        $file = $this->fileScopeDataProvider->execute($params, 'downloadFile');
        if ($file) {
            /** @var \Amasty\ProductAttachmentApi\Model\File\FileContent $fileContent */
            $fileContent = $this->fileContentFactory->create();
            $fileContent->setNameWithExtension($file->getFileName() . '.' . $file->getFileExtension())
                ->setBase64EncodedData(
                    base64_encode(
                        $this->mediaDirectory->readFile(
                            Directory::DIRECTORY_CODES[Directory::ATTACHMENT]
                                . DIRECTORY_SEPARATOR . $file->getFilePath()
                        )
                    )
                );

            return $fileContent;
        }
        throw new LocalizedException(__('File not found'));
    }
}
