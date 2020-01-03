<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Observer;

use Amasty\StoreCredit\Api\Data\StoreCreditInterface;
use Amasty\StoreCredit\Model\History\MessageProcessor;

class ReturnStoreCredit implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Amasty\StoreCredit\Api\ManageCustomerStoreCreditInterface
     */
    private $manageCustomerStoreCredit;

    /**
     * @var \Amasty\StoreCredit\Model\ConfigProvider
     */
    private $configProvider;

    public function __construct(
        \Amasty\StoreCredit\Model\ConfigProvider $configProvider,
        \Amasty\StoreCredit\Api\ManageCustomerStoreCreditInterface $manageCustomerStoreCredit
    ) {
        $this->manageCustomerStoreCredit = $manageCustomerStoreCredit;
        $this->configProvider = $configProvider;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->configProvider->isEnabled()) {
            $order = $observer->getData('order');
            $storeCreditLeft = $order->getAmstorecreditBaseAmount()
                - $order->getAmstorecreditRefundedBaseAmount();
            if (($customerId = $order->getCustomerId()) && $storeCreditLeft > 0) {
                $this->manageCustomerStoreCredit->addOrSubtractStoreCredit(
                    $customerId,
                    $storeCreditLeft,
                    MessageProcessor::ORDER_CANCEL,
                    [
                        $order->getIncrementId()
                    ],
                    $order->getStoreId()
                );
                $order->setAmstorecreditRefunded($order->getAmstorecreditAmount());
                $order->setAmstorecreditRefundedBaseAmount($order->getAmstorecreditRefundedBaseAmount());
            }
        }
    }
}
