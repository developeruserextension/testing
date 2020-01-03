<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Api;

/**
 * @api
 */
interface HistoryRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\StoreCredit\Api\Data\HistoryInterface $history
     * @return \Amasty\StoreCredit\Api\Data\HistoryInterface
     */
    public function save(\Amasty\StoreCredit\Api\Data\HistoryInterface $history);

    /**
     * Get by id
     *
     * @param int $historyId
     * @return \Amasty\StoreCredit\Api\Data\HistoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($historyId);

    /**
     * Get last CUSTOMER_HISTORY_ID
     *
     * @param int $customerId
     * @return int
     */
    public function getNextCustomerHistoryId($customerId);

    /**
     * Delete
     *
     * @param \Amasty\StoreCredit\Api\Data\HistoryInterface $history
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\StoreCredit\Api\Data\HistoryInterface $history);

    /**
     * Delete by id
     *
     * @param int $historyId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($historyId);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
