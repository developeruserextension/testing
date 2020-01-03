<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Model;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\CalculatorFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order as OriginalOrder;
use Magento\Sales\Model\Order\Creditmemo as OriginalCreditMemo;
use Magento\Sales\Model\Order\Creditmemo\CommentFactory as CreditmemoCommentFactory;
use Magento\Sales\Model\Order\Creditmemo\Config as CreditmemoConfig;
use Magento\Sales\Model\Order\Creditmemo\Item as OriginalCreditMemoItem;
use Magento\Sales\Model\Order\InvoiceFactory as OriginalInvoiceFactory;
use Magento\Sales\Model\OrderFactory as OriginalOrderFactory;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\Comment\CollectionFactory as CreditmemoCommentCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\Item\CollectionFactory as CreditmemoItemCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use MageWorx\OrderEditor\Api\OrderItemRepositoryInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

/**
 * Class Creditmemo
 */
class Creditmemo extends OriginalCreditMemo
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $originalOrderRepository;

    /**
     * @var OrderItemRepositoryInterface
     */
    protected $oeOrderItemRepository;

    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;

    /**
     * Creditmemo constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param CreditmemoConfig $creditmemoConfig
     * @param OriginalOrderFactory $orderFactory
     * @param CreditmemoItemCollectionFactory $cmItemCollectionFactory
     * @param CalculatorFactory $calculatorFactory
     * @param StoreManagerInterface $storeManager
     * @param CreditmemoCommentFactory $commentFactory
     * @param CreditmemoCommentCollectionFactory $commentCollectionFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderItemRepositoryInterface $oeOrderItemRepository
     * @param MessageManagerInterface $messageManager
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     * @param OriginalInvoiceFactory|null $invoiceFactory
     * @param ScopeConfigInterface|null $scopeConfig
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        CreditmemoConfig $creditmemoConfig,
        OriginalOrderFactory $orderFactory,
        CreditmemoItemCollectionFactory $cmItemCollectionFactory,
        CalculatorFactory $calculatorFactory,
        StoreManagerInterface $storeManager,
        CreditmemoCommentFactory $commentFactory,
        CreditmemoCommentCollectionFactory $commentCollectionFactory,
        PriceCurrencyInterface $priceCurrency,
        OrderRepositoryInterface $orderRepository,
        OrderItemRepositoryInterface $oeOrderItemRepository,
        MessageManagerInterface $messageManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->originalOrderRepository = $orderRepository;
        $this->oeOrderItemRepository   = $oeOrderItemRepository;
        $this->messageManager          = $messageManager;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $creditmemoConfig,
            $orderFactory,
            $cmItemCollectionFactory,
            $calculatorFactory,
            $storeManager,
            $commentFactory,
            $commentCollectionFactory,
            $priceCurrency,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\MageWorx\OrderEditor\Model\ResourceModel\Creditmemo::class);
    }

    /**
     * @return void
     */
    public function cancel()
    {
        $this->cancelItems();
        $this->updateOrderStatusAfterCancel();
        $this->cancelOrderTotal();
    }

    /**
     * @return void
     */
    protected function cancelItems()
    {
        $creditmemoItems = $this->getItemsCollection();

        /** @var OriginalCreditMemoItem $creditmemoItem */
        foreach ($creditmemoItems as $creditmemoItem) {
            $orderItems = $this->getOrder()->getItems();

            foreach ($orderItems as $orderItem) {
                if ($orderItem->getProductId() != $creditmemoItem->getProductId()) {
                    continue;
                }

                $amountRefunded = $orderItem->getAmountRefunded()
                    - $creditmemoItem->getRowTotal();

                $baseAmountRefunded = $orderItem->getBaseAmountRefunded()
                    - $creditmemoItem->getRowTotal();

                $taxRefunded = $orderItem->getTaxRefunded()
                    - $creditmemoItem->getTaxAmount();

                $baseTaxRefunded = $orderItem->getBaseTaxRefunded()
                    - $creditmemoItem->getBaseTaxAmount();

                $discountRefunded = $orderItem->getDiscountRefunded()
                    - $creditmemoItem->getDiscountAmount();

                $baseDiscountRefunded = $orderItem->getBaseDiscountRefunded()
                    - $creditmemoItem->getBaseDiscountAmount();

                $hiddenTaxRefunded = $orderItem->getDiscountTaxCompensationRefunded()
                    - $creditmemoItem->getDiscountTaxCompensationAmount();

                $baseHiddenTaxRefunded = $orderItem->getBaseDiscountTaxCompensationRefunded()
                    - $creditmemoItem->getBaseDiscountTaxCompensationAmount();

                $qtyRefunded = $orderItem->getQtyRefunded()
                    - $creditmemoItem->getQty();

                if ($amountRefunded >= 0) {
                    $orderItem->setAmountRefunded($amountRefunded);
                }

                if ($baseAmountRefunded >= 0) {
                    $orderItem->setBaseAmountRefunded($baseAmountRefunded);
                }

                if ($taxRefunded >= 0) {
                    $orderItem->setTaxRefunded($taxRefunded);
                }

                if ($baseTaxRefunded >= 0) {
                    $orderItem->setBaseTaxRefunded($baseTaxRefunded);
                }

                if ($discountRefunded >= 0) {
                    $orderItem->setDiscountRefunded($discountRefunded);
                }

                if ($baseDiscountRefunded >= 0) {
                    $orderItem->setBaseDiscountRefunded($baseDiscountRefunded);
                }

                if ($hiddenTaxRefunded >= 0) {
                    $orderItem->setDiscountTaxCompensationRefunded($hiddenTaxRefunded);
                }

                if ($baseHiddenTaxRefunded >= 0) {
                    $orderItem->setBaseDiscountTaxCompensationRefunded($baseHiddenTaxRefunded);
                }

                if ($qtyRefunded >= 0) {
                    $orderItem->setQtyRefunded($qtyRefunded);
                }

                try {
                    $this->oeOrderItemRepository->save($orderItem);
                } catch (LocalizedException $exception) {
                    $this->messageManager->addErrorMessage(
                        __(
                            'Something goes wrong while cancelling credit memos. Original error message: %1',
                            $exception->getMessage()
                        )
                    );
                }
            }
        }
    }

    /**
     * @return void
     */
    protected function cancelOrderTotal()
    {
        $order = $this->getOrder();

        $totalRefunded     = $order->getTotalRefunded() - $this->getBaseGrandTotal();
        $baseTotalRefunded = $order->getTotalRefunded() - $this->getBaseGrandTotal();

        $order->setTotalRefunded($totalRefunded);
        $order->setBaseTotalRefunded($baseTotalRefunded);

        $this->originalOrderRepository->save($order);
    }

    /**
     * @return void
     */
    protected function updateOrderStatusAfterCancel()
    {
        $order = $this->getOrder();

        if ($order->hasInvoices() && $order->hasShipments()) {
            $state = OriginalOrder::STATE_COMPLETE;
        } elseif ($order->hasInvoices()) {
            $state = OriginalOrder::STATE_PROCESSING;
        } else {
            $state = $order->getState();
        }

        $order->setData('state', $state);
        $order->setStatus($order->getConfig()->getStateDefaultStatus($state));
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    public function beforeDelete(): self
    {
        $this->_commentCollectionFactory
            ->create()
            ->setCreditmemoFilter($this->getId())
            ->walk('delete');

        $this->_cmItemCollectionFactory
            ->create()
            ->setCreditmemoFilter($this->getId())
            ->walk('delete');

        $this->deleteFromGrid();

        parent::beforeDelete();

        return $this;
    }

    /**
     * @return void
     */
    protected function deleteFromGrid()
    {
        $id = (int)$this->getId();
        if (!empty($id)) {
            $resource   = $this->getResource();
            $connection = $resource->getConnection();

            $salesCreditMemosGridTable = $resource->getTable(
                'sales_creditmemo_grid'
            );

            $connection->delete(
                $salesCreditMemosGridTable,
                [
                    'entity_id = ?' => $id
                ]
            );
        }
    }
}
