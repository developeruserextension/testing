<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use MageWorx\OrderEditor\Api\OrderRepositoryInterface;
use MageWorx\OrderEditor\Api\QuoteRepositoryInterface;

/**
 * Class Payment
 *
 * @method Payment setOrderId(int $id)
 * @method int getOrderId()
 * @method Payment setPaymentMethod(string $method)
 * @method string getPaymentMethod()
 * @method Payment setPaymentTitle(string $title)
 * @method string getPaymentTitle()
 */
class Payment extends AbstractModel
{
    /**
     * @var Order
     */
    protected $order;

    /**
     * @var Quote
     */
    protected $quote;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var QuoteRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var OrderPaymentRepositoryInterface
     */
    protected $orderPaymentRepository;

    /**
     * Payment constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param Order $order
     * @param Quote $quote
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        QuoteRepositoryInterface $quoteRepository,
        OrderRepositoryInterface $orderRepository,
        OrderPaymentRepositoryInterface $orderPaymentRepository,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );

        $this->quoteRepository        = $quoteRepository;
        $this->orderRepository        = $orderRepository;
        $this->orderPaymentRepository = $orderPaymentRepository;
    }

    /**
     * Init params
     *
     * @param array $params
     */
    public function initParams(array $params)
    {
        if (isset($params['order_id'])) {
            $this->setOrderId($params['order_id']);
        }
        if (isset($params['payment_method'])) {
            $this->setPaymentMethod($params['payment_method']);
        }
        if (isset($params['payment_title'])) {
            $this->setPaymentTitle($params['payment_title']);
        }
    }

    /**
     * Update payment method
     *
     * @throws LocalizedException
     */
    public function updatePaymentMethod()
    {
        $this->loadOrder();
        $payment = $this->order->getPayment();
        $payment->setMethod($this->getPaymentMethod());
        /* Prepare date for additional information */
        if ($this->getPaymentTitle() !== null) {
            $payment->setAdditionalInformation(
                'method_title',
                $this->getPaymentTitle()
            );
        }

        $payment = $this->order->setPayment($payment);
        $this->orderPaymentRepository->save($payment);

        /* change data in quote */
        $quote   = $this->getQuote();
        $payment = $quote->getPayment();
        $payment->setMethod($this->getPaymentMethod());
        if ($this->getPaymentTitle() !== null) {
            $payment->setAdditionalInformation(
                'method_title',
                $this->getPaymentTitle()
            );
        }
    }

    /**
     * @return Order
     * @throws LocalizedException
     */
    protected function loadOrder(): Order
    {
        $id = $this->getOrderId();
        if ($this->order === null) {
            $this->order = $this->orderRepository->getById($id);
        }

        return $this->order;
    }

    /**
     * @return Quote
     * @throws LocalizedException
     */
    protected function getQuote(): Quote
    {
        if ($this->quote === null) {
            if ($this->order === null || !$this->order->getQuoteId()) {
                throw new LocalizedException(__('Order must be set'));
            }

            $this->quote = $this->quoteRepository->getById($this->order->getQuoteId());
        }

        return $this->quote;
    }
}
