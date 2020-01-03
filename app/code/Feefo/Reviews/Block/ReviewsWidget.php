<?php

namespace Feefo\Reviews\Block;

use Feefo\Reviews\Api\Feefo\Data\WidgetConfigInterface;
use Feefo\Reviews\Api\Feefo\StorageInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context as TemplateContext;

/**
 * Class ReviewsWidget
 */
class ReviewsWidget extends AbstractWidget
{
    const KEY_ALREADY_REGISTRED = "feefo_reviews_reviews_registered";

    const PLACEHOLDER_PRODUCT_ID = "{{ product.id }}";

    /**
     * @var  Registry
     */
    protected $registry;

    /**
     * @var string
     */
    protected $_template = 'Feefo_Reviews::reviews.phtml';

    /**
     * ReviewsWidget constructor.
     * @param TemplateContext $context
     * @param StorageInterface $storage
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
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
        // the widget should be rendered only once
        $this->setAlreadyRendered();

        if ($this->getCurrentProduct()) {
            $productId = $this->getCurrentProduct()->getId();
            $template = $this->getWidgetSnippets()->getProductBaseSnippet();

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
     * Check if the current widget can be rendered
     *
     * @return boolean
     */
    public function canRender()
    {
        return $this->registry->registry(static::KEY_ALREADY_REGISTRED) === null;
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

    /**
     * Enable flag that the widget has been already rendered on the page
     *
     * @return void
     */
    protected function setAlreadyRendered()
    {
        $this->registry->register(static::KEY_ALREADY_REGISTRED, true);
    }
}