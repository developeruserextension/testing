<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\File;

use Amasty\ProductAttachment\Api\Data\FileInterface;
use Magento\Framework\Model\AbstractModel;

class File extends AbstractModel implements FileInterface
{
    public function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\ProductAttachment\Model\File\ResourceModel\File::class);
        $this->setIdFieldName(FileInterface::FILE_ID);
    }

    /**
     * @inheritdoc
     */
    public function getFileId()
    {
        return (int)$this->_getData(FileInterface::FILE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setFileId($fileId)
    {
        return $this->setData(FileInterface::FILE_ID, (int)$fileId);
    }

    /**
     * @inheritdoc
     */
    public function getAttachmentType()
    {
        return (int)$this->_getData(FileInterface::ATTACHMENT_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setAttachmentType($attachmentType)
    {
        return $this->setData(FileInterface::ATTACHMENT_TYPE, (int)$attachmentType);
    }

    /**
     * @inheritdoc
     */
    public function getFilePath()
    {
        if ($filePath = $this->_getData(FileInterface::FILE_PATH)) {
            return $filePath . '.' . $this->_getData(FileInterface::EXTENSION);
        }
        return  '';
    }

    /**
     * @inheritdoc
     */
    public function setFilePath($filePath)
    {
        return $this->setData(FileInterface::FILE_PATH, $filePath);
    }

    /**
     * @inheritdoc
     */
    public function getLink()
    {
        return $this->_getData(FileInterface::LINK);
    }

    /**
     * @inheritdoc
     */
    public function setLink($link)
    {
        return $this->setData(FileInterface::LINK, $link);
    }

    /**
     * @inheritdoc
     */
    public function getCategories()
    {
        return $this->_getData(FileInterface::CATEGORIES);
    }

    /**
     * @inheritdoc
     */
    public function setCategories($categories)
    {
        foreach ($categories as &$category) {
            $category = (int)$category;
        }

        return $this->setData(FileInterface::CATEGORIES, array_unique($categories));
    }

    /**
     * @inheritdoc
     */
    public function getProducts()
    {
        return $this->_getData(FileInterface::PRODUCTS);
    }

    /**
     * @inheritdoc
     */
    public function setProducts($products)
    {
        foreach ($products as &$product) {
            $product = (int)$product;
        }
        return $this->setData(FileInterface::PRODUCTS, array_unique($products));
    }

    /**
     * @inheritdoc
     */
    public function getFileExtension()
    {
        return $this->_getData(FileInterface::EXTENSION);
    }

    /**
     * @inheritdoc
     */
    public function setFileExtension($extension)
    {
        return $this->setData(FileInterface::EXTENSION, $extension);
    }

    /**
     * @inheritdoc
     */
    public function getMimeType()
    {
        return $this->_getData(FileInterface::MIME_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setMimeType($mimeType)
    {
        return $this->setData(FileInterface::MIME_TYPE, $mimeType);
    }

    /**
     * @inheritdoc
     */
    public function getFileSize()
    {
        return (int)$this->_getData(FileInterface::SIZE);
    }

    /**
     * @inheritdoc
     */
    public function setFileSize($fileSize)
    {
        return $this->setData(FileInterface::SIZE, (int)$fileSize);
    }

    /**
     * @inheritdoc
     */
    public function getFileName()
    {
        return $this->_getData(FileInterface::FILENAME);
    }

    /**
     * @inheritdoc
     */
    public function setFileName($fileName)
    {
        return $this->setData(FileInterface::FILENAME, $fileName);
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return $this->_getData(FileInterface::LABEL);
    }

    /**
     * @inheritdoc
     */
    public function setLabel($label)
    {
        return $this->setData(FileInterface::LABEL, $label);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerGroups()
    {
        $customerGroups = $this->_getData(FileInterface::CUSTOMER_GROUPS);
        if ($customerGroups !== null && $customerGroups !== "" && !is_array($customerGroups)) {
            $customerGroups = explode(',', $customerGroups);
        }
        return $customerGroups;
    }

    /**
     * @inheritdoc
     */
    public function setCustomerGroups($customerGroups)
    {
        return $this->setData(FileInterface::CUSTOMER_GROUPS, $customerGroups);
    }

    /**
     * @inheritdoc
     */
    public function isVisible()
    {
        return (bool)$this->_getData(FileInterface::IS_VISIBLE);
    }

    /**
     * @inheritdoc
     */
    public function setIsVisible($isVisible)
    {
        return $this->setData(FileInterface::IS_VISIBLE, (bool)$isVisible);
    }

    /**
     * @inheritdoc
     */
    public function isIncludeInOrder()
    {
        return (bool)$this->_getData(FileInterface::INCLUDE_IN_ORDER);
    }

    /**
     * @inheritdoc
     */
    public function setIsIncludeInOrder($isIncludeInOrder)
    {
        return $this->setData(FileInterface::INCLUDE_IN_ORDER, (bool)$isIncludeInOrder);
    }

    /**
     * @inheritdoc
     */
    public function setIconUrl($iconUrl)
    {
        return $this->setData(FileInterface::ICON_URL, $iconUrl);
    }

    /**
     * @inheritdoc
     */
    public function getIconUrl()
    {
        return $this->_getData(FileInterface::ICON_URL);
    }

    /**
     * @inheritdoc
     */
    public function setFrontendUrl($frontendUrl)
    {
        return $this->setData(FileInterface::FRONTEND_URL, $frontendUrl);
    }

    /**
     * @inheritdoc
     */
    public function getFrontendUrl()
    {
        return $this->_getData(FileInterface::FRONTEND_URL);
    }

    /**
     * @inheritdoc
     */
    public function getUrlHash()
    {
        return $this->_getData(FileInterface::URL_HASH);
    }

    /**
     * @inheritdoc
     */
    public function setUrlHash($urlHash)
    {
        return $this->setData(FileInterface::URL_HASH, $urlHash);
    }

    /**
     * @param int $bytes
     *
     * @return string
     */
    public function getReadableFileSize()
    {
        if ($bytes = $this->getFileSize()) {
            $size   = ['B', 'kB', 'MB', 'GB', 'TB'];
            $factor = (int)floor((strlen($bytes) - 1) / 3);
            if (isset($size[$factor])) {
                $bytes = sprintf("%.2f", $bytes / pow(1024, $factor)) . ' ' . $size[$factor];
            }
        }

        return $bytes;
    }
}
