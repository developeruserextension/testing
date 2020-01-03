<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Plugin\Catalog\Model\Option;

class Value
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

    public function afterGetPrice(
        $subject,
        $result
    ) {
        if ($this->helper->isNeedHideProduct($subject->getOption()->getProduct())) {
            return '';
        }

        return $result;
    }
}
