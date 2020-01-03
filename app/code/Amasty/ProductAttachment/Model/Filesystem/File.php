<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Filesystem;

use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;

class File
{
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var UploadFileDataFactory
     */
    private $uploadFileDataFactory;

    /**
     * @var \Magento\Downloadable\Helper\Download
     */
    private $downloadHelper;

    /**
     * @var \Amasty\ProductAttachment\Model\Icon\ResourceModel\Icon
     */
    private $iconResource;

    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Amasty\ProductAttachment\Model\Filesystem\UploadFileDataFactory $uploadFileDataFactory,
        \Magento\Downloadable\Helper\Download $downloadHelper,
        \Amasty\ProductAttachment\Model\Icon\ResourceModel\Icon $iconResource
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->uploadFileDataFactory = $uploadFileDataFactory;
        $this->downloadHelper = $downloadHelper;
        $this->iconResource = $iconResource;
    }

    /**
     * @param string $filename
     * @param string $directoryCode
     *
     * @return bool|string
     * @throws LocalizedException
     */
    public function getFilePath($filename, $directoryCode)
    {
        $this->checkDirectoryCode($directoryCode);

        $file = Directory::DIRECTORY_CODES[$directoryCode] . DIRECTORY_SEPARATOR . $filename;
        if (!$this->mediaDirectory->isExist($file)) {

            return false;
        }

        return $file;
    }

    /**
     * @param UploadFileData $fileData
     * @param string $directoryCode
     * @param bool $checkExtension
     * @param int $importId
     *
     * @return UploadFileData|bool
     * @throws LocalizedException
     */
    public function save(UploadFileData &$fileData, $directoryCode, $checkExtension = true, $importId = 0)
    {
        $this->checkDirectoryCode($directoryCode);

        mt_srand();
        /** @codingStandardsIgnoreStart */
        $ext = strtolower(pathinfo($fileData->getTmpFileName(), PATHINFO_EXTENSION));
        $newName = md5(mt_rand());
        /** @codingStandardsIgnoreEnd */
        $allowedExtensions = array_merge(
            $this->iconResource->getAllowedExtensions(),
            RegistryConstants::ICON_EXTENSIONS
        );

        if (!in_array($ext, $allowedExtensions) && $checkExtension) {
            throw new LocalizedException(__('Disallowed Extension'));
        }
        if ($directoryCode !== Directory::IMPORT) {
            $newPath = Directory::DIRECTORY_CODES[$directoryCode];
        } else {
            $newPath = Directory::DIRECTORY_CODES[$directoryCode] . DIRECTORY_SEPARATOR . (int)$importId;
        }

        $newNameWithPath = $newPath . DIRECTORY_SEPARATOR . $newName . '.' . $ext;

        try {
            if ($this->mediaDirectory->isFile($fileData->getTmpFileName())) {
                $oldPath = $fileData->getTmpFileName();
            } else {
                $oldPath = Directory::DIRECTORY_CODES[Directory::TMP_DIRECTORY]
                    . DIRECTORY_SEPARATOR . $fileData->getTmpFileName();
            }
            if (!$this->mediaDirectory->isDirectory($newPath)) {
                $this->mediaDirectory->create($newPath);
            }

            $this->mediaDirectory->copyFile(
                $oldPath,
                $newNameWithPath
            );
        } catch (\Magento\Framework\Exception\FileSystemException $e) {
            $this->mediaDirectory->delete($oldPath);

            return false;
        }
        $this->mediaDirectory->delete($oldPath);
        $fileStat = $this->mediaDirectory->stat($newNameWithPath);
        $fileData->setFileName($newName);
        $fileData->setExtension($ext);
        $fileData->setFileSize($fileStat['size']);
        $this->downloadHelper->setResource($newNameWithPath);
        $fileData->setMimeType($this->downloadHelper->getContentType());

        return $fileData;
    }

    public function deleteFile($file, $directoryCode)
    {
        $this->checkDirectoryCode($directoryCode);
        if ($this->mediaDirectory->isFile(Directory::DIRECTORY_CODES[$directoryCode] . DIRECTORY_SEPARATOR . $file)
            && strpos($file, '..') === false) {
            $this->mediaDirectory->delete(
                Directory::DIRECTORY_CODES[$directoryCode] . DIRECTORY_SEPARATOR . $file
            );
        }
    }

    /**
     * @param string $directoryCode
     *
     * @throws LocalizedException
     */
    private function checkDirectoryCode($directoryCode)
    {
        if (!array_key_exists($directoryCode, Directory::DIRECTORY_CODES)) {
            throw new LocalizedException(__('Directory Code doesn\'t exist.'));
        }
    }

    /**
     * @return \Amasty\ProductAttachment\Model\Filesystem\UploadFileData
     */
    public function getUploadFileData()
    {
        return $this->uploadFileDataFactory->create();
    }
}
