<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\File;

use Amasty\ProductAttachment\Api\Data\FileInterface;
use Amasty\ProductAttachment\Api\FileRepositoryInterface;
use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Amasty\ProductAttachment\Model\File\FileScope\SaveFileScopeInterface;
use Amasty\ProductAttachment\Model\File\FileFactory;
use Amasty\ProductAttachment\Model\Filesystem\Directory;
use Amasty\ProductAttachment\Model\Filesystem\File as FileSystemFile;
use Amasty\ProductAttachment\Model\Icon\ResourceModel\Icon;
use Magento\Downloadable\Helper\DownloadFactory as DownloadHelperFactory;
use Magento\Downloadable\Helper\Download as DownloadHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;

class Repository implements FileRepositoryInterface
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var ResourceModel\File
     */
    private $fileResource;

    /**
     * @var FileInterface[]
     */
    private $files;

    /**
     * @var SaveFileScopeInterface
     */
    private $saveFileStore;

    /**
     * @var FileSystemFile
     */
    private $file;

    /**
     * @var Icon
     */
    private $iconResource;

    /**
     * @var DownloadHelperFactory
     */
    private $downloadHelperFactory;

    public function __construct(
        FileFactory $fileFactory,
        ResourceModel\File $fileResource,
        SaveFileScopeInterface $saveFileStore,
        Icon\Proxy $iconResource,
        DownloadHelperFactory $downloadHelperFactory,
        FileSystemFile $file
    ) {
        $this->fileFactory = $fileFactory;
        $this->fileResource = $fileResource;
        $this->saveFileStore = $saveFileStore;
        $this->file = $file;
        $this->iconResource = $iconResource;
        $this->downloadHelperFactory = $downloadHelperFactory;
    }

    /**
     * Save file.
     *
     * @param \Amasty\ProductAttachment\Api\Data\FileInterface $file
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return \Amasty\ProductAttachment\Api\Data\FileInterface
     */
    public function save(\Amasty\ProductAttachment\Api\Data\FileInterface $file)
    {
        try {
            if ($file->getFileId()) {
                $file = $this->getById($file->getFileId())->addData($file->getData());
            }

            $this->fileResource->save($file);
            unset($this->files[$file->getFileId()]);
        } catch (\Exception $e) {
            if ($file->getFileId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save file with ID %1. Error: %2',
                        [$file->getFileId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new file. Error: %1', $e->getMessage()));
        }

        return $file;
    }

    /**
     * @inheritdoc
     */
    public function saveAll(\Amasty\ProductAttachment\Api\Data\FileInterface $file, $params = [], $checkExtension = true)
    {
        try {
            $downloadHelper = $this->downloadHelperFactory->create();
            $data = $file->getData();
            if ($file->getAttachmentType() == \Amasty\ProductAttachment\Model\SourceOptions\AttachmentType::FILE) {
                if (isset($data[RegistryConstants::FILE_KEY]) && is_array($data[RegistryConstants::FILE_KEY])) {
                    if (isset($data[RegistryConstants::FILE_KEY][0]['name'])
                        && isset($data[RegistryConstants::FILE_KEY][0]['tmp_name'])
                    ) {
                        $uploadFileData = $this->file->getUploadFileData();
                        $uploadFileData->setTmpFileName($data[RegistryConstants::FILE_KEY][0]['file']);
                        if ($this->file->save(
                            $uploadFileData,
                            \Amasty\ProductAttachment\Model\Filesystem\Directory::ATTACHMENT,
                            $checkExtension
                        )) {
                            $data[FileInterface::FILE_PATH] = $uploadFileData->getFileName();
                            $data[FileInterface::SIZE] = $uploadFileData->getFileSize();
                            $data[FileInterface::EXTENSION] = $uploadFileData->getExtension();
                            $data[FileInterface::MIME_TYPE] = $uploadFileData->getMimeType();
                        } else {
                            $data[FileInterface::FILE_PATH] = '';
                        }
                    }
                } else {
                    $data[FileInterface::FILE_PATH] = '';
                }
            } else {
                if ($this->needToCheckUrl($file)) {
                    $downloadHelper->setResource($file->getLink(), DownloadHelper::LINK_TYPE_URL);
                    try {
                        $fileName = $downloadHelper->getFilename();
                        /** @codingStandardsIgnoreStart */
                        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                        /** @codingStandardsIgnoreEnd */

                        if (!in_array($extension, $this->iconResource->getAllowedExtensions()) && $checkExtension) {
                            throw new LocalizedException(__('Disallowed Extension'));
                        }
                        $data[FileInterface::EXTENSION] = $extension;
                        $data[FileInterface::SIZE] = $downloadHelper->getFileSize();
                        $data[FileInterface::MIME_TYPE] = $downloadHelper->getContentType();
                    } catch (\Exception $e) {
                        throw new CouldNotSaveException(__('Unable to save new file. Error: %1', $e->getMessage()));
                    }
                }
            }

            $file->addData($data);
            if (!$file->getFilePath()) {
                $file->unsetData(FileInterface::FILE_PATH);
            }

            if ($file->getFileId()) {
                $file = $this->getById($file->getFileId())->addData($file->getData());
            }

            if (empty($file->getUrlHash())) {
                $file->setUrlHash(md5(mt_rand()));
            }

            $this->fileResource->save($file);
            $this->saveFileStore->execute(array_merge($params, [RegistryConstants::FILE => $file]), 'file');
            unset($this->files[$file->getFileId()]);
        } catch (\Exception $e) {
            if ($file->getFileId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save file with ID %1. Error: %2',
                        [$file->getFileId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new file. Error: %1', $e->getMessage()));
        }

        return $file;
    }

    /**
     * @param FileInterface $file
     *
     * @return bool
     *
     * @throws NoSuchEntityException
     */
    public function needToCheckUrl($file)
    {
        if (!$file->getFileId()) {
            return true;
        }

        if (($origFile = $this->getById($file->getFileId())) && ($origFile->getLink() != $file->getLink())) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getById($fileId)
    {
        if (!isset($this->files[$fileId])) {
            /** @var \Amasty\ProductAttachment\Model\File\File $file */
            $file = $this->fileFactory->create();
            $this->fileResource->load($file, $fileId);
            if (!$file->getFileId()) {
                throw new NoSuchEntityException(__('File with specified ID "%1" not found.', $fileId));
            }
            $this->files[$fileId] = $file;
        }

        return $this->files[$fileId];
    }

    /**
     * @inheritdoc
     */
    public function getByHash($hash)
    {
        /** @var \Amasty\ProductAttachment\Model\File\File $file */
        $file = $this->fileFactory->create();
        $this->fileResource->load($file, $hash, FileInterface::URL_HASH);
        if (!$file->getFileId()) {
            throw new NoSuchEntityException(__('File with specified Hash "%1" not found.', $hash));
        }
        $this->files[$file->getFileId()] = $file;

        return $file;
    }

    /**
     * Retrieve files matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Amasty\ProductAttachment\Api\Data\FileInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        // TODO: Implement getList() method.
    }

    /**
     * Delete file.
     *
     * @param \Magento\Framework\Model\AbstractModel|\Amasty\ProductAttachment\Api\Data\FileInterface $file
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Amasty\ProductAttachment\Api\Data\FileInterface $file)
    {
        try {
            $this->file->deleteFile($file->getFilePath(), Directory::ATTACHMENT);
            $this->fileResource->delete($file);
            unset($this->files[$file->getFileId()]);
        } catch (\Exception $e) {
            if ($file->getFileId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove file with ID %1. Error: %2',
                        [$file->getFileId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove icon. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * Delete file by ID.
     *
     * @param int $fileId
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($fileId)
    {
        if (!($file = $this->getById($fileId))) {
            throw new NoSuchEntityException(__('File with specified ID "%1" not found.', $fileId));
        } else {
            $this->delete($file);

            return true;
        }
    }
}
