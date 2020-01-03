<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Import;

use Amasty\ProductAttachment\Api\Data\FileInterface;
use Amasty\ProductAttachment\Model\Filesystem\Directory;
use Amasty\ProductAttachment\Model\Filesystem\UploadFileDataFactory;
use Amasty\ProductAttachment\Model\Import\ResourceModel\ImportFileCollection;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class Repository
{
    /**
     * @var ImportFactory
     */
    private $importFactory;

    /**
     * @var ResourceModel\Import
     */
    private $importResource;

    /**
     * @var array
     */
    private $imports = [];

    /**
     * @var ImportFileFactory
     */
    private $importFileFactory;

    /**
     * @var \Amasty\ProductAttachment\Model\Filesystem\File
     */
    private $moveFile;

    /**
     * @var ResourceModel\ImportFile
     */
    private $importFileResource;

    /**
     * @var ResourceModel\ImportFileCollectionFactory
     */
    private $importFileCollectionFactory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    public function __construct(
        \Amasty\ProductAttachment\Model\Import\ImportFactory $importFactory,
        \Amasty\ProductAttachment\Model\Import\ImportFileFactory $importFileFactory,
        \Amasty\ProductAttachment\Model\Filesystem\File $moveFile,
        \Amasty\ProductAttachment\Model\Import\ResourceModel\ImportFile $importFileResource,
        \Amasty\ProductAttachment\Model\Import\ResourceModel\ImportFileCollectionFactory $importFileCollectionFactory,
        \Amasty\ProductAttachment\Model\Import\ResourceModel\Import $importResource,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->importFactory = $importFactory;
        $this->importResource = $importResource;
        $this->importFileFactory = $importFileFactory;
        $this->moveFile = $moveFile;
        $this->importFileResource = $importFileResource;
        $this->importFileCollectionFactory = $importFileCollectionFactory;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    /**
     * @param $importId
     *
     * @throws NoSuchEntityException
     * @return \Amasty\ProductAttachment\Model\Import\Import
     */
    public function getById($importId)
    {
        if (!isset($this->imports[$importId])) {
            /** @var \Amasty\ProductAttachment\Model\Import\Import $import*/
            $import = $this->importFactory->create();
            $this->importResource->load($import, $importId);
            if (!$import->getImportId()) {
                throw new NoSuchEntityException(__('Import with specified ID "%1" not found.', $importId));
            }
            $this->imports[$importId] = $import;
        }

        return $this->imports[$importId];
    }

    /**
     * @param $importId
     *
     * @throws NoSuchEntityException
     * @return \Amasty\ProductAttachment\Model\Import\ImportFile[]
     */
    public function getImportFilesByImportId($importId)
    {
        /** @var \Amasty\ProductAttachment\Model\Import\ResourceModel\ImportFileCollection $filesCollection */
        $filesCollection = $this->importFileCollectionFactory->create();
        $filesCollection->addFieldToFilter(ImportFile::IMPORT_ID, (int)$importId);

        return $filesCollection->getItems();
    }

    public function save(\Amasty\ProductAttachment\Model\Import\Import $import)
    {
        try {
            if ($import->getImportId()) {
                $import = $this->getById($import->getImportId())->addData($import->getData());
            }
            $import->setStoreIds($import->getStoreIds());
            $this->importResource->save($import);
            unset($this->imports[$import->getIconId()]);
        } catch (\Exception $e) {
            if ($import->getImportId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save import with ID %1. Error: %2',
                        [$import->getImportId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new import. Error: %1', $e->getMessage()));
        }

        return $import;
    }

    /**
     * @param int $importId
     * @param array $files
     */
    public function saveImportFiles($importId, $files)
    {
        /** @var \Amasty\ProductAttachment\Model\Import\ResourceModel\ImportFileCollection $toDeleteCollection */
        $toDeleteCollection = $this->importFileCollectionFactory->create();
        $toDeleteCollection->addFieldToFilter(ImportFile::IMPORT_ID, (int)$importId);
        $toDeleteCollection->addFieldToSelect(ImportFile::IMPORT_FILE_ID);
        $toDelete = [];
        foreach ($toDeleteCollection->getData() as $item) {
            $toDelete[$item[ImportFile::IMPORT_FILE_ID]] = 1;
        }

        foreach ($files as $file) {
            if (!empty($file['file']) || !empty($file['link'])) {
                /** @var \Amasty\ProductAttachment\Model\Import\ImportFile $newFile */
                $newFile = $this->importFileFactory->create();
                $uploadFileData = $this->moveFile->getUploadFileData();
                $uploadFileData->setTmpFileName($file['file']);
                try {
                    $this->moveFile->save(
                        $uploadFileData,
                        Directory::IMPORT,
                        true,
                        $importId
                    );
                    if ($file[FileInterface::CUSTOMER_GROUPS] !== ''
                        && is_array($file[FileInterface::CUSTOMER_GROUPS])) {
                        $customerGroups = implode(',', $file[FileInterface::CUSTOMER_GROUPS]);
                    } else {
                        $customerGroups = null;
                    }

                    $newFile->setImportId((int)$importId)
                        ->setFilePath($uploadFileData->getFileName() . '.' . $uploadFileData->getExtension())
                        ->setLabel($file[FileInterface::LABEL])
                        ->setFileName($file[FileInterface::FILENAME])
                        ->setIsIncludeInOrder($file[FileInterface::INCLUDE_IN_ORDER])
                        ->setCustomerGroups($customerGroups)
                        ->setIsVisible($file[FileInterface::IS_VISIBLE]);

                    $this->importFileResource->save($newFile);
                } catch (\Exception $e) {
                    null;
                }
            } elseif (!empty($file['filepath'])) {
                /** @var \Amasty\ProductAttachment\Model\Import\ImportFile $newFile */
                $newFile = $this->importFileFactory->create();
                $uploadFileData = $this->moveFile->getUploadFileData();
                $uploadFileData->setTmpFileName(
                    Directory::DIRECTORY_CODES[Directory::IMPORT_FTP] . DIRECTORY_SEPARATOR . $file['filepath']
                );
                try {
                    $this->moveFile->save(
                        $uploadFileData,
                        Directory::IMPORT,
                        true,
                        $importId
                    );
                    if ($file[FileInterface::CUSTOMER_GROUPS] !== ''
                        && is_array($file[FileInterface::CUSTOMER_GROUPS])) {
                        $customerGroups = implode(',', $file[FileInterface::CUSTOMER_GROUPS]);
                    } else {
                        $customerGroups = null;
                    }

                    $newFile->setImportId((int)$importId)
                        ->setFilePath($uploadFileData->getFileName() . '.' . $uploadFileData->getExtension())
                        ->setLabel($file[FileInterface::LABEL])
                        ->setFileName($file[FileInterface::FILENAME])
                        ->setIsIncludeInOrder($file[FileInterface::INCLUDE_IN_ORDER])
                        ->setCustomerGroups($customerGroups)
                        ->setIsVisible($file[FileInterface::IS_VISIBLE]);

                    $this->importFileResource->save($newFile);
                } catch (\Exception $e) {
                    null;
                }
            } elseif (!empty($file[FileInterface::FILE_ID])) {
                unset($toDelete[$file[FileInterface::FILE_ID]]);
                /** @var \Amasty\ProductAttachment\Model\Import\ImportFile $newFile */
                $newFile = $this->importFileFactory->create();
                $this->importFileResource->load($newFile, (int)$file[FileInterface::FILE_ID]);
                if ($newFile->getImportFileId()) {
                    $newFile->setLabel($file[FileInterface::LABEL])
                        ->setFileName($file[FileInterface::FILENAME])
                        ->setIsIncludeInOrder($file[FileInterface::INCLUDE_IN_ORDER])
                        ->setCustomerGroups($file[FileInterface::CUSTOMER_GROUPS . '_output'])
                        ->setIsVisible($file[FileInterface::IS_VISIBLE]);
                    $this->importFileResource->save($newFile);
                }
            }
        }
        if (!empty($toDelete)) {
            $this->importFileResource->deleteFiles($importId, array_keys($toDelete));
        }
    }

    /**
     * @param int $importId
     */
    public function deleteById($importId)
    {
        try {
            $import = $this->getById((int)$importId);
            $this->mediaDirectory->delete(
                Directory::DIRECTORY_CODES[Directory::IMPORT] . DIRECTORY_SEPARATOR . (int)$importId
            );
            $this->importResource->delete($import);
        } catch (\Exception $e) {
            null;
        }
    }
}
