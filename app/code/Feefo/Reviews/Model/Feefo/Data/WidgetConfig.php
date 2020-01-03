<?php

namespace Feefo\Reviews\Model\Feefo\Data;

use Feefo\Reviews\Api\Feefo\Data\WidgetConfigInterface;

/**
 * Class WidgetConfig
 */
class WidgetConfig extends JsonableDataObject implements WidgetConfigInterface
{
    /**
     * Retrieve should the native review system be enabled or not
     *
     * @return boolean
     */
    public function isNativePlatformReviewSystem()
    {
        return $this->getData(static::NATIVE_PLATFORM_REVIEW_SYSTEM);
    }

    /**
     * Configure should the native review system be enabled or not
     *
     * @param boolean $value
     * @return $this
     */
    public function setNativePlatformReviewSystem($value)
    {
        return $this->setData(static::NATIVE_PLATFORM_REVIEW_SYSTEM, $value);
    }

    /**
     * Retrieve should the product review widget be enabled or not
     *
     * @return boolean
     */
    public function isProductReviewsWidget()
    {
        return $this->getData(static::PRODUCT_REVIEWS_WIDGET);
    }

    /**
     * Configure should the product review widget be enabled or not
     *
     * @param boolean $value
     * @return $this
     */
    public function setProductReviewsWidget($value)
    {
        return $this->setData(static::PRODUCT_REVIEWS_WIDGET, $value);
    }

    /**
     * Retrieve placement of the product widget
     *
     * @return string
     */
    public function getProductWidgetPlacement()
    {
        return $this->getData(static::PRODUCT_WIDGET_PLACEMENT);
    }

    /**
     * Configure placement of the product widget
     *
     * @param string $placement
     * @return $this
     */
    public function setProductWidgetPlacement($placement)
    {
        return $this->setData(static::PRODUCT_WIDGET_PLACEMENT, $placement);
    }

    /**
     * Retrieve should the product listing rating stars be enabled or not
     *
     * @return boolean
     */
    public function isProductListingStars()
    {
        return $this->getData(static::PRODUCT_LISTING_STARS);
    }

    /**
     * Configure should the product listing rating stars be enabled or not
     *
     * @param boolean $value
     * @return $this
     */
    public function setProductListingStars($value)
    {
        return $this->setData(static::PRODUCT_LISTING_STARS, $value);
    }

    /**
     * Retrieve placement of the product listing stars widget
     *
     * @return $this
     */
    public function getProductListingStarsPlacement()
    {
        return $this->getData(static::PRODUCT_LISTING_STARS_PLACEMENT);
    }

    /**
     * Configure placement of the product listing stars widget
     *
     * @param $placement
     * @return mixed
     */
    public function setProductListingStarsPlacement($placement)
    {
        return $this->setData(static::PRODUCT_LISTING_STARS_PLACEMENT, $placement);
    }

    /**
     * Retrieve should the service widget be enabled or not
     *
     * @return $this
     */
    public function isServiceReviewsWidget()
    {
        return $this->getData(static::SERVICE_REVIEWS_WIDGET);
    }

    /**
     * Configure should the service widget be enabled or not
     *
     * @param boolean $value
     * @return $this
     */
    public function setServiceReviewsWidget($value)
    {
        return $this->setData(static::SERVICE_REVIEWS_WIDGET, $value);
    }
}