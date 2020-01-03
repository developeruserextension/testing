<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Plugin\Conf;

use Amasty\Conf\Plugin\Product\View\Type\Configurable as ConfPlugin;
use Magento\Framework\Registry;
use Amasty\HidePrice\Helper\Data;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;

class Configurable
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        Data $helper,
        Registry $registry
    ) {
        $this->helper = $helper;
        $this->registry = $registry;
    }

    /**
     * @param ConfPlugin $subject
     * @param $result
     *
     * @return mixed
     */
    public function afterGetMatrixTitles(
        ConfPlugin $subject,
        $result
    ) {
        /** @var Product $product */
        $product = $this->registry->registry('current_product');
        if ($product
            && $this->helper->getModuleConfig('information/hide_price')
            && $product->getTypeId() == ConfigurableType::TYPE_CODE
            && $this->helper->isApplied($product)
        ) {
            unset($result['price']);
            unset($result['subtotal']);
        }

        return $result;
    }
}
