<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\OrderEditor\Model\Order\Item as Entity;
use Magento\Sales\Api\Data\OrderItemInterface;

/**
 * Interface OrderItemRepositoryInterface
 *
 * @package MageWorx\OrderEditor\Api
 */
interface OrderItemRepositoryInterface
{
    const ENTITY_DATA_INTERFACE = Entity::class;

    /**
     * Save entity.
     *
     * @param OrderItemInterface $entity
     * @return OrderItemInterface
     * @throws LocalizedException
     */
    public function save(OrderItemInterface $entity): OrderItemInterface;

    /**
     * Retrieve entity.
     *
     * @param int $entityId
     * @return Entity
     * @throws NoSuchEntityException
     */
    public function getById(int $entityId): Entity;

    /**
     * Retrieve entities matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param bool $returnRawObjects
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $returnRawObjects = false): SearchResultsInterface;

    /**
     * Delete entity.
     *
     * @param OrderItemInterface $entity
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(OrderItemInterface $entity): bool;

    /**
     * Delete entity by ID.
     *
     * @param int $entityId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById(int $entityId): bool;

    /**
     * Get empty Entity
     *
     * @return Entity
     */
    public function getEmptyEntity(): Entity;
}
