<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Icon;

use Amasty\ProductAttachment\Api\Data\IconInterface;
use Magento\Framework\Model\AbstractModel;

class Icon extends AbstractModel implements IconInterface
{
    public function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\ProductAttachment\Model\Icon\ResourceModel\Icon::class);
        $this->setIdFieldName(IconInterface::ICON_ID);
    }

    /**
     * @return int
     */
    public function getIconId()
    {
        return (int)$this->_getData(IconInterface::ICON_ID);
    }

    /**
     * @param int $iconId
     *
     * @return \Amasty\ProductAttachment\Api\Data\IconInterface
     */
    public function setIconId($iconId)
    {
        return $this->setData(IconInterface::ICON_ID, (int)$iconId);
    }

    /**
     * @return string
     */
    public function getFileType()
    {
        return $this->_getData(IconInterface::FILE_TYPE);
    }

    /**
     * @param string $fileType
     *
     * @return \Amasty\ProductAttachment\Api\Data\IconInterface
     */
    public function setFileType($fileType)
    {
        return $this->setData(IconInterface::FILE_TYPE, $fileType);
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->_getData(IconInterface::IMAGE);
    }

    /**
     * @param string $image
     *
     * @return \Amasty\ProductAttachment\Api\Data\IconInterface
     */
    public function setImage($image)
    {
        return $this->setData(IconInterface::IMAGE, $image);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return (bool)$this->_getData(IconInterface::IS_ACTIVE);
    }

    /**
     * @param bool $isActive
     *
     * @return \Amasty\ProductAttachment\Api\Data\IconInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(IconInterface::IS_ACTIVE, (bool)$isActive);
    }

    /**
     * @return array
     */
    public function getExtension()
    {
        if (($extensions = $this->_getData(IconInterface::EXTENSION)) === null) {
            $extensions = $this->_getResource()->getIconExtensions($this->getIconId());
            $this->setExtension($extensions);
        }
        return $extensions;
    }

    /**
     * @param array $extensions
     *
     * @return \Amasty\ProductAttachment\Api\Data\IconInterface
     */
    public function setExtension($extensions)
    {
        return $this->setData(IconInterface::EXTENSION, $extensions);
    }
}
