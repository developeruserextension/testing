<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachmentApi
 */


namespace Amasty\ProductAttachmentApi\Api;

interface BackendAttachmentInterface
{
    /**
     * Save Attachment
     *
     * @param \Amasty\ProductAttachmentApi\Api\Data\BackendFileInterface $file
     * @param int $fileId
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Amasty\ProductAttachmentApi\Api\Data\BackendFileInterface
     */
    public function save(\Amasty\ProductAttachmentApi\Api\Data\BackendFileInterface $file);

    /**
     * Retrieve file.
     *
     * @param int $fileId
     *
     * @return \Amasty\ProductAttachmentApi\Api\Data\BackendFileInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($fileId);

    /**
     * Delete file by ID.
     *
     * @param int $fileId
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($fileId);

    /**
     * Retrieve files matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
