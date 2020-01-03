<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachmentApi
 */


namespace Amasty\ProductAttachmentApi\Api;

interface DownloadFileInterface
{
    /**
     * Download File
     *
     * @param int $fileId
     * @param int $productId
     * @param int $categoryId
     * @param int $amastyCustomerGroup
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Amasty\ProductAttachmentApi\Api\Data\FileContentInterface
     */
    public function download($fileId, $productId = 0, $categoryId = 0, $amastyCustomerGroup = 0);
}
