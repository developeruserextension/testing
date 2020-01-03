<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Api\Data;

interface IconInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ICON_ID = 'icon_id';

    const FILE_TYPE = 'filetype';

    const IMAGE = 'image';

    const IS_ACTIVE = 'is_active';

    const EXTENSION = 'extension';
    /**#@-*/

    /**
     * @return int
     */
    public function getIconId();

    /**
     * @param int $iconId
     *
     * @return \Amasty\ProductAttachment\Api\Data\IconInterface
     */
    public function setIconId($iconId);

    /**
     * @return string
     */
    public function getFileType();

    /**
     * @param string $fileType
     *
     * @return \Amasty\ProductAttachment\Api\Data\IconInterface
     */
    public function setFileType($fileType);

    /**
     * @return string
     */
    public function getImage();

    /**
     * @param string $image
     *
     * @return \Amasty\ProductAttachment\Api\Data\IconInterface
     */
    public function setImage($image);

    /**
     * @return bool
     */
    public function isActive();

    /**
     * @param bool $isActive
     *
     * @return \Amasty\ProductAttachment\Api\Data\IconInterface
     */
    public function setIsActive($isActive);

    /**
     * @return array
     */
    public function getExtension();

    /**
     * @param array $extensions
     *
     * @return \Amasty\ProductAttachment\Api\Data\IconInterface
     */
    public function setExtension($extensions);
}
