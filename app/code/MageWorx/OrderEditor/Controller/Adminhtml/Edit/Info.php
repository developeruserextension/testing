<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Controller\Adminhtml\Edit;

use MageWorx\OrderEditor\Controller\Adminhtml\AbstractAction;
use MageWorx\OrderEditor\Helper\Data;
use MageWorx\OrderEditor\Model\Shipping as ShippingModel;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use MageWorx\OrderEditor\Model\Payment as PaymentModel;
use MageWorx\OrderEditor\Api\OrderRepositoryInterface;
use MageWorx\OrderEditor\Api\QuoteRepositoryInterface;

/**
 * Class Info
 */
class Info extends AbstractAction
{
    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * Info constructor.
     *
     * @param Context                   $context
     * @param PageFactory               $resultPageFactory
     * @param RawFactory                $resultRawFactory
     * @param Data                      $helper
     * @param ScopeConfigInterface      $scopeConfig
     * @param QuoteRepositoryInterface  $quoteRepository
     * @param ShippingModel             $shipping
     * @param PaymentModel              $payment
     * @param OrderRepositoryInterface  $orderRepository
     * @param TimezoneInterface         $timezone
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
        OrderRepositoryInterface $orderRepository,
        TimezoneInterface $timezone
    ) {
        parent::__construct(
            $context,
            $resultPageFactory,
            $resultRawFactory,
            $helper,
            $scopeConfig,
            $quoteRepository,
            $shipping,
            $payment,
            $orderRepository
        );
        $this->localeDate      = $timezone;
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     * @throws \Exception
     */
    protected function update()
    {
        $order    = $this->loadOrder();
        $params   = $this->getRequest()->getParams();
        $infoData = !empty($params['order']['info']) ? $params['order']['info'] : [];
        if (isset($infoData['created_at'])) {
            $createdAt = $this->localeDate->date($infoData['created_at'], null, true);
            $createdAt->setTimezone(new \DateTimeZone($this->localeDate->getDefaultTimezone()));
            $infoData['created_at'] = $createdAt;
        }
        $order->addData($infoData);
        try {
            $this->orderRepository->save($order);
        } catch (\Exception $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    protected function prepareResponse(): string
    {
        return static::ACTION_RELOAD_PAGE;
    }
}
