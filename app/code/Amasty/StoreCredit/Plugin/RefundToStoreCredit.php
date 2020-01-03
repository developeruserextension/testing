<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Plugin;

class RefundToStoreCredit
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $currency;

    /**
     * @var \Amasty\StoreCredit\Api\ManageCustomerStoreCreditInterface
     */
    private $manageCustomerStoreCredit;

    /**
     * @var \Amasty\StoreCredit\Model\ConfigProvider
     */
    private $configProvider;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    private $orderRepository;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Pricing\PriceCurrencyInterface $currency,
        \Amasty\StoreCredit\Api\ManageCustomerStoreCreditInterface $manageCustomerStoreCredit,
        \Amasty\StoreCredit\Model\ConfigProvider $configProvider,
        \Magento\Sales\Model\OrderRepository $orderRepository
    ) {
        $this->request = $request;
        $this->currency = $currency;
        $this->manageCustomerStoreCredit = $manageCustomerStoreCredit;
        $this->configProvider = $configProvider;
        $this->orderRepository = $orderRepository;
    }

    public function beforeRefund(
        \Magento\Sales\Model\Service\CreditmemoService $subject,
        \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo,
        $offlineRequested = false
    ) {
        if ($this->configProvider->isEnabled()) {
            if ($amount = $creditmemo->getAmstorecreditAmount()) {
                $order = $creditmemo->getOrder();
                $order->setAmstorecreditRefundedAmount($order->getAmstorecreditRefundedAmount() + $amount);
                $order->setAmstorecreditRefundedBaseAmount(
                    $order->getAmstorecreditRefundedBaseAmount() + $creditmemo->getAmstorecreditBaseAmount()
                );
                $this->manageCustomerStoreCredit->addOrSubtractStoreCredit(
                    $creditmemo->getCustomerId(),
                    $amount,
                    \Amasty\StoreCredit\Model\History\MessageProcessor::CREDIT_MEMO_REFUND,
                    [$this->orderRepository->get($creditmemo->getOrderId())->getIncrementId()],
                    $creditmemo->getStoreId()
                );
            }
        }

        return [$creditmemo, $offlineRequested];
    }
}
