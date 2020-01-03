<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\Store as StoreModel;
use MageWorx\OrderEditor\Api\OrderRepositoryInterface;
use MageWorx\OrderEditor\Api\QuoteRepositoryInterface as CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Model\Order;
use MageWorx\OrderEditor\Model\Config\Source\Shipments\UpdateMode;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use MageWorx\OrderEditor\Model\Order as OrderEditorOrderModel;
use MageWorx\OrderEditor\Model\Quote as OrderEditorQuoteModel;

/**
 * Class Data
 */
class Data extends AbstractHelper
{
    /**
     * XML config return to stock
     */
    const XML_PATH_RETURN_TO_STOCK      = 'mageworx_order_management/order_editor/order_items/return_to_stock';
    const XML_PATH_INVOICE_UPDATE_MODE  =
        'mageworx_order_management/order_editor/invoice_shipment_refund/invoice_update_mode';
    const XML_PATH_SHIPMENT_UPDATE_MODE =
        'mageworx_order_management/order_editor/invoice_shipment_refund/shipments_update_mode';

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param SerializerInterface $serializer
     * @param CartRepositoryInterface $cartRepository
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        Context $context,
        Registry $registry,
        SerializerInterface $serializer,
        CartRepositoryInterface $cartRepository,
        StoreRepositoryInterface $storeRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->coreRegistry    = $registry;
        $this->serializer      = $serializer;
        $this->cartRepository  = $cartRepository;
        $this->storeRepository = $storeRepository;
        $this->orderRepository = $orderRepository;
        parent::__construct($context);
    }

    /**
     * Get enable permanent order item removal
     *
     * @return bool
     */
    public function getReturnToStock() : bool
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_RETURN_TO_STOCK);
    }

    /**
     * Allow keep previous invoice and add new one
     *
     * @return bool
     */
    public function getIsAllowKeepPrevInvoice() : bool
    {
        return $this->scopeConfig->getValue(self::XML_PATH_INVOICE_UPDATE_MODE) ==
            UpdateMode::MODE_UPDATE_ADD;
    }

    /**
     * Get update shipments mode
     *
     * @return string
     * @see    \MageWorx\OrderEditor\Model\Config\Source\Shipments\UpdateMode
     */
    public function getUpdateShipmentMode() : string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SHIPMENT_UPDATE_MODE);
    }

    /**
     * Get current order
     *
     * @return mixed
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('ordereditor_order');
    }

    /**
     * Set current order
     *
     * @param  Order $order
     * @return void
     */
    public function setOrder(Order $order)
    {
        $this->coreRegistry->register('ordereditor_order', $order, true);
    }

    /**
     * Get current order entity id
     *
     * @return int|null
     */
    public function getOrderId()
    {
        if ($this->coreRegistry->registry('current_order')) {
            $order = $this->coreRegistry->registry('current_order');
        }
        if ($this->coreRegistry->registry('order')) {
            $order = $this->coreRegistry->registry('order');
        }

        if (isset($order)) {
            $orderId = (int)$order->getId();
        } else {
            $orderId = null;
        }

        return $orderId;
    }

    /**
     * Retrieve quote model object
     *
     * @return \Magento\Quote\Model\Quote
     * @throws LocalizedException
     */
    public function getQuote(): CartInterface
    {
        $quote = $this->coreRegistry->registry('ordereditor_quote');
        if (!$quote) {
            /** @var \MageWorx\OrderEditor\Model\Order $order */
            $order = $this->coreRegistry->registry('ordereditor_order');
            if (!$order) {
                throw new LocalizedException(
                    __('There is no Order in the registry')
                );
            }
            $quote = $this->getQuoteByOrder($order);
        }

        $this->coreRegistry->register('ordereditor_quote', $quote, true);

        return $quote;
    }

    /**
     * Set current quote
     *
     * @param  CartInterface $quote
     * @return void
     */
    public function setQuote(CartInterface $quote)
    {
        $this->coreRegistry->register('ordereditor_quote', $quote);
    }

    /**
     * Retrieve customer identifier
     *
     * @return int
     * @throws LocalizedException
     */
    public function getCustomerId()
    {
        $order = $this->getOrder();
        if (!$order) {
            throw new LocalizedException(
                __('There is no Order in the registry')
            );
        }

        return $order->getCustomerId() ? (int)$order->getCustomerId() : null;
    }

    /**
     * Retrieve store model object
     *
     * @return StoreInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getStore(): StoreInterface
    {
        return $this->storeRepository->getById($this->getStoreId());
    }

    /**
     * Retrieve store identifier
     *
     * @return int
     * @throws LocalizedException
     */
    public function getStoreId(): int
    {
        $order = $this->getOrder();
        if (!$order) {
            throw new LocalizedException(
                __('There is no Order in the registry')
            );
        }

        return (int)$order->getStoreId();
    }

    /**
     * Round and format price
     *
     * @return string
     */
    public function roundAndFormatPrice($price): string
    {
        return number_format($price, 2, '.', '');
    }

    /**
     * @param mixed $value
     * @return array|bool|float|int|string|null
     */
    public function decodeBuyRequestValue($value)
    {
        return $this->serializer->unserialize($value);
    }

    /**
     * @param array $value
     * @return bool|string
     */
    public function encodeBuyRequestValue($value)
    {
        return $this->serializer->serialize($value);
    }

    /**
     * @param OrderEditorOrderModel $order
     * @return OrderEditorQuoteModel
     * @throws LocalizedException
     */
    public function getQuoteByOrder(OrderEditorOrderModel $order): OrderEditorQuoteModel
    {
        $storeId = $order->getStoreId();
        /** @var StoreModel $store */
        $store   = $this->storeRepository->getById($storeId);
        $quoteId = $order->getQuoteId();

        try {
            $quote = $this->cartRepository->getById($quoteId)
                                                 ->setStore($store);
        } catch (NoSuchEntityException $exception) {
            $quote = $this->recreateEmptyQuote($order);
        }

        return $quote;
    }

    /**
     * Create new quote with empty items and shipping address
     *
     * @param OrderEditorOrderModel $order
     * @return CartInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function recreateEmptyQuote(OrderEditorOrderModel $order): CartInterface
    {
        $storeId = $order->getStoreId();
        /** @var StoreModel $store */
        $store   = $this->storeRepository->getById($storeId);

        $quote = $this->cartRepository->getEmptyEntity()
                                      ->setStore($store);
        $shippingAddress = $quote->getShippingAddress();
        $orderAddressData = $order->getShippingAddress()->getData();
        unset($orderAddressData['entity_id']);
        $shippingAddress->addData($orderAddressData);
        $quote->setShippingAddress($shippingAddress);
        $this->cartRepository->save($quote);
        $order->setQuoteId($quote->getId());
        $this->orderRepository->save($order);

        return $quote;
    }
}
