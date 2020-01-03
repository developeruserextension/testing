<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\File\FileScope\SaveProcessors;

use Amasty\ProductAttachment\Api\Data\FileInterface;
use Amasty\ProductAttachment\Api\Data\FileScopeInterface;
use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Amasty\ProductAttachment\Model\SourceOptions\AttachmentType;

class Category implements FileScopeSaveProcessorInterface
{
    /**
     * @var \Amasty\ProductAttachment\Model\FileFactory
     */
    private $fileFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Amasty\ProductAttachment\Model\File\FileScope\ResourceModel\FileStoreCategory
     */
    private $fileStoreCategory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Amasty\ProductAttachment\Model\File\Repository
     */
    private $fileRepository;

    public function __construct(
        \Amasty\ProductAttachment\Model\File\FileFactory $fileFactory,
        \Amasty\ProductAttachment\Model\File\FileScope\ResourceModel\FileStoreCategory $fileStoreCategory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\RequestInterface $request,
        \Amasty\ProductAttachment\Model\File\Repository\Proxy $fileRepository
    ) {
        $this->fileFactory = $fileFactory;
        $this->messageManager = $messageManager;
        $this->fileStoreCategory = $fileStoreCategory;
        $this->request = $request;
        $this->fileRepository = $fileRepository;
    }

    /**
     * @param \Amasty\ProductAttachment\Api\Data\FileInterface $params
     *
     * @return array|void
     */
    public function execute($params)
    {
        $storeId = isset($params[RegistryConstants::STORE]) ? (int)$params[RegistryConstants::STORE]
            : (int)$this->request->getParam('store');

        $toDelete = [];
        if (!empty($params[RegistryConstants::TO_DELETE])) {
            $toDelete = $params[RegistryConstants::TO_DELETE];
        }

        if ($files = $params[RegistryConstants::FILES]) {
            foreach ($files as $file) {
                if (!empty($file['file']) || !empty($file['link'])) {
                    /** @var \Amasty\ProductAttachment\Model\File\File $newFile */
                    $newFile = $this->fileFactory->create();

                    if (!empty($file['file'])) {
                        $tmpFile = [];
                        $tmpFile[0]['file'] = $file['file'];
                        $tmpFile[0]['tmp_name'] = $tmpFile[0]['name'] = true;
                        $file[RegistryConstants::FILE_KEY] = $tmpFile;
                        $file[FileInterface::ATTACHMENT_TYPE] = AttachmentType::FILE;
                    } else {
                        $file[FileInterface::ATTACHMENT_TYPE] = AttachmentType::LINK;
                    }

                    $file[FileInterface::CATEGORIES] = [$params[RegistryConstants::CATEGORY]];
                    $file[FileInterface::FILE_ID] = null;
                    $newFile->addData($file);
                    try {
                        $this->fileRepository->saveAll($newFile, [RegistryConstants::STORE => $storeId]);
                    } catch (\Magento\Framework\Exception\CouldNotSaveException $e) {
                        $this->messageManager->addErrorMessage(__('Couldn\'t save file'));
                    }
                } else {
                    unset($toDelete[$file[FileScopeInterface::FILE_ID]]);
                    $fileStoreCategory = $this->fileStoreCategory->getCategoryStoreFile(
                        $file[FileScopeInterface::FILE_ID],
                        $params[RegistryConstants::CATEGORY],
                        $storeId
                    );
                    if (!$fileStoreCategory) {
                        $fileStoreCategory = [];
                    }

                    foreach (RegistryConstants::USE_DEFAULT_FIELDS as $field) {
                        if ($file[$field . '_use_defaults'] === 'true') {
                            $fileStoreCategory[$field] = null;
                        } elseif ($field === 'customer_groups') {
                            $fileStoreCategory[$field] = $file[$field . '_output'];
                        } else {
                            $fileStoreCategory[$field] = $file[$field];
                        }
                    }
                    $fileStoreCategory[FileScopeInterface::POSITION] = (int)$file[FileScopeInterface::POSITION];
                    $fileStoreCategory[FileScopeInterface::CATEGORY_ID] = $params[RegistryConstants::CATEGORY];
                    $fileStoreCategory[FileScopeInterface::FILE_ID] = $file[FileScopeInterface::FILE_ID];
                    $fileStoreCategory[FileScopeInterface::STORE_ID] = $storeId;
                    if ($storeId
                        && $this->fileStoreCategory->isAllStoreViewFile($file[FileScopeInterface::FILE_ID], $storeId)
                    ) {
                        $fileCategories = $this->fileStoreCategory->getStoreCategoryIdsByStoreId(
                            $file[FileScopeInterface::FILE_ID],
                            0
                        );
                        unset($fileCategories[$params[RegistryConstants::CATEGORY]]);
                        foreach ($fileCategories as $fileCategory) {
                            $fileCategory[FileScopeInterface::STORE_ID] = $storeId;
                            $fileCategory[FileScopeInterface::FILE_ID] = $file[FileScopeInterface::FILE_ID];
                            $this->fileStoreCategory->insertFileStoreCategoryData($fileCategory);
                        }
                    }
                    $this->fileStoreCategory->saveFileStoreCategory($fileStoreCategory);
                }
            }
        }

        if (!empty($toDelete)) {
            foreach (array_keys($toDelete) as $fileId) {
                if (!$storeId) {
                    $this->fileStoreCategory->deleteFileByStoreCategory(
                        $fileId,
                        $params[RegistryConstants::CATEGORY],
                        $storeId
                    );
                } else {
                    $isAllStoreViewFile = $this->fileStoreCategory->isAllStoreViewFile($fileId, $storeId);
                    if ($isAllStoreViewFile) {
                        $fileCategories = $this->fileStoreCategory->getStoreCategoryIdsByStoreId(
                            $fileId,
                            0
                        );
                        unset($fileCategories[$params[RegistryConstants::CATEGORY]]);
                        if ($fileCategories) {
                            foreach ($fileCategories as $fileCategory) {
                                $fileCategory[FileScopeInterface::STORE_ID] = $storeId;
                                $fileCategory[FileScopeInterface::FILE_ID] = $fileId;
                                $this->fileStoreCategory->insertFileStoreCategoryData($fileCategory);
                            }
                        } else {
                            $this->fileStoreCategory->insertFileStoreCategoryData([
                                FileScopeInterface::STORE_ID => $storeId,
                                FileScopeInterface::FILE_ID => $fileId,
                                FileScopeInterface::CATEGORY_ID => 0
                            ]);
                        }
                    } else {
                        $this->fileStoreCategory->deleteFileByStoreCategory(
                            $fileId,
                            $params[RegistryConstants::CATEGORY],
                            $storeId
                        );
                    }
                }
            }
        }
    }
}
