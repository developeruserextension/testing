<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Model;

use Magento\Sales\Model\ResourceModel as OriginalSalesResourceModel;

/**
 * Class OrderCollectionFactoryBox
 *
 * Box class for the Collection Factories required by a Order class.
 *
 * @important Contains only Collection Factories injected and corresponding getters.
 * @important Do not place any logic inside this class!
 *
 * @see       \MageWorx\OrderEditor\Model\Order::__construct()
 */
class OrderCollectionFactoryBox
{
    /**
     * @var OriginalSalesResourceModel\Order\Item\CollectionFactory
     */
    private $orderItemCollectionFactory;

    /**
     * @var OriginalSalesResourceModel\Order\Address\CollectionFactory
     */
    private $addressCollectionFactory;

    /**
     * @var OriginalSalesResourceModel\Order\Payment\CollectionFactory
     */
    private $paymentCollectionFactory;

    /**
     * @var OriginalSalesResourceModel\Order\Status\History\CollectionFactory
     */
    private $historyCollectionFactory;

    /**
     * @var OriginalSalesResourceModel\Order\Invoice\CollectionFactory
     */
    private $invoiceCollectionFactory;

    /**
     * @var OriginalSalesResourceModel\Order\Shipment\CollectionFactory
     */
    private $shipmentCollectionFactory;

    /**
     * @var OriginalSalesResourceModel\Order\Creditmemo\CollectionFactory
     */
    private $memoCollectionFactory;

    /**
     * @var OriginalSalesResourceModel\Order\Shipment\Track\CollectionFactory
     */
    private $trackCollectionFactory;

    /**
     * @var OriginalSalesResourceModel\Order\CollectionFactory
     */
    private $salesOrderCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productListFactory;

    /**
     * @var \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory
     */
    private $taxCollectionFactory;

    /**
     * @var ResourceModel\Invoice\CollectionFactory
     */
    private $oeInvoiceCollectionFactory;

    /**
     * @var ResourceModel\Creditmemo\CollectionFactory
     */
    private $oeCreditmemoCollectionFactory;

    /**
     * @var ResourceModel\Shipment\CollectionFactory
     */
    private $oeShipmentCollectionFactory;

    /**
     * OrderCollectionFactoryBox constructor.
     *
     * @param OriginalSalesResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory
     * @param OriginalSalesResourceModel\Order\Address\CollectionFactory $addressCollectionFactory
     * @param OriginalSalesResourceModel\Order\Payment\CollectionFactory $paymentCollectionFactory
     * @param OriginalSalesResourceModel\Order\Status\History\CollectionFactory $historyCollectionFactory
     * @param OriginalSalesResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory
     * @param OriginalSalesResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory
     * @param OriginalSalesResourceModel\Order\Creditmemo\CollectionFactory $memoCollectionFactory
     * @param OriginalSalesResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory
     * @param OriginalSalesResourceModel\Order\CollectionFactory $salesOrderCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productListFactory
     * @param \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $taxCollectionFactory
     * @param ResourceModel\Invoice\CollectionFactory $oeInvoiceCollectionFactory
     * @param ResourceModel\Creditmemo\CollectionFactory $oeCreditmemoCollectionFactory
     * @param ResourceModel\Shipment\CollectionFactory $oeShipmentCollectionFactory
     */
    public function __construct(
        OriginalSalesResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory,
        OriginalSalesResourceModel\Order\Address\CollectionFactory $addressCollectionFactory,
        OriginalSalesResourceModel\Order\Payment\CollectionFactory $paymentCollectionFactory,
        OriginalSalesResourceModel\Order\Status\History\CollectionFactory $historyCollectionFactory,
        OriginalSalesResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
        OriginalSalesResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory,
        OriginalSalesResourceModel\Order\Creditmemo\CollectionFactory $memoCollectionFactory,
        OriginalSalesResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory,
        OriginalSalesResourceModel\Order\CollectionFactory $salesOrderCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productListFactory,
        \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $taxCollectionFactory,
        \MageWorx\OrderEditor\Model\ResourceModel\Invoice\CollectionFactory $oeInvoiceCollectionFactory,
        \MageWorx\OrderEditor\Model\ResourceModel\Creditmemo\CollectionFactory $oeCreditmemoCollectionFactory,
        \MageWorx\OrderEditor\Model\ResourceModel\Shipment\CollectionFactory $oeShipmentCollectionFactory
    ) {
        $this->orderItemCollectionFactory    = $orderItemCollectionFactory;
        $this->addressCollectionFactory      = $addressCollectionFactory;
        $this->paymentCollectionFactory      = $paymentCollectionFactory;
        $this->historyCollectionFactory      = $historyCollectionFactory;
        $this->invoiceCollectionFactory      = $invoiceCollectionFactory;
        $this->shipmentCollectionFactory     = $shipmentCollectionFactory;
        $this->memoCollectionFactory         = $memoCollectionFactory;
        $this->trackCollectionFactory        = $trackCollectionFactory;
        $this->salesOrderCollectionFactory   = $salesOrderCollectionFactory;
        $this->productListFactory            = $productListFactory;
        $this->taxCollectionFactory          = $taxCollectionFactory;
        $this->oeInvoiceCollectionFactory    = $oeInvoiceCollectionFactory;
        $this->oeCreditmemoCollectionFactory = $oeCreditmemoCollectionFactory;
        $this->oeShipmentCollectionFactory   = $oeShipmentCollectionFactory;
    }

    /**
     * @return OriginalSalesResourceModel\Order\Item\CollectionFactory
     */
    public function getOrderItemCollectionFactory(): OriginalSalesResourceModel\Order\Item\CollectionFactory
    {
        return $this->orderItemCollectionFactory;
    }

    /**
     * @return OriginalSalesResourceModel\Order\Address\CollectionFactory
     */
    public function getAddressCollectionFactory(): OriginalSalesResourceModel\Order\Address\CollectionFactory
    {
        return $this->addressCollectionFactory;
    }

    /**
     * @return OriginalSalesResourceModel\Order\Payment\CollectionFactory
     */
    public function getPaymentCollectionFactory(): OriginalSalesResourceModel\Order\Payment\CollectionFactory
    {
        return $this->paymentCollectionFactory;
    }

    /**
     * @return OriginalSalesResourceModel\Order\Status\History\CollectionFactory
     */
    public function getHistoryCollectionFactory(): OriginalSalesResourceModel\Order\Status\History\CollectionFactory
    {
        return $this->historyCollectionFactory;
    }

    /**
     * @return OriginalSalesResourceModel\Order\Invoice\CollectionFactory
     */
    public function getInvoiceCollectionFactory(): OriginalSalesResourceModel\Order\Invoice\CollectionFactory
    {
        return $this->invoiceCollectionFactory;
    }

    /**
     * @return OriginalSalesResourceModel\Order\Shipment\CollectionFactory
     */
    public function getShipmentCollectionFactory(): OriginalSalesResourceModel\Order\Shipment\CollectionFactory
    {
        return $this->shipmentCollectionFactory;
    }

    /**
     * @return OriginalSalesResourceModel\Order\Creditmemo\CollectionFactory
     */
    public function getMemoCollectionFactory(): OriginalSalesResourceModel\Order\Creditmemo\CollectionFactory
    {
        return $this->memoCollectionFactory;
    }

    /**
     * @return OriginalSalesResourceModel\Order\Shipment\Track\CollectionFactory
     */
    public function getTrackCollectionFactory(): OriginalSalesResourceModel\Order\Shipment\Track\CollectionFactory
    {
        return $this->trackCollectionFactory;
    }

    /**
     * @return OriginalSalesResourceModel\Order\CollectionFactory
     */
    public function getSalesOrderCollectionFactory(): OriginalSalesResourceModel\Order\CollectionFactory
    {
        return $this->salesOrderCollectionFactory;
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    public function getProductListFactory(): \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
    {
        return $this->productListFactory;
    }

    /**
     * @return \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory
     */
    public function getTaxCollectionFactory(): \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory
    {
        return $this->taxCollectionFactory;
    }

    /**
     * @return ResourceModel\Invoice\CollectionFactory
     */
    public function getOeInvoiceCollectionFactory(): ResourceModel\Invoice\CollectionFactory
    {
        return $this->oeInvoiceCollectionFactory;
    }

    /**
     * @return ResourceModel\Creditmemo\CollectionFactory
     */
    public function getOeCreditmemoCollectionFactory(): ResourceModel\Creditmemo\CollectionFactory
    {
        return $this->oeCreditmemoCollectionFactory;
    }

    /**
     * @return ResourceModel\Shipment\CollectionFactory
     */
    public function getOeShipmentCollectionFactory(): ResourceModel\Shipment\CollectionFactory
    {
        return $this->oeShipmentCollectionFactory;
    }
}
