<?php

namespace Grizzlysts\HidePaymentMethod\Observer;

use Magento\Framework\Event\ObserverInterface;

class PaymentMethodAvailable implements ObserverInterface
{
    private $state;

    private $scopeConfig;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->state = $state;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $area = $this->state->getAreaCode();
        if ($area === 'adminhtml') {
            return;
        }
        $listMethod = $this->scopeConfig->getValue('payment/grizzly_hide_payment/hide_specific_method');
        $listMethod = explode(',', $listMethod);

        if ( in_array($observer->getEvent()->getMethodInstance()->getCode(), $listMethod))
        {
            $checkResult = $observer->getEvent()->getResult();
            $checkResult->setData('is_available', false);
        }
    }
}