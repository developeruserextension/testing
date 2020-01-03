<?php

namespace Grizzlysts\AmastyShippingTableRates\Plugin\Carrier;

class Table
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    private $_rateResultFactory;

    private $state;

    private $_code = 'amstrates';

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Framework\App\State $state
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->_rateResultFactory = $rateResultFactory;
        $this->state = $state;
    }

    /**
     * @param \Amasty\ShippingTableRates\Model\Carrier\Table $subject
     * @param \Magento\Shipping\Model\Rate\Result $result
     */
    public function afterCollectRates(\Amasty\ShippingTableRates\Model\Carrier\Table $subject, $result)
    {
        $area = $this->state->getAreaCode();
        if ($area === 'adminhtml') {
            return $result;
        }
        $allRates = $result->getAllRates();
        $specifyMethods = $this->scopeConfig->getValue('carriers/amstrates/hide_specific_method');
        $specifyMethods = explode(',', $specifyMethods);
        $amastyCode = $this->_code;
        $hideMethod = [];
        foreach ($specifyMethods as $specifyMethod)
        {
            $hideMethod[] = $amastyCode.$specifyMethod;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $newResult */
        $newResult = $this->_rateResultFactory->create();

        foreach ($allRates as $key => $method)
        {
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $methodName = $method->getMethod();
            if (in_array($methodName, $hideMethod))
            {
                unset($allRates[$key]);
            }
            else
            {
                $newResult->append($method);
            }
        }

        return $newResult;
    }
}