<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Model\Locator\LocatorInterface;

class HidePrice extends AbstractModifier
{
    /**
     * @var LocatorInterface
     */
    protected $locator;
    /**
     * @param LocatorInterface $locator
     */
    public function __construct(
        LocatorInterface $locator
    ) {
        $this->locator = $locator;
    }

    public function modifyData(array $data)
    {
        $product = $this->locator->getProduct();
        $modelId = $product->getId();

        $data[$modelId][self::DATA_SOURCE_DEFAULT]['am_hide_price_mode'] = $product->getData('am_hide_price_mode');
        $data[$modelId][self::DATA_SOURCE_DEFAULT]['am_hide_price_customer_gr']
            = $product->getData('am_hide_price_customer_gr');
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        /* should be implement from Abstract class*/
        return $meta;
    }
}
