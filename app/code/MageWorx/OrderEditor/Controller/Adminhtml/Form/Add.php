<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Controller\Adminhtml\Form;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\OrderEditor\Model\Edit\Quote;
use MageWorx\OrderEditor\Model\Order;
use MageWorx\OrderEditor\Api\OrderRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\DataObject;

/**
 * Class Add
 */
class Add extends Action
{
    /**
     * @var Order
     */
    protected $order;

    /**
     * @var RawFactory
     */
    protected $resultFactory;

    /**
     * @var Quote $processor
     */
    protected $processor;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param RawFactory $resultFactory
     * @param Quote $processor
     * @param Order $order
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        RawFactory $resultFactory,
        Quote $processor,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->resultFactory     = $resultFactory;
        $this->processor         = $processor;
        $this->orderRepository   = $orderRepository;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        try {
            $response = [
                'result' => $this->prepareResultHtml(),
                'status' => true
            ];
        } catch (\Exception $e) {
            $response = [
                'error'  => $e->getMessage(),
                'status' => false
            ];
        }

        $updateResult = new DataObject($response);
        $json         = $this->prepareResponse($updateResult);
        $result       = $this->resultFactory->create()->setContents($json);

        return $result;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function prepareResultHtml()
    {
        $resultPage = $this->resultPageFactory->create();

        $formContainer = $resultPage->getLayout()
                                    ->getBlock('ordereditor_order_items_form_container');
        if (empty($formContainer)) {
            $message = __('Can not load block');
            throw new LocalizedException($message);
        }

        $order      = $this->getOrder();
        $orderItems = $this->getNewOrderItems();

        $formContainer->setOrder($order);
        $formContainer->setNewOrderItems($orderItems);

        return $formContainer->toHtml();
    }

    /**
     * @param string $updateResult
     * @return string
     */
    protected function prepareResponse($updateResult)
    {
        if ($updateResult) {
            $json = $updateResult->toJson();
        } else {
            $json = '{"error":"Can not get response","status":"false"}';
        }

        return "<script type=\"text/javascript\">
                    //<![CDATA[ \r\n var iFrameResponse = " . $json . ";\r\n //]]>
                </script>";
    }

    /**
     * @return Order
     * @throws NoSuchEntityException
     */
    protected function getOrder()
    {
        if (empty($this->order)) {
            $id = (int)$this->getRequest()->getParam('order_id');

            $this->order = $this->orderRepository->getById($id);
            if (!$this->order->getEntityId()) {
                throw new NoSuchEntityException(
                    __('Can not load order with id %1', $id)
                );
            }
        }

        return $this->order;
    }

    /**
     * @return \MageWorx\OrderEditor\Model\Order\Item[]
     * @throws NoSuchEntityException
     */
    protected function getNewOrderItems(): array
    {
        $items = $this->getRequest()->getParam('item', []);
        $order = $this->getOrder();

        return $this->processor->createNewOrderItems($items, $order);
    }
}
