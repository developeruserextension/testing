<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Model;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Sales\Model\ResourceModel\Order\Tax as OrderTaxResource;
use Magento\Sales\Model\Order\Tax as OrderTax;
use Magento\Sales\Model\Order\Tax\Item as OrderTaxItem;
use Magento\Sales\Model\ResourceModel\Order\Tax\Item as OrderTaxItemResource;
use Magento\Tax\Api\Data\OrderTaxDetailsAppliedTaxInterfaceFactory;
use Magento\Tax\Api\Data\OrderTaxDetailsInterface;
use Magento\Tax\Api\Data\OrderTaxDetailsItemInterface;
use Magento\Tax\Api\OrderTaxManagementInterface;
use Magento\Tax\Model\Calculation\Rate as TaxCalculationRate;
use MageWorx\OrderEditor\Api\TaxManagerInterface;
use Magento\Tax\Model\ResourceModel\Calculation\Rate\CollectionFactory as TaxRatesCollectionFactory;
use MageWorx\OrderEditor\Model\ResourceModel\Tax\Item\CollectionFactory as TaxItemsCollectionFactory;
use MageWorx\OrderEditor\Model\ResourceModel\Tax\Item\Collection as TaxItemsCollection;
use Magento\Sales\Model\ResourceModel\Order\Tax\CollectionFactory as OrderTaxCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Tax\Collection as OrderTaxCollection;

/**
 * Class TaxManager
 */
class TaxManager implements TaxManagerInterface
{
    /**
     * @var \Magento\Tax\Api\Data\OrderTaxManagementInterface
     */
    private $orderTaxManagement;

    /**
     * @var TaxRatesCollectionFactory
     */
    private $taxRateCollectionFactory;

    /**
     * @var TaxItemsCollectionFactory
     */
    private $taxItemsCollectionFactory;

    /**
     * @var OrderTaxCollectionFactory
     */
    private $orderTaxCollectionFactory;

    /**
     * @var OrderTaxDetailsAppliedTaxInterfaceFactory
     */
    private $orderTaxDetailsAppliedTaxFactory;

    /**
     * @var OrderTaxResource
     */
    private $orderTaxResource;

    /**
     * @var OrderTaxItemResource
     */
    private $orderTaxItemResource;

    /**
     * TaxManager constructor.
     *
     * @param OrderTaxManagementInterface $orderTaxManagement
     * @param TaxRatesCollectionFactory $taxRateCollectionFactory
     * @param TaxItemsCollectionFactory $taxItemsCollectionFactory
     * @param OrderTaxCollectionFactory $orderTaxCollectionFactory
     * @param OrderTaxDetailsAppliedTaxInterfaceFactory $orderTaxDetailsAppliedTaxFactory
     */
    public function __construct(
        OrderTaxManagementInterface $orderTaxManagement,
        TaxRatesCollectionFactory $taxRateCollectionFactory,
        TaxItemsCollectionFactory $taxItemsCollectionFactory,
        OrderTaxCollectionFactory $orderTaxCollectionFactory,
        OrderTaxDetailsAppliedTaxInterfaceFactory $orderTaxDetailsAppliedTaxFactory,
        OrderTaxResource $orderTaxResource,
        OrderTaxItemResource $orderTaxItemResource
    ) {
        $this->orderTaxManagement               = $orderTaxManagement;
        $this->taxRateCollectionFactory         = $taxRateCollectionFactory;
        $this->taxItemsCollectionFactory        = $taxItemsCollectionFactory;
        $this->orderTaxCollectionFactory        = $orderTaxCollectionFactory;
        $this->orderTaxDetailsAppliedTaxFactory = $orderTaxDetailsAppliedTaxFactory;
        $this->orderTaxResource                 = $orderTaxResource;
        $this->orderTaxItemResource             = $orderTaxItemResource;
    }

    /**
     * Returns existing order tax details
     *
     * @param int $orderId
     *
     * @return OrderTaxDetailsInterface
     * @throws NoSuchEntityException
     */
    public function getOrderTaxDetails(int $orderId): OrderTaxDetailsInterface
    {
        $orderTaxDetails = $this->orderTaxManagement->getOrderTaxDetails($orderId);

        return $orderTaxDetails;
    }

    /**
     * Get tax details array for an order item
     *
     * @param OrderItem $orderItem
     *
     * @return \Magento\Tax\Model\Sales\Order\Tax[]
     * @throws NoSuchEntityException
     */
    public function getOrderItemTaxDetails(OrderItem $orderItem): array
    {
        if ($this->isOrderItemNew($orderItem)) {
            return $this->getNewOrderItemTaxDetails($orderItem);
        } else {
            return $this->getRegularOrderItemTaxDetails($orderItem);
        }
    }

    /**
     * Detects is order item new
     *
     * @param OrderItem $orderItem
     * @return bool
     */
    private function isOrderItemNew(OrderItem $orderItem): bool
    {
        return !$orderItem->getOrder();
    }

    /**
     * Get tax details array for the order item which already exists (saved)
     *
     * @param OrderItem $orderItem
     *
     * @return \Magento\Tax\Model\Sales\Order\Tax[]
     * @throws NoSuchEntityException
     */
    private function getRegularOrderItemTaxDetails(OrderItem $orderItem): array
    {
        $itemId  = $orderItem->getItemId();
        $orderId = $orderItem->getOrderId();
        if (!$itemId || !$orderId) {
            return [];
        }

        $orderTaxDetails = $this->getOrderTaxDetails($orderId);
        /** @var OrderTaxDetailsItemInterface[] $appliedTaxes */
        $appliedTaxes = $orderTaxDetails->getItems();
        $itemTaxes    = [];
        /** @var OrderTaxDetailsItemInterface $appliedTax */
        foreach ($appliedTaxes as $appliedTax) {
            if ($appliedTax->getItemId() == $itemId) {
                $itemTaxes = $appliedTax->getAppliedTaxes();
                break;
            }
        }

        return $itemTaxes;
    }

    /**
     * Get tax details array for an order item which recently added to the order
     * (not saved yet)
     *
     * @param OrderItem $orderItem
     *
     * @return \Magento\Tax\Model\Sales\Order\Tax[]
     */
    private function getNewOrderItemTaxDetails(OrderItem $orderItem): array
    {
        if (empty($orderItem->getAppliedTaxes())) {
            return [];
        }

        $appliedTaxes = $orderItem->getAppliedTaxes();
        $itemTaxes    = [];

        foreach ($appliedTaxes as $appliedTax) {
            if (empty($appliedTax['rates'])) {
                continue;
            }

            foreach ($appliedTax['rates'] as $rate) {
                $rateData                 = [
                    'code'    => $rate['code'],
                    'title'   => $rate['title'],
                    'percent' => $appliedTax['percent'],
                ];
                $itemTaxes[$rate['code']] = $this->orderTaxDetailsAppliedTaxFactory
                    ->create(['data' => $rateData]);
            }
        }

        return $itemTaxes;
    }

    /**
     * Get all tax rates as an option array
     *
     * @return array
     */
    public function getAllAvailableTaxRateCodes(): array
    {
        $taxRatesCollection = $this->taxRateCollectionFactory->create();

        $options = [];
        /** @var TaxCalculationRate $item */
        foreach ($taxRatesCollection as $item) {
            $options[$item->getId()] = [
                'label'   => $item->getCode(),
                'value'   => $item->getCode(),
                'percent' => (float)$item->getRate(),
                'id'      => (int)$item->getData('tax_calculation_rate_id')
            ];
        }

        return $options;
    }

    /**
     * Returns all tax classes applied to the order item
     *
     * @param OrderItem $orderItem
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getOrderItemTaxClasses(OrderItem $orderItem): array
    {
        if ($this->isOrderItemNew($orderItem)) {
            $taxClasses = $this->getNewOrderItemTaxClasses($orderItem);
        } else {
            $taxClasses = $this->getRegularOrderItemTaxClasses($orderItem);
        }

        return $taxClasses;
    }

    /**
     * Returns all tax classes applied to the order item
     *
     * @param OrderItem $orderItem
     *
     * @return array
     * @throws NoSuchEntityException
     */
    private function getRegularOrderItemTaxClasses(OrderItem $orderItem): array
    {
        if ($orderItem->getOrderId()) {
            /** @var OrderTaxDetailsInterface $taxDetails */
            $taxDetails  = $this->getOrderTaxDetails($orderItem->getOrderId());
            $items       = $taxDetails->getItems();
            $orderItemId = $orderItem->getItemId();
            if ($orderItemId) {
                foreach ($items as $item) {
                    if ($item->getItemId() == $orderItemId) {
                        return array_keys($item->getAppliedTaxes());
                    }
                }
            }
        }

        return [];
    }

    /**
     * Get applied tax classes (codes) for the newly created order item (not saved yet)
     *
     * @param OrderItem $orderItem
     * @return array
     * @throws NoSuchEntityException
     */
    private function getNewOrderItemTaxClasses(OrderItem $orderItem): array
    {
        $orderItemTaxDetails = $this->getOrderItemTaxDetails($orderItem);
        if (empty($orderItemTaxDetails)) {
            return [];
        }

        return array_keys($orderItemTaxDetails);
    }

    /**
     * @param int $orderItemId
     * @return \Magento\Framework\DataObject[]|\MageWorx\OrderEditor\Model\Order\Tax\Item[]
     */
    public function getOrderItemTaxItems(int $orderItemId): array
    {
        /** @var TaxItemsCollection $collection */
        $collection = $this->getOrderTaxItemsCollection();
        $collection->addOrderItemIdFilter($orderItemId);
        $collection->addTaxCodeColumn();

        $items = $collection->getItems();

        return $items;
    }

    /**
     * @param int $orderId
     * @return \Magento\Tax\Model\Sales\Order\Tax[]
     */
    public function getOrderTaxes(int $orderId): array
    {
        $orderTaxCollection = $this->getOrderTaxCollection()
                                   ->addFieldToSelect('*')
                                   ->addFilter('order_id', $orderId);
        /** @var \Magento\Tax\Model\Sales\Order\Tax[] $orderTaxes */
        $orderTaxes = $orderTaxCollection->getItems();

        return $orderTaxes;
    }

    /**
     * @return TaxItemsCollection
     */
    public function getOrderTaxItemsCollection(): TaxItemsCollection
    {
        /** @var TaxItemsCollection $collection */
        $collection = $this->taxItemsCollectionFactory->create();

        return $collection;
    }

    /**
     * @return OrderTaxCollection
     */
    public function getOrderTaxCollection(): OrderTaxCollection
    {
        /** @var OrderTaxCollection $collection */
        $collection = $this->orderTaxCollectionFactory->create();

        return $collection;
    }

    /**
     * Delete records from the sales_order_tax table using tax_id
     *
     * @param int $taxId
     * @return void
     */
    public function deleteOrderTaxRecordByTaxId(int $taxId)
    {
        $this->getOrderTaxCollection()
             ->addFieldToFilter('tax_id', $taxId)
             ->walk('delete');
    }

    /**
     * Delete records from sales_order_tax_item table using tax_item_id
     *
     * @param int $taxItemId
     */
    public function deleteOrderTaxItemsRecordByTaxItemId(int $taxItemId)
    {
        $this->getOrderTaxItemsCollection()
             ->addFieldToFilter('tax_item_id', $taxItemId)
             ->walk('delete');
    }

    /**
     * Find tax item by code and order id
     *
     * @param string $rateCode
     * @param int $orderId
     * @return OrderTax|DataObject
     */
    public function getOrderTaxItemByCodeAndOrderId(string $rateCode, int $orderId): OrderTax
    {
        return $this->getOrderTaxCollection()
                    ->addFieldToFilter('order_id', $orderId)
                    ->addFieldToFilter('code', $rateCode)
                    ->getFirstItem();
    }

    /**
     * Get tax details array for an order shipping
     *
     * @param OrderInterface $order
     * @return \Magento\Tax\Model\Sales\Order\Tax[]
     * @throws NoSuchEntityException
     */
    public function getOrderShippingTaxDetails(OrderInterface $order): array
    {
        $orderId = $order->getEntityId();
        if (!$orderId) {
            return [];
        }

        $orderTaxDetails = $this->getOrderTaxDetails($orderId);
        /** @var OrderTaxDetailsItemInterface[] $appliedTaxes */
        $appliedTaxes  = $orderTaxDetails->getItems();
        $shippingTaxes = [];
        /** @var OrderTaxDetailsItemInterface $appliedTax */
        foreach ($appliedTaxes as $appliedTax) {
            if ($appliedTax->getType() === 'shipping') {
                $shippingTaxes = $appliedTax->getAppliedTaxes();
                break;
            }
        }

        return $shippingTaxes;
    }

    /**
     * Get applied tax classes (codes) for the order shipping
     *
     * @param OrderInterface $order
     * @return array
     * @throws NoSuchEntityException
     */
    public function getOrderShippingTaxClasses(OrderInterface $order): array
    {
        $appliedTaxes = $this->getOrderShippingTaxDetails($order);

        return array_keys($appliedTaxes);
    }

    /**
     * Delete tax items.
     * Tax item amount will be deducted from corresponding tax.
     * If the corresponding tax become equal to zero, it will be also deleted.
     *
     * @param \MageWorx\OrderEditor\Model\Order\Tax\Item[] $taxItems
     * @return void
     * @throws \Exception
     */
    public function deleteTaxItems(array $taxItems)
    {
        /** @var \MageWorx\OrderEditor\Model\Order\Tax\Item $taxItem */
        foreach ($taxItems as $taxItem) {
            $taxId = $taxItem->getTaxId();
            $tax   = $this->getTaxById($taxId);
            if (abs((float)$tax->getAmount() - (float)$taxItem->getData('amount')) < 0.0001) {
                $this->deleteOrderTaxRecordByTaxId($taxId);
                $this->deleteOrderTaxItemsRecordByTaxItemId($taxItem->getData('tax_item_id'));

                continue;
            } else {
                $newTaxAmount         = (float)$tax->getAmount() - (float)$taxItem->getData('amount');
                $newBaseTaxAmount     = (float)$tax->getBaseAmount() - (float)$taxItem->getData('base_amount');
                $newRealBaseTaxAmount = (float)$tax->getBaseRealAmount() -
                    (float)$taxItem->getData('real_base_amount');

                $tax->setAmount($newTaxAmount);
                $tax->setBaseAmount($newBaseTaxAmount);
                $tax->setBaseRealAmount($newRealBaseTaxAmount);

                $this->saveOrderTax($tax);

                $this->deleteOrderTaxItemsRecordByTaxItemId($taxItem->getData('tax_item_id'));

                continue;
            }
        }
    }

    /**
     * @param int $taxId
     * @return OrderTax
     */
    public function getTaxById(int $taxId): OrderTax
    {
        /** @var OrderTax $tax */
        $tax = $this->getOrderTaxCollection()
                    ->addFilter('tax_id', $taxId)
                    ->getFirstItem();

        return $tax;
    }

    /**
     * Save order tax using its resource model
     *
     * @param OrderTax $tax
     * @return OrderTax
     * @throws AlreadyExistsException
     */
    public function saveOrderTax(OrderTax $tax): OrderTax
    {
        $this->orderTaxResource->save($tax);

        return $tax;
    }

    /**
     * Save order tax item using its resource model
     *
     * @param OrderTaxItem $taxItem
     * @return OrderTaxItem
     * @throws AlreadyExistsException
     */
    public function saveOrderTaxItem(OrderTaxItem $taxItem): OrderTaxItem
    {
        $this->orderTaxItemResource->save($taxItem);

        return $taxItem;
    }
}
