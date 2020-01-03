<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Filesystem;

use Amasty\ProductAttachment\Model\Filesystem\Directory;
use Magento\Framework\App\Filesystem\DirectoryList;

class ImportFilesScanner
{
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    public function __construct(
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
    }

    /**
     * @return array
     */
    public function execute()
    {
        $result = [];

        $folders = $this->mediaDirectory->read(Directory::DIRECTORY_CODES[Directory::IMPORT_FTP]);
        foreach ($folders as $file) {
            if ($this->mediaDirectory->isFile($file)) {
                $result[] = basename($file);
            }
        }

        return $result;
    }
}
