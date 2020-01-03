<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Plugin\RequestQuote\Model\HidePrice;

use Amasty\HidePrice\Helper\Data;
use Magento\Catalog\Api\Data\ProductInterface;

class ProviderPlugin
{
    /**
     * @var Data
     */
    private $helper;

    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param \Amasty\RequestQuote\Model\HidePrice\Provider $subject
     * @param bool $result
     * @param ProductInterface $product
     * @return bool
     */
    public function afterIsHidePrice($subject, $result, ProductInterface $product)
    {
        return $this->helper->isNeedHidePrice($product);
    }
}
