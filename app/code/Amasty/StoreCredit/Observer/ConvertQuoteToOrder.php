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

class ConvertQuoteToOrder implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Amasty\StoreCredit\Api\ManageCustomerStoreCreditInterface
     */
    private $manageCustomerStoreCredit;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    private $authorization;

    public function __construct(
        \Amasty\StoreCredit\Api\ManageCustomerStoreCreditInterface $manageCustomerStoreCredit,
        \Magento\Framework\AuthorizationInterface $authorization
    ) {
        $this->manageCustomerStoreCredit = $manageCustomerStoreCredit;
        $this->authorization = $authorization;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getData('order');
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getData('quote');
        if ($quote->getData(SalesFieldInterface::AMSC_USE)) {
            $order->setAmstorecreditBaseAmount($quote->getAmstorecreditBaseAmount());
            $order->setAmstorecreditAmount($quote->getAmstorecreditAmount());

            $this->manageCustomerStoreCredit->addOrSubtractStoreCredit(
                $quote->getCustomerId(),
                -$quote->getAmstorecreditBaseAmount(),
                MessageProcessor::ORDER_PAY,
                [$order->getIncrementId()],
                $quote->getStoreId()
            );
        }
    }
}
