<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Model\StoreCredit;

use Amasty\StoreCredit\Api\StoreCreditRepositoryInterface;
use Amasty\StoreCredit\Model\StoreCredit\StoreCreditFactory;

class StoreCreditRepository implements StoreCreditRepositoryInterface
{
    /**
     * @var StoreCreditFactory
     */
    private $storeCreditFactory;

    /**
     * @var ResourceModel\Collection
     */
    private $storeCreditCollection;

    /**
     * @var array
     */
    private $storeCredits = [];

    public function __construct(
        StoreCreditFactory $storeCreditFactory,
        ResourceModel\Collection $storeCreditCollection
    ) {
        $this->storeCreditFactory = $storeCreditFactory;
        $this->storeCreditCollection = $storeCreditCollection;
    }

    public function getByCustomerId($customerId)
    {
        if (!empty($this->storeCredits[$customerId])) {
            return $this->storeCredits[$customerId];
        }

        if ($storeCredit = $this->storeCreditCollection->getByCustomerId($customerId)) {
            $this->storeCredits[$customerId] = $storeCredit;

            return $storeCredit;
        }

        /** @var \Amasty\StoreCredit\Api\Data\StoreCreditInterface $storeCredit */
        $storeCredit = $this->storeCreditFactory->create();
        $storeCredit->setCustomerId($customerId)
            ->setStoreCredit('0.00');

        $this->storeCredits[$customerId] = $storeCredit;

        return $storeCredit;
    }
}
