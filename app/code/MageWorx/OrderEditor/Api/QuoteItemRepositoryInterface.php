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
use MageWorx\OrderEditor\Model\Quote\Item as Entity;
use Magento\Quote\Api\Data\CartItemInterface;

/**
 * Interface QuoteItemRepositoryInterface
 *
 * @package MageWorx\OrderEditor\Api
 */
interface QuoteItemRepositoryInterface
{
    const ENTITY_DATA_INTERFACE = Entity::class;

    /**
     * Save entity.
     *
     * @param CartItemInterface $entity
     * @return Entity
     * @throws LocalizedException
     */
    public function save(CartItemInterface $entity): Entity;

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
     * @param CartItemInterface $entity
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(CartItemInterface $entity): bool;

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
