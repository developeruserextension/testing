<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachmentApi
 */


namespace Amasty\ProductAttachmentApi\Model\File;

use Amasty\ProductAttachmentApi\Api\Data\FileContentInterface;
use Magento\Framework\Model\AbstractModel;

class FileContent extends AbstractModel implements FileContentInterface
{

    /**
     * @inheritdoc
     */
    public function getBase64EncodedData()
    {
        return $this->_getData(FileContentInterface::BASE64_ENCODED_DATA);
    }

    /**
     * @inheritdoc
     */
    public function setBase64EncodedData($base64EncodedData)
    {
        return $this->setData(FileContentInterface::BASE64_ENCODED_DATA, $base64EncodedData);
    }

    /**
     * @inheritdoc
     */
    public function setNameWithExtension($nameWithExtension)
    {
        return $this->setData(FileContentInterface::NAME, $nameWithExtension);
    }

    /**
     * @inheritdoc
     */
    public function getNameWithExtension()
    {
        return $this->_getData(FileContentInterface::NAME);
    }

    /**
     * @inheritdoc
     */
    public function getTemporaryName()
    {
        return $this->_getData(FileContentInterface::TMP_NAME);
    }

    /**
     * @param string $tmpName
     *
     * @return FileContentInterface
     */
    public function setTemporaryName($tmpName)
    {
        return $this->setData(FileContentInterface::TMP_NAME, $tmpName);
    }
}
