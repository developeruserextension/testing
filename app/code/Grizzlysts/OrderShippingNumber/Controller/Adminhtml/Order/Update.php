<?php

namespace Grizzlysts\OrderShippingNumber\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Backend\Model\UrlInterface as BackendUrl;

class Update extends \Magento\Backend\App\Action
{
    protected $orderRepository;

    protected $redirectFactory;

    public function __construct(
        Action\Context $context,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->redirectFactory = $redirectFactory;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $shippingNumber = $this->getRequest()->getParam('shipping_account_number');
        $order = $this->orderRepository->get($orderId);
        $order->setShippingAccountNumber($shippingNumber);

        $this->orderRepository->save($order);
        /**
         * @var $result \Magento\Framework\Controller\Result\Redirect
         */
        $result = $this->redirectFactory->create();
        return $result->setPath('sales/order/view', ['order_id' => $orderId]);
    }
}