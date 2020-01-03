<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Observer;

use Amasty\StoreCredit\Api\Data\SalesFieldInterface;
use Amasty\StoreCredit\Api\Data\StoreCreditInterface;
use Amasty\StoreCredit\Model\History\MessageProcessor;

class RemoveStoreCreditFromPayment implements \Magento\Framework\Event\ObserverInterface
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
            $cart = $observer->getData('cart');
            $salesEntity = $cart->getSalesModel();
            if ($salesEntity->getDataUsingMethod('entity_type') === 'order'
                || $salesEntity->getDataUsingMethod(SalesFieldInterface::AMSC_USE)
            ) {
                $value = abs($salesEntity->getDataUsingMethod(SalesFieldInterface::AMSC_BASE_AMOUNT));
                if ($value > 0.0001) {
                    $cart->addDiscount((double)$value);
                }
            }
        }
    }
}
