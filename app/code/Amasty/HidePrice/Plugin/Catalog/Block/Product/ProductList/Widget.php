<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Plugin\Catalog\Block\Product\ProductList;

use Magento\CatalogWidget\Block\Product\ProductsList;

class Widget extends AbstractList
{
    /**
     * @param ProductsList $subject
     * @param $html
     * @return string
     */
    public function afterToHtml(ProductsList $subject, $html)
    {
        $html = $this->replaceButtonFromHtml($html);

        return $html;
    }
}
