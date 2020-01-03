<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Filesystem;

use Amasty\ProductAttachment\Model\Filesystem\Directory;
use Amasty\ProductAttachment\Model\Filesystem\File;
use Magento\Framework\UrlInterface;

class UrlResolver
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var File
     */
    private $file;

    public function __construct(
        UrlInterface $urlBuilder,
        File $file
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->file = $file;
    }

    public function getIconUrlByName($name)
    {
        if (!($icon = $this->file->getFilePath($name, Directory::ICON))) {
            return false;
        }

        $baseUrl = $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
        $baseUrl = trim(str_replace('index.php', '', $baseUrl), '/');

        return $baseUrl . '/' . $icon;
    }

    public function getAttachmentUrlByName($name)
    {
        if (!($file = $this->file->getFilePath($name, Directory::ATTACHMENT))) {
            return false;
        }

        $baseUrl = $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
        $baseUrl = trim(str_replace('index.php', '', $baseUrl), '/');

        return $baseUrl . '/' . $file;
    }
}
