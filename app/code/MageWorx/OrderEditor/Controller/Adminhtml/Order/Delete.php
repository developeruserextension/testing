<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\OrderEditor\Api\OrderRepositoryInterface;

/**
 * Class Delete
 */
class Delete extends Action
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * Delete constructor.
     *
     * @param Action\Context $context
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Action\Context $context,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return ResultInterface|ResponseInterface
     */
    public function execute(): Redirect
    {
        $orderId = (int)$this->_request->getParam('order_id');
        $result = $this->resultRedirectFactory->create();
        if ($orderId) {
            try {
                $this->orderRepository->deleteById($orderId);
                $message = __('The order %1 was successfully deleted.', $orderId);
                $this->messageManager->addSuccessMessage($message);
                $result->setPath('sales/order/index');
            } catch (LocalizedException $exception) {
                $message = $exception->getMessage();
                $this->messageManager->addSuccessMessage($message);
                $result->setPath('sales/order/view', ['id' => $orderId]);
            }
        } else {
            $message = __('Order id is not set.');
            $this->messageManager->addSuccessMessage($message);
            $result->setPath('sales/order/index');
        }

        return $result;
    }
}
