<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Observer;

use Magento\Sales\Model\Order;

class IsCanCreditMemo implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Amasty\StoreCredit\Model\ConfigProvider
     */
    private $configProvider;

    public function __construct(\Amasty\StoreCredit\Model\ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->configProvider->isEnabled()) {
            $order = $observer->getData('order');

            if ($order->canUnhold()) {
                return;
            }

            if ($order->isCanceled() || $order->getState() === Order::STATE_CLOSED) {
                return;
            }

            if (($order->getAmstorecreditInvoicedAmount() - $order->getAmstorecreditRefundedAmount()) > 0) {
                $order->setForcedCanCreditmemo(true);
            } elseif ($order->getAmstorecreditInvoicedAmount()) {
                $hide = true;
                foreach ($order->getItems() as $item) {
                    $qty = (double)$item->getQtyOrdered() - (double)$item->getQtyRefunded();
                    if ($qty > 0.0001) {
                        $hide = false;
                        $order->setForcedCanCreditmemo(true);
                        break;
                    }
                }

                if ($hide) {
                    $order->setForcedCanCreditmemo(false);
                }
            }
        }
    }
}
