<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachmentApi
 */


namespace Amasty\ProductAttachmentApi\Model\File;

use Amasty\ProductAttachmentApi\Api\Data\BackendFileInterface;
use Amasty\ProductAttachment\Model\File\File;

class BackendFile extends File implements BackendFileInterface
{
    /**
     * @inheritdoc
     */
    public function getAttachmentProducts()
    {
        return $this->_getData(BackendFileInterface::ATTACHMENT_PRODUCTS);
    }

    /**
     * @inheritdoc
     */
    public function setAttachmentProducts($products)
    {
        return $this->setData(BackendFileInterface::ATTACHMENT_PRODUCTS, $products);
    }

    /**
     * @inheritdoc
     */
    public function getAttachmentCategories()
    {
        return $this->_getData(BackendFileInterface::ATTACHMENT_CATEGORIES);
    }

    /**
     * @inheritdoc
     */
    public function setAttachmentCategories($categories)
    {
        return $this->setData(BackendFileInterface::ATTACHMENT_CATEGORIES, $categories);
    }

    /**
     * @inheritdoc
     */
    public function getTmpFile()
    {
        return $this->_getData(BackendFileInterface::TMP_FILE);
    }

    /**
     * @inheritdoc
     */
    public function setTmpFile($tmpFile)
    {
        return $this->setData(BackendFileInterface::TMP_FILE, $tmpFile);
    }
}
