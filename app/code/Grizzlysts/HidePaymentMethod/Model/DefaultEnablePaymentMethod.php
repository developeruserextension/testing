<?php
namespace Grizzlysts\HidePaymentMethod\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class DefaultEnablePaymentMethod implements ConfigProviderInterface
{
    protected $scopeConfig;

    /**
     * DefaultEnablePaymentMethod constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getConfig()
    {
        $config = [];
        $config['defaultpayment'] = $this->scopeConfig->getValue('payment/grizzly_hide_payment/default_method');

        return $config;
    }
}