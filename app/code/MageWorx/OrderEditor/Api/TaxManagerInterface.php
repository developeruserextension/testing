<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Sales\Model\Order\Tax;
use Magento\Tax\Api\Data\OrderTaxDetailsInterface;
use MageWorx\OrderEditor\Model\ResourceModel\Tax\Item\Collection as TaxItemsCollection;
use Magento\Sales\Model\ResourceModel\Order\Tax\Collection as OrderTaxCollection;

/**
 * Interface TaxManagerInterface
 *
 * Manage order (and order items) taxes
 *
 * @package MageWorx\OrderEditor\Api
 */
interface TaxManagerInterface
{
    /**
     * Returns existing order tax details
     *
     * @param int $orderId
     * @return OrderTaxDetailsInterface
     * @throws NoSuchEntityException
     */
    public function getOrderTaxDetails(int $orderId): OrderTaxDetailsInterface;

    /**
     * Get tax details array for the order item
     *
     * @param OrderItem $orderItem
     * @return \Magento\Tax\Model\Sales\Order\Tax[]
     * @throws NoSuchEntityException
     */
    public function getOrderItemTaxDetails(OrderItem $orderItem): array;

    /**
     * Get all tax rates as an option array
     *
     * @return array
     */
    public function getAllAvailableTaxRateCodes(): array;

    /**
     * Returns all tax classes applied to the order item
     *
     * @param OrderItem $orderItem
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getOrderItemTaxClasses(OrderItem $orderItem): array;

    /**
     * @param int $orderItemId
     * @return \Magento\Framework\DataObject[]|\MageWorx\OrderEditor\Model\Order\Tax\Item[]
     */
    public function getOrderItemTaxItems(int $orderItemId): array;

    /**
     * @return TaxItemsCollection
     */
    public function getOrderTaxItemsCollection(): TaxItemsCollection;

    /**
     * @param int $orderId
     * @return \Magento\Tax\Model\Sales\Order\Tax[]
     */
    public function getOrderTaxes(int $orderId): array;

    /**
     * @return OrderTaxCollection
     */
    public function getOrderTaxCollection(): OrderTaxCollection;

    /**
     * Delete records from the sales_order_tax table using tax_id
     *
     * @param int $taxId
     * @return void
     */
    public function deleteOrderTaxRecordByTaxId(int $taxId);

    /**
     * Delete records from sales_order_tax_item table using tax_item_id
     *
     * @param int $taxItemId
     */
    public function deleteOrderTaxItemsRecordByTaxItemId(int $taxItemId);

    /**
     * Find tax item by code and order id
     *
     * @param string $rateCode
     * @param int $orderId
     * @return Tax|null
     */
    public function getOrderTaxItemByCodeAndOrderId(string $rateCode, int $orderId);

    /**
     * Get tax details array for an order shipping
     *
     * @param OrderInterface $order
     * @return \Magento\Tax\Model\Sales\Order\Tax[]
     * @throws NoSuchEntityException
     */
    public function getOrderShippingTaxDetails(OrderInterface $order): array;

    /**
     * Get applied tax classes (codes) for the order shipping
     *
     * @param OrderInterface $order
     * @return array
     * @throws NoSuchEntityException
     */
    public function getOrderShippingTaxClasses(OrderInterface $order): array;

    /**
     * Delete tax items.
     * Tax item amount will be deducted from corresponding tax.
     * If the corresponding tax become equal to zero, it will be also deleted.
     *
     * @param array $taxItems
     * @return void
     * @throws \Exception
     */
    public function deleteTaxItems(array $taxItems);

    /**
     * @param int $taxId
     * @return \Magento\Sales\Model\Order\Tax
     */
    public function getTaxById(int $taxId): \Magento\Sales\Model\Order\Tax;

    /**
     * Save order tax using its resource model
     *
     * @param \Magento\Sales\Model\Order\Tax $tax
     * @return \Magento\Sales\Model\Order\Tax
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function saveOrderTax(\Magento\Sales\Model\Order\Tax $tax): \Magento\Sales\Model\Order\Tax;

    /**
     * Save order tax item using its resource model
     *
     * @param \Magento\Sales\Model\Order\Tax\Item $taxItem
     * @return \Magento\Sales\Model\Order\Tax\Item
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function saveOrderTaxItem(\Magento\Sales\Model\Order\Tax\Item $taxItem): \Magento\Sales\Model\Order\Tax\Item;
}
