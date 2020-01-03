<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Api\Data;

/**
 * @method mixed getData($key = '', $index = null)
 * @method $this setData($key = '', $value = null)
 */
interface FileInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const FILE_ID = 'file_id';
    const ATTACHMENT_TYPE = 'attachment_type';
    const FILE_PATH = 'filepath';
    const LINK = 'link';
    const EXTENSION = 'extension';
    const SIZE = 'size';
    const MIME_TYPE = 'mime_type';
    const FILENAME = 'filename';
    const LABEL = 'label';
    const IS_VISIBLE = 'is_visible';
    const INCLUDE_IN_ORDER = 'include_in_order';
    const CUSTOMER_GROUPS = 'customer_groups';
    const CATEGORIES = 'category_ids';
    const PRODUCTS = 'product_ids';
    const ICON_URL = 'icon_url';
    const FRONTEND_URL = 'frontend_url';
    const URL_HASH = 'url_hash';
    /**#@-*/

    /**
     * @return int
     */
    public function getFileId();

    /**
     * @param int $fileId
     *
     * @return \Amasty\ProductAttachment\Api\Data\FileInterface
     */
    public function setFileId($fileId);

    /**
     * @return int
     */
    public function getAttachmentType();

    /**
     * @param int $attachmentType
     *
     * @return \Amasty\ProductAttachment\Api\Data\FileInterface
     */
    public function setAttachmentType($attachmentType);

    /**
     * @return string
     */
    public function getFilePath();

    /**
     * @param string $filePath
     *
     * @return \Amasty\ProductAttachment\Api\Data\FileInterface
     */
    public function setFilePath($filePath);

    /**
     * @return string
     */
    public function getLink();

    /**
     * @param string link
     *
     * @return \Amasty\ProductAttachment\Api\Data\FileInterface
     */
    public function setLink($link);

    /**
     * @return string
     */
    public function getFileExtension();

    /**
     * @param string $extension
     *
     * @return \Amasty\ProductAttachment\Api\Data\FileInterface
     */
    public function setFileExtension($extension);

    /**
     * @return string
     */
    public function getMimeType();

    /**
     * @param string $mimeType
     *
     * @return \Amasty\ProductAttachment\Api\Data\FileInterface
     */
    public function setMimeType($mimeType);

    /**
     * @return int
     */
    public function getFileSize();

    /**
     * @param int $fileSize
     *
     * @return \Amasty\ProductAttachment\Api\Data\FileInterface
     */
    public function setFileSize($fileSize);

    /**
     * @return string
     */
    public function getFileName();

    /**
     * @param string $fileName
     *
     * @return \Amasty\ProductAttachment\Api\Data\FileInterface
     */
    public function setFileName($fileName);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     *
     * @return \Amasty\ProductAttachment\Api\Data\FileInterface
     */
    public function setLabel($label);

    /**
     * @return string[]
     */
    public function getCustomerGroups();

    /**
     * @param string[] $customerGroups
     *
     * @return \Amasty\ProductAttachment\Api\Data\FileInterface
     */
    public function setCustomerGroups($customerGroups);

    /**
     * @return bool
     */
    public function isVisible();

    /**
     * @param bool $isVisible
     *
     * @return \Amasty\ProductAttachment\Api\Data\FileInterface
     */
    public function setIsVisible($isVisible);

    /**
     * @return bool
     */
    public function isIncludeInOrder();

    /**
     * @param bool $isIncludeInOrder
     *
     * @return \Amasty\ProductAttachment\Api\Data\FileInterface
     */
    public function setIsIncludeInOrder($isIncludeInOrder);

    /**
     * @return string
     */
    public function getIconUrl();

    /**
     * @param string $iconUrl
     *
     * @return \Amasty\ProductAttachment\Api\Data\FileInterface
     */
    public function setIconUrl($iconUrl);

    /**
     * @return string
     */
    public function getFrontendUrl();

    /**
     * @param string $frontendUrl
     *
     * @return \Amasty\ProductAttachment\Api\Data\FileInterface
     */
    public function setFrontendUrl($frontendUrl);

    /**
     * @return string
     */
    public function getUrlHash();

    /**
     * @param string $urlHash
     *
     * @return \Amasty\ProductAttachment\Api\Data\FileInterface
     */
    public function setUrlHash($urlHash);
}
