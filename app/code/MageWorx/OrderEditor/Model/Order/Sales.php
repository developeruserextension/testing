<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Model\Order;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DB\TransactionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order as OriginalOrder;
use Magento\Sales\Model\Order\Invoice as OriginalInvoice;
use Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader;
use MageWorx\OrderEditor\Helper\Data as Helper;
use MageWorx\OrderEditor\Model\Config\Source\Shipments\UpdateMode;
use MageWorx\OrderEditor\Model\Order as OrderEditorOrder;
use MageWorx\OrderEditor\Api\OrderRepositoryInterface;
use MageWorx\OrderEditor\Api\OrderItemRepositoryInterface;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;

/**
 * Class Sales
 */
class Sales extends AbstractModel
{
    /**
     * @var OrderEditorOrder
     */
    protected $order;

    /**
     * @var ShipmentLoader
     */
    protected $shipmentLoader;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var Helper
     */
    protected $helperData;

    /**
     * @var HttpRequest
     */
    protected $request;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var OrderItemRepositoryInterface
     */
    protected $oeOrderItemRepository;

    /**
     * @var OrderPaymentRepositoryInterface
     */
    protected $orderPaymentRepository;

    /**
     * Sales constructor.
     *
     * @param Context                         $context
     * @param Registry                        $registry
     * @param Helper                          $helperData
     * @param ShipmentLoader                  $shipmentLoader
     * @param TransactionFactory              $transactionFactory
     * @param HttpRequest                     $request
     * @param OrderRepositoryInterface        $orderRepository
     * @param OrderItemRepositoryInterface    $oeOrderItemRepository
     * @param OrderPaymentRepositoryInterface $orderPaymentRepository
     * @param AbstractResource|null           $resource
     * @param AbstractDb|null                 $resourceCollection
     * @param array                           $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Helper $helperData,
        ShipmentLoader $shipmentLoader,
        TransactionFactory $transactionFactory,
        HttpRequest $request,
        OrderRepositoryInterface $orderRepository,
        OrderItemRepositoryInterface $oeOrderItemRepository,
        OrderPaymentRepositoryInterface $orderPaymentRepository,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->shipmentLoader         = $shipmentLoader;
        $this->transactionFactory     = $transactionFactory;
        $this->helperData             = $helperData;
        $this->request                = $request;
        $this->orderRepository        = $orderRepository;
        $this->oeOrderItemRepository  = $oeOrderItemRepository;
        $this->orderPaymentRepository = $orderPaymentRepository;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @param OrderEditorOrder $order
     * @return $this
     */
    public function setOrder(OrderEditorOrder $order): self
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return OrderEditorOrder|null
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Update credit-memos, invoices, shipments
     *
     * @return bool
     */
    public function updateSalesObjects(): bool
    {
        try {
            $order = $this->getOrder();
            if ($order === null) {
                throw new LocalizedException(__('Order is not set!'));
            }

            if (!$order->isTotalWasChanged()
                && !$order->hasChangesInAmounts()
                && !$order->hasItemsWithIncreasedQty()
                && !$order->hasAddedItems()
                && !$order->hasItemsWithDecreasedQty()
                && !$order->hasRemovedItems()
            ) {
                return true;
            }

            if ($order->hasCreditmemos()) {
                $this->updateCreditMemos();
            } else {
                $this->updateInvoices();
            }

            $this->updateShipments();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function updateInvoices()
    {
        if ($this->getOrder()->hasInvoices()) {
            if ($this->isOrderTotalIncreased()
                && $this->helperData->getIsAllowKeepPrevInvoice()
            ) {
                $this->createInvoiceForOrder();
            } else {
                $this->removeAllInvoices();
                $this->createInvoiceForOrder();
            }
        }
    }

    /**
     * @return bool
     */
    protected function isOrderTotalIncreased(): bool
    {
        $order = $this->getOrder();

        return ($order->hasItemsWithIncreasedQty() || $order->hasAddedItems())
            && (!$order->hasItemsWithDecreasedQty() && !$order->hasRemovedItems());
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function updateCreditMemos()
    {
        if (!$this->isOrderTotalIncreased()
            || !$this->helperData->getIsAllowKeepPrevInvoice()
        ) {
            $this->removeAllCreditMemos();
        }
        $this->updateInvoices();
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function updateShipments()
    {
        $order = $this->getOrder();

        if ($order->hasShipments()) {
            switch ($this->helperData->getUpdateShipmentMode()) {
                case UpdateMode::MODE_UPDATE_ADD:
                    if (!$this->isOrderTotalIncreased()) {
                        $this->removeAllShipments();
                    }
                    $this->createShipmentForOrder();
                    break;
                case UpdateMode::MODE_UPDATE_REBUILD:
                    $this->removeAllShipments();
                    $this->createShipmentForOrder();
                    break;
                case UpdateMode::MODE_UPDATE_NOTHING:
                    if ($order->hasRemovedItems()
                        || $order->hasItemsWithDecreasedQty()
                    ) {
                        $this->removeAllShipments();
                    }
                    break;
            }
        }
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function removeAllCreditMemos()
    {
        /**
         * @var \Magento\Sales\Model\Order\Creditmemo $creditMemos
         */
        $creditMemos = $this->getOrder()->getCreditmemosCollection();
        foreach ($creditMemos as $creditMemo) {
            $creditMemo->delete();
        }

        $orderItems = $this->getOrder()->getItems();
        foreach ($orderItems as $orderItem) {
            $orderItem->setQtyRefunded(0)
                      ->setQtyReturned(0)
                      ->setDiscountRefunded(0)
                      ->setBaseDiscountRefunded(0)
                      ->setAmountRefunded(0)
                      ->setBaseAmountRefunded(0)
                      ->setTaxRefunded(0)
                      ->setBaseTaxRefunded(0)
                      ->setDiscountTaxCompensationRefunded(0)
                      ->setBaseDiscountTaxCompensationRefunded(0);

            $this->oeOrderItemRepository->save($orderItem);
        }

        $state = OriginalOrder::STATE_PROCESSING;

        $this->getOrder()
             ->setTaxRefunded(0)->setBaseTaxRefunded(0)
             ->setDiscountRefunded(0)->setBaseDiscountRefunded(0)
             ->setSubtotalRefunded(0)->setBaseSubtotalRefunded(0)
             ->setShippingRefunded(0)->setBaseShippingRefunded(0)
             ->setTotalOfflineRefunded(0)->setBaseTotalOfflineRefunded(0)
             ->setTotalRefunded(0)->setBaseTotalRefunded(0)
             ->setState($state);

        $this->orderRepository->save($this->getOrder());

        $payment = $this->getOrder()->getPayment();
        $payment->setAmountRefunded(0)
                ->setBaseAmountRefunded(0)
                ->setBaseAmountRefundedOnline(0)
                ->setShippingRefunded(0)
                ->setBaseShippingRefunded(0);

        $this->orderPaymentRepository->save($payment);
    }

    /**
     * @throws \Exception
     */
    protected function removeAllInvoices()
    {
        $invoices = $this->getOrder()->getInvoiceCollection();
        foreach ($invoices as $invoice) {
            $shippingMethodDataInRequest = $this->request->getPost('shipping_method');
            if (!$invoice->isCanceled() && !empty($shippingMethodDataInRequest)) {
                $invoice->cancel();
                $transaction = $this->transactionFactory->create();
                $transaction->addObject($invoice->getOrder())->save();
            }
            $invoice->delete();
        }

        $invoices->removeAllItems();

        foreach ($this->getOrder()->getAllItems() as $orderItem) {
            $orderItem->setQtyInvoiced(0)
                      ->setRowInvoiced(0)
                      ->setBaseRowInvoiced(0)
                      ->setTaxInvoiced(0)
                      ->setBaseTaxInvoiced(0)
                      ->setDiscountInvoiced(0)
                      ->setBaseDiscountInvoiced(0)
                      ->setGwPriceInvoiced(0)
                      ->setGwBasePriceInvoiced(0)
                      ->setGwTaxAmountInvoiced(0)
                      ->setGwBaseTaxAmountInvoiced(0)
                      ->setDiscountTaxCompensationInvoiced(0)
                      ->setBaseDiscountTaxCompensationInvoiced(0);

            $this->oeOrderItemRepository->save($orderItem);
        }

        $this->getOrder()
             ->setTaxInvoiced(0)->setBaseTaxInvoiced(0)
             ->setDiscountInvoiced(0)->setBaseDiscountInvoiced(0)
             ->setSubtotalInvoiced(0)->setBaseSubtotalInvoiced(0)
             ->setTotalInvoiced(0)->setBaseTotalInvoiced(0)
             ->setShippingInvoiced(0)->setBaseShippingInvoiced(0)
             ->setTotalPaid(0)->setBaseTotalPaid(0)
             ->setState(OriginalOrder::STATE_PROCESSING);

        $this->orderRepository->save($this->getOrder());
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function removeAllShipments()
    {
        $shipments = $this->getOrder()->getShipmentsCollection();
        foreach ($shipments as $shipment) {
            $shipment->delete();
        }

        $orderItems = $this->getOrder()->getItems();
        foreach ($orderItems as $orderItem) {
            $orderItem->setQtyShipped(0);
            $this->oeOrderItemRepository->save($orderItem);
        }

        $state = OriginalOrder::STATE_PROCESSING;

        $this->getOrder()->setState($state);
        $this->orderRepository->save($this->getOrder());

        $payment = $this->getOrder()->getPayment();
        $payment->setShippingCaptured(0)
                ->setBaseShippingCaptured(0)
                ->setShippingRefunded(0)
                ->setBaseShippingRefunded(0);

        $this->orderPaymentRepository->save($payment);
    }

    /**
     * @return void
     * @throws \Exception
     * @throws LocalizedException
     */
    protected function createInvoiceForOrder()
    {
        $this->getOrder()
             ->setState(OriginalOrder::STATE_PROCESSING);
        $this->orderRepository->save($this->getOrder());

        if ($this->getOrder()->canInvoice()) {
            $order   = $this->getOrder();
            $invoice = $this->getOrder()->prepareInvoice();
            if (!$invoice) {
                throw new LocalizedException(__('Can not create invoice'));
            }

            $invoice->setRequestedCaptureCase(OriginalInvoice::CAPTURE_OFFLINE);
            $invoice->register();

            $transaction = $this->transactionFactory->create();
            $transaction->addObject($invoice)->addObject($invoice->getOrder())->save();

            /* hack for fix lost $0.01 */
            $payment = $order->getPayment();
            $payment->setBaseAmountPaid($order->getBaseGrandTotal())
                    ->setAmountPaid($order->getGrandTotal());

            $this->orderPaymentRepository->save($payment);

            $order->setBaseTotalPaid($order->getBaseGrandTotal())
                  ->setTotalPaid($order->getGrandTotal());

            $this->orderRepository->save($order);
        }
    }

    /**
     * @return void
     * @throws \Exception
     * @throws LocalizedException
     */
    protected function createShipmentForOrder()
    {
        if ($this->getOrder()->canShip()) {
            $this->shipmentLoader->setOrderId($this->getOrder()->getId());
            $shipment = $this->shipmentLoader->load();
            if (!$shipment) {
                throw new LocalizedException(__('Can not create shipment'));
            }

            $shipment->register();

            $transaction = $this->transactionFactory->create();
            $transaction->addObject($shipment)->addObject($shipment->getOrder())->save();
        }
    }
}
