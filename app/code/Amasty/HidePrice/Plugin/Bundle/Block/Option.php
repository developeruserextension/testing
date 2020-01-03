<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Plugin\Bundle\Block;

class Option
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

    public function aroundRenderPriceString(
        \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option $subject,
        \Closure $proceed,
        $selection,
        $includeContainer = true
    ) {
        if ($this->helper->isNeedHideProduct($subject->getProduct())) {
            return '';
        }

        return $proceed($selection, $includeContainer);
    }

    /**
     * remove + symbal
     * @param \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option $subject
     * @param $result
     * @return mixed
     */
    public function afterGetSelectionTitlePrice(
        \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option $subject,
        $result
    ) {
        $result = str_replace('<span class="price-notice">+</span>', '', $result);

        return $result;
    }
}
