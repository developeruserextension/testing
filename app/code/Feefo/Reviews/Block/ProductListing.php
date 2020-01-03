<?php

namespace Feefo\Reviews\Block;

use Feefo\Reviews\Api\Feefo\Data\WidgetConfigInterface;
use Feefo\Reviews\Api\Feefo\StorageInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Template;

/**
 * Class ProductListing
 */
class ProductListing extends AbstractWidget
{
    const PLACEHOLDER_PRODUCT_ID = "{{ product.id }}";

    const PLACEHOLDER_TEMPLATE_NAME = "{{ template.name }}";

    const DEFAULT_TEMPLATE = 'product-page-orange-stars-only-85x18.png';

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var string
     */
    protected $_template = 'Feefo_Reviews::product_list_rating.phtml';

    /**
     * ProductListing constructor
     *
     * @param Template\Context $context
     * @param StorageInterface $storage
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        StorageInterface $storage,
        array $data
    ) {
        parent::__construct($context, $storage, $data);

        $this->setPlacement(WidgetConfigInterface::PLACEMENT_CUSTOM);
        $this->setRatingTemplate($this->getDefaultRatingTemplate());
    }

    /**
     * Check if the current widget is enabled
     *
     * @return boolean
     */
    public function isEnabledWidget()
    {
        return parent::isEnabledWidget() && $this->getWidgetSettings()->isProductListingStars();
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
            $templateName = $this->getRatingTemplate();

            $template = $this->getWidgetSnippets()->getProductListSnippet();

            return str_replace(
                array(static::PLACEHOLDER_PRODUCT_ID, static::PLACEHOLDER_TEMPLATE_NAME),
                array($productId, $templateName),
                $template
            );
        }

        return "";
    }

    /**
     * Check if the current rendering is for the right placement
     *
     * @return boolean
     */
    public function isRightPlacement()
    {
        return $this->getPlacement() === $this->getWidgetSettings()->getProductListingStarsPlacement();
    }

    /**
     * Configure a product for the widget
     *
     * @param Product $product
     */
    public function setCurrentProduct($product)
    {
        $this->product = $product;
    }

    /**
     * Retrieve a product for the widget
     *
     * @return Product
     */
    protected function getCurrentProduct()
    {
        return $this->product;
    }

    /**
     * Retrieve a default rating template
     *
     * @return string
     */
    protected function getDefaultRatingTemplate()
    {
        return static::DEFAULT_TEMPLATE;
    }
}