<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Plugin\Catalog\Block\Product\ProductList;

use Magento\Catalog\Block\Product\ListProduct;

class Category extends AbstractList
{
    /**
     * @param ListProduct $subject
     * @param $html
     * @return string
     */
    public function afterToHtml(ListProduct $subject, $html)
    {
        $html = $this->replaceButtonFromHtml($html);

        return $html;
    }
}
