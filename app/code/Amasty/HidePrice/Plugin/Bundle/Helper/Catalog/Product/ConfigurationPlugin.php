<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Plugin\Bundle\Helper\Catalog\Product;

use \Magento\Bundle\Helper\Catalog\Product\Configuration;
use \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;

/**
 * Class ConfigurationPlugin
 */
class ConfigurationPlugin
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
     * @param Configuration $subject
     * @param callable $proceed
     * @param ItemInterface $item
     * @return array
     */
    public function aroundGetBundleOptions(Configuration $subject, callable $proceed, ItemInterface $item)
    {
        $options = $proceed($item);
        if ($this->helper->isNeedHidePrice($item->getProduct())) {
            foreach ($options as &$option) {
                if (isset($option['value']) && is_array($option['value'])) {
                    $value = current($option['value']);
                    $pricePosition = strpos($value, '<span class="price">');
                    if ($pricePosition !== false) {
                        $option['value'] = [substr($value, 0, $pricePosition)];
                    }
                }
            }
        }

        return $options;
    }
}