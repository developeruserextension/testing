<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachmentApi
 */


namespace Amasty\ProductAttachmentApi\Model\File;

use Amasty\ProductAttachment\Api\Data\FileInterface;
use Amasty\ProductAttachmentApi\Api\Data\FrontendFileInterface;
use Magento\Framework\Model\AbstractModel;

class FrontendFile extends AbstractModel implements FrontendFileInterface
{
    /**
     * @inheritdoc
     */
    public function getFileId()
    {
        return $this->_getData(FileInterface::FILE_ID);
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
        return $this->setData(FileInterface::ATTACHMENT_TYPE, (int)$fileSize);
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
        return $this->setData(FileInterface::ATTACHMENT_TYPE, $fileName);
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
        return $this->setData(FileInterface::ATTACHMENT_TYPE, $label);
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
    public function setIconUrl($iconUrl)
    {
        return $this->setData(FileInterface::ATTACHMENT_TYPE, $iconUrl);
    }

    /**
     * @inheritdoc
     */
    public function getUrl()
    {
        return $this->_getData(FrontendFileInterface::URL);
    }

    /**
     * @inheritdoc
     */
    public function setUrl($frontendUrl)
    {
        return $this->setData(FrontendFileInterface::URL, $frontendUrl);
    }
}
