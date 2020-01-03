<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachmentApi
 */


namespace Amasty\ProductAttachmentApi\Api\Data;

interface FrontendFileInterface
{
    const URL = 'url';

    /**
     * @return int
     */
    public function getFileId();

    /**
     * @param string $fileId
     *
     * @return \Amasty\ProductAttachmentApi\Api\Data\FrontendFileInterface
     */
    public function setFileId($fileId);

    /**
     * @return string
     */
    public function getMimeType();

    /**
     * @param string $mimeType
     *
     * @return \Amasty\ProductAttachmentApi\Api\Data\FrontendFileInterface
     */
    public function setMimeType($mimeType);

    /**
     * @return int
     */
    public function getFileSize();

    /**
     * @param int $fileSize
     *
     * @return \Amasty\ProductAttachmentApi\Api\Data\FrontendFileInterface
     */
    public function setFileSize($fileSize);

    /**
     * @return string
     */
    public function getFileName();

    /**
     * @param string $fileName
     *
     * @return \Amasty\ProductAttachmentApi\Api\Data\FrontendFileInterface
     */
    public function setFileName($fileName);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     *
     * @return \Amasty\ProductAttachmentApi\Api\Data\FrontendFileInterface
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getIconUrl();

    /**
     * @param string $iconUrl
     *
     * @return \Amasty\ProductAttachmentApi\Api\Data\FrontendFileInterface
     */
    public function setIconUrl($iconUrl);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $frontendUrl
     *
     * @return \Amasty\ProductAttachmentApi\Api\Data\FrontendFileInterface
     */
    public function setUrl($frontendUrl);
}
