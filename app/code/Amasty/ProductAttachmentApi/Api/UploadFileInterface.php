<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachmentApi
 */


namespace Amasty\ProductAttachmentApi\Api;

interface UploadFileInterface
{
    /**
     * Upload File
     *
     * @param \Amasty\ProductAttachmentApi\Api\Data\FileContentInterface $fileContent
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Amasty\ProductAttachmentApi\Api\Data\FileContentInterface
     */
    public function upload(\Amasty\ProductAttachmentApi\Api\Data\FileContentInterface $fileContent);
}
