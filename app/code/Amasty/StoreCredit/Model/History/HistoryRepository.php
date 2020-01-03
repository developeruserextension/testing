<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Model\History;

use Amasty\StoreCredit\Api\Data\HistoryInterface;
use Amasty\StoreCredit\Api\HistoryRepositoryInterface;
use Amasty\StoreCredit\Model\History\ResourceModel\Collection;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;

class HistoryRepository implements HistoryRepositoryInterface
{
    /**
     * @var array
     */
    private $histories = [];

    /**
     * @var ResourceModel\History
     */
    private $historyResource;

    /**
     * @var ResourceModel\CollectionFactory
     */
    private $historyCollectionFactory;

    /**
     * @var HistoryFactory
     */
    private $historyFactory;

    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        ResourceModel\History $historyResource,
        HistoryFactory $historyFactory,
        ResourceModel\CollectionFactory $historyCollectionFactory
    ) {
        $this->historyResource = $historyResource;
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->historyFactory = $historyFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @inheritdoc
     */
    public function save(\Amasty\StoreCredit\Api\Data\HistoryInterface $history)
    {
        try {
            if ($history->getHistoryId()) {
                $history = $this->getById($history->getHistoryId())->addData($history->getData());
            }
            $this->historyResource->save($history);
            unset($this->histories[$history->getHistoryId()]);
        } catch (\Exception $e) {
            if ($history->getHistoryId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save store credit history with ID %1. Error: %2',
                        [$history->getHistoryId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new store credit history. Error: %1', $e->getMessage()));
        }

        return $history;
    }

    /**
     * @inheritdoc
     */
    public function getById($historyId)
    {
        if (!isset($this->histories[$historyId])) {
            /** @var \Amasty\StoreCredit\Model\History\History $history */
            $history = $this->historyFactory->create();
            $this->historyResource->load($history, $historyId);
            if (!$history->getHistoryId()) {
                throw new NoSuchEntityException(
                    __('Store Credit History with specified ID "%1" not found.', $historyId)
                );
            }
            $this->histories[$historyId] = $history;
        }

        return $this->histories[$historyId];
    }

    /**
     * @inheritdoc
     */
    public function getNextCustomerHistoryId($customerId)
    {
        return $this->historyResource->getNextCustomerHistoryId($customerId);
    }

    /**
     * @inheritdoc
     */
    public function delete(\Amasty\StoreCredit\Api\Data\HistoryInterface $history)
    {
        try {
            $this->historyResource->delete($history);
            unset($this->histories[$history->getHistoryId()]);
        } catch (\Exception $e) {
            if ($history->getHistoryId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove store credit history with ID %1. Error: %2',
                        [$history->getHistoryId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove store credit history. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($historyId)
    {
        $historyModel = $this->getById($historyId);
        $this->delete($historyModel);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Amasty\StoreCredit\Model\History\ResourceModel\Collection $historyCollection */
        $historyCollection = $this->historyCollectionFactory->create();
        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $historyCollection);
        }
        $searchResults->setTotalCount($historyCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $historyCollection);
        }
        $historyCollection->setCurPage($searchCriteria->getCurrentPage());
        $historyCollection->setPageSize($searchCriteria->getPageSize());
        $history = [];
        /** @var HistoryInterface $item */
        foreach ($historyCollection->getItems() as $item) {
            $history[] = $this->getById($item->getHistoryId());
        }
        $searchResults->setItems($history);

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection  $historyCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $historyCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $historyCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param Collection  $historyCollection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, Collection $historyCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $historyCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? 'DESC' : 'ASC'
            );
        }
    }
}
