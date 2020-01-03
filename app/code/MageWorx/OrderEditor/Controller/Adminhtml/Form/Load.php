<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Controller\Adminhtml\Form;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\OrderEditor\Model\Order;
use MageWorx\OrderEditor\Model\Quote;
use MageWorx\OrderEditor\Model\Address;
use MageWorx\OrderEditor\Api\OrderRepositoryInterface;
use MageWorx\OrderEditor\Api\QuoteRepositoryInterface;

/**
 * Class Load
 */
class Load extends Action
{
    /**
     * Page factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageFactory;

    /**
     * Raw factory
     *
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $rawFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Order Editor helper
     *
     * @var \MageWorx\OrderEditor\Helper\Data
     */
    protected $helperData;

    /**
     * @var string
     */
    protected $blockId;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var Quote
     */
    protected $quote;

    /**
     * @var Address
     */
    protected $address;

    /**
     * @var \MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Payment\Method
     */
    protected $method;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var QuoteRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Load constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $rawFactory
     * @param \MageWorx\OrderEditor\Helper\Data $helperData
     * @param \Magento\Framework\Registry $registry
     * @param \MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Payment\Method $method
     * @param Order $order
     * @param Quote $quote
     * @param Address $address
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\RawFactory $rawFactory,
        \MageWorx\OrderEditor\Helper\Data $helperData,
        \Magento\Framework\Registry $registry,
        \MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Payment\Method $method,
        OrderRepositoryInterface $orderRepository,
        QuoteRepositoryInterface $quoteRepository,
        Address $address
    ) {
        $this->rawFactory      = $rawFactory;
        $this->pageFactory     = $pageFactory;
        $this->helperData      = $helperData;
        $this->coreRegistry    = $registry;
        $this->orderRepository = $orderRepository;
        $this->quoteRepository = $quoteRepository;
        $this->address         = $address;
        $this->method          = $method;

        return parent::__construct($context);
    }

    /**
     * Render block form
     *
     * @return ResultInterface
     * @throws \Exception
     */
    public function execute(): ResultInterface
    {
        try {
            $response = [
                'result' => $this->getResultHtml(),
                'status' => true
            ];
        } catch (\Exception $e) {
            $response = [
                'error'  => $e->getMessage(),
                'status' => false
            ];
        }

        $result = $this->rawFactory->create()->setContents(json_encode($response));

        return $result;
    }

    /**
     * @return string
     */
    protected function getResultHtml(): string
    {
        $this->blockId = $this->getRequest()->getParam('block_id');

        $this->registerOrder();
        $this->registerQuote();
        $this->registerAddress();

        if ($this->blockId === 'payment_method') {
            $this->method->setPaymentMethod();
        }

        $resultPage = $this->pageFactory->create();
        $resultPage->addHandle('ordereditor_load_block_' . $this->blockId);

        return $resultPage->getLayout()->renderElement('content');
    }

    /**
     * Register order
     *
     * @return void
     * @throws LocalizedException
     */
    private function registerOrder()
    {
        $orderId     = $this->getRequest()->getParam('order_id');
        $this->order = $this->orderRepository->getById($orderId);
        $this->helperData->setOrder($this->order);
    }

    /**
     * Register quote
     *
     * @return void
     * @throws \Exception
     */
    private function registerQuote()
    {
        if ($this->blockId == 'shipping_method'
            || $this->blockId == 'payment_method'
        ) {
            $quoteId = $this->order->getQuoteId();
            try {
                $this->quote = $this->quoteRepository->getById($quoteId);
                $this->helperData->setQuote($this->quote);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('Required quote with id %1 does not exist.', $quoteId));
            }
        }
    }

    /**
     * Register order address
     *
     * @return void
     * @throws LocalizedException
     */
    private function registerAddress()
    {
        $addressId = '';

        if ($this->blockId == 'billing_address') {
            $addressId = $this->order->getBillingAddressId();
        } elseif ($this->blockId == 'shipping_address') {
            $addressId = $this->order->getShippingAddressId();
        }

        if (!$addressId) {
            return;
        }

        $address = $this->address->loadAddress($addressId);
        if ($address->getId()) {
            $this->coreRegistry->register('order_address', $address);
        } else {
            throw new LocalizedException(__('Can not load address'));
        }
    }
}
