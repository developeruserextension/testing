<?php

namespace Feefo\Reviews\Block;

use Feefo\Reviews\Api\Feefo\Data\WidgetConfigInterface;
use Feefo\Reviews\Api\Feefo\StorageInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context as TemplateContext;

/**
 * Class ProductRating
 */
class ProductRating extends AbstractWidget
{
    const PLACEHOLDER_PRODUCT_ID = "{{ product.id }}";

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var string
     */
    protected $_template = 'Feefo_Reviews::product_rating.phtml';

    /**
     * ProductRating constructor.
     * @param TemplateContext $context
     * @param StorageInterface $storage
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        StorageInterface $storage,
        Registry $registry,
        array $data
    ) {
        parent::__construct($context, $storage, $data);
        $this->registry = $registry;

        $this->setPlacement(WidgetConfigInterface::PLACEMENT_CUSTOM);
    }

    /**
     * Retrieve a code snippet for the current widget
     *
     * @return string
     */
    public function getSnippet()
    {
        if ($this->getCurrentProduct()) {
            $productId = $this->getCurrentProduct()->getId();
            $template = $this->getWidgetSnippets()->getProductStarsSnippet();

            return str_replace(static::PLACEHOLDER_PRODUCT_ID, $productId, $template) ;
        }

        return "";
    }

    /**
     * Check if the current widget is enabled
     *
     * @return boolean
     */
    public function isEnabledWidget()
    {
        return parent::isEnabledWidget() && $this->getWidgetSettings()->isProductReviewsWidget();
    }

    /**
     * Check if the current placement is right
     *
     * @return boolean
     */
    public function isRightPlacement()
    {
        return $this->getPlacement() === $this->getWidgetSettings()->getProductWidgetPlacement();
    }

    /**
     * Retrieve the current product
     *
     * @return Product
     */
    protected function getCurrentProduct()
    {
        return $this->registry->registry('product');
    }
}