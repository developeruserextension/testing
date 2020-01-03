<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachmentApi
 */


namespace Amasty\ProductAttachmentApi\Api;

interface FrontendAttachmentInterface
{
    /**
     * Get Product Attachments For Logged In User
     *
     * @param int $productId
     * @param string[] $extraUrlParams
     * @param bool $includeInOrderOnly
     * @param int $amastyCustomerGroup
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Amasty\ProductAttachmentApi\Api\Data\FrontendFileInterface[]
     */
    public function getByProductId(
        $productId,
        $extraUrlParams = [],
        $includeInOrderOnly = false,
        $amastyCustomerGroup = 0
    );

    /**
     * Get Category Attachments For Logged In User
     *
     * @param int $categoryId
     * @param string[] $extraUrlParams
     * @param bool $includeInOrderOnly
     * @param int $amastyCustomerGroup
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Amasty\ProductAttachmentApi\Api\Data\FrontendFileInterface[]
     */
    public function getByCategoryId(
        $categoryId,
        $extraUrlParams = [],
        $includeInOrderOnly = false,
        $amastyCustomerGroup = 0
    );

    /**
     * Get Category Attachments For Logged In User
     *
     * @param string[] $fileIds
     * @param string[] $extraUrlParams
     * @param bool $includeInOrderOnly
     * @param int $amastyCustomerGroup
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Amasty\ProductAttachmentApi\Api\Data\FrontendFileInterface[]
     */
    public function getByFileIds(
        $fileIds,
        $extraUrlParams = [],
        $includeInOrderOnly = false,
        $amastyCustomerGroup = 0
    );
}
