<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Raw as RawResult;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\OrderEditor\Api\OrderRepositoryInterface;
use MageWorx\OrderEditor\Api\QuoteRepositoryInterface;
use MageWorx\OrderEditor\Model\Order;
use MageWorx\OrderEditor\Model\Quote;
use MageWorx\OrderEditor\Model\Shipping as ShippingModel;
use MageWorx\OrderEditor\Model\Payment as PaymentModel;
use MageWorx\OrderEditor\Helper\Data;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class AbstractAction
 *
 * Abstract controller for all our edit actions
 */
abstract class AbstractAction extends Action
{
    const ADMIN_RESOURCE = 'Magento_Sales::actions_edit';

    /**
     * Action code used to say js "reload page" on success
     */
    const ACTION_RELOAD_PAGE = 'reload';

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var Quote
     */
    protected $quote;

    /**
     * @var ShippingModel
     */
    protected $shipping;

    /**
     * @var PaymentModel
     */
    protected $payment;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var QuoteRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * AbstractAction constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param RawFactory $resultRawFactory
     * @param Data $helper
     * @param ScopeConfigInterface $scopeConfig
     * @param QuoteRepositoryInterface $quoteRepository
     * @param ShippingModel $shipping
     * @param PaymentModel $payment
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        RawFactory $resultRawFactory,
        Data $helper,
        ScopeConfigInterface $scopeConfig,
        QuoteRepositoryInterface $quoteRepository,
        ShippingModel $shipping,
        PaymentModel $payment,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultRawFactory  = $resultRawFactory;
        $this->context           = $context;
        $this->helper            = $helper;
        $this->scopeConfig       = $scopeConfig;
        $this->quoteRepository   = $quoteRepository;
        $this->shipping          = $shipping;
        $this->payment           = $payment;
        $this->orderRepository   = $orderRepository;
    }

    /**
     * @return RawResult
     */
    public function execute(): RawResult
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

        /**
         * @var RawResult $result
         */
        $result = $this->resultRawFactory->create();
        $result->setContents(json_encode($response));

        return $result;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getResultHtml(): string
    {
        if (!$this->getRequest()->getParam('skip_save')) {
            $this->update();
        }

        return $this->prepareResponse();
    }

    /**
     * @return void
     */
    abstract protected function update();

    /**
     * @return string
     */
    abstract protected function prepareResponse(): string;

    /**
     * @return Order
     * @throws InputException
     * @throws NoSuchEntityException
     */
    protected function loadOrder(): Order
    {
        if ($this->order === null) {
            $id = $this->getRequest()->getParam('order_id');
            if ($id === null) {
                throw new InputException(__('The order id must be set.'));
            }
            $this->order = $this->orderRepository->getById($id);
            $this->helper->setOrder($this->order);
        }

        return $this->order;
    }

    /**
     * @return Order
     * @throws InputException
     * @throws NoSuchEntityException
     */
    protected function getOrder()
    {
        if ($this->order === null) {
            $this->loadOrder();
        }

        return $this->order;
    }

    /**
     * Drop existing order from memory
     *
     * @return $this
     */
    protected function clearOrder()
    {
        $this->order = null;

        return $this;
    }
}
