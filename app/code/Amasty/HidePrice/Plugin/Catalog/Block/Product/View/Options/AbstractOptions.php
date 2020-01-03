<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Plugin\Catalog\Block\Product\View\Options;

class AbstractOptions
{
    /**
     * @var \Amasty\HidePrice\Helper\Data
     */
    private $helper;

    public function __construct(
        \Amasty\HidePrice\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Hide option price html
     * @param $subject
     * @param callable $proceed
     * @return string
     */
    public function aroundGetFormatedPrice(
        $subject,
        callable $proceed
    ) {
        if ($this->helper->isNeedHideProduct($subject->getProduct())) {
            return '';
        }

        return $proceed();
    }

    public function afterGetValuesHtml($subject, $result)
    {
        $result = str_replace('<span class="price-notice">+</span>', '', $result);

        return $result;
    }
}
