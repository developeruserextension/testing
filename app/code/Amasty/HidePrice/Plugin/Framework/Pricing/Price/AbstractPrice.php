<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Plugin\Framework\Pricing\Price;

use Magento\Framework\Pricing\Price\AbstractPrice as NativePricing;
use Amasty\HidePrice\Helper\Data;
use Magento\Framework\Pricing\Amount\Base as BaseAmount;
use Magento\Framework\Registry;

class AbstractPrice
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Data $helper, Registry $registry)
    {
        $this->helper = $helper;
        $this->registry = $registry;
    }

    /**
     * @param NativePricing $subject
     * @param $result
     *
     * @return BaseAmount
     */
    public function afterGetAmount(
        NativePricing $subject,
        $result
    ) {
        if ($this->helper->getModuleConfig('information/hide_price')
            && !$this->registry->registry('hideprice_off')
            && $this->helper->isApplied($subject->getProduct())
        ) {
            $result = new BaseAmount(0);
        }

        return $result;
    }
}
