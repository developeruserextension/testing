<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Plugin\Catalog\Block\Product;

use Magento\Catalog\Block\Product\View as MagentoView;

class View
{
    /**
     * @var \Amasty\HidePrice\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    public function __construct(
        \Amasty\HidePrice\Helper\Data $helper,
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        $this->helper = $helper;
        $this->layout = $layout;
    }

    /**
     * Hide Add to cart Button
     * @param MagentoView $subject
     * @param $result
     * @return string
     */
    public function afterToHtml(
        MagentoView $subject,
        $result
    ) {
        $matchedNames = [
            'product.info.addtocart.additional',
            'product.info.addtocart',
            'product.info.addtocart.bundle'
        ];

        if (in_array($subject->getNameInLayout(), $matchedNames)
            && $this->helper->getModuleConfig('information/hide_button')
            && $this->helper->isApplied($subject->getProduct())
        ) {
            preg_match('@<button[^>]*amquote-addto-button.*?<\/button>@s', $result, $quoteCartButton);
            $result = $this->helper->getNewAddToCartHtml($subject->getProduct());
            if (isset($quoteCartButton[0])) {
                //compatibility with Amasty Request a Quote
                $buttonBlock = $this->layout->createBlock(\Amasty\RequestQuote\Block\Product\Action::class);
                if ($buttonBlock) {
                    $result .= $buttonBlock->toHtml();
                }
            }
        }

        return $result;
    }

    /**
     * Hide meta data
     * @param MagentoView $subject
     */
    public function beforeToHtml(
        MagentoView $subject
    ) {
        if ($subject->getNameInLayout() == 'opengraph.general'
            && $subject->getProduct()
            && $this->helper->isNeedHideProduct($subject->getProduct())
        ) {
            $subject->getProduct()->setData('final_price', 0);
        }
    }
}
