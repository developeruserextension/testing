<?php

namespace Feefo\Reviews\Api\Feefo\Data;

/**
 * Interface ConfigInterface
 *
 * Data Service Contract that describes widget configs of Feefo service
 */
interface WidgetConfigInterface
{
    /**
     * Available placements
     */

    const PLACEMENT_AUTO = 'AUTO';

    const PLACEMENT_CUSTOM = 'CUSTOM';

    /**
     * Available configs
     */
    const NATIVE_PLATFORM_REVIEW_SYSTEM = 'nativePlatformReviewSystem';

    const PRODUCT_REVIEWS_WIDGET = 'productReviewsWidget';

    const PRODUCT_WIDGET_PLACEMENT = 'productWidgetPlacement';

    const PRODUCT_LISTING_STARS = 'productListingStars';

    const PRODUCT_LISTING_STARS_PLACEMENT = 'productListingStarsPlacement';
    
    const SERVICE_REVIEWS_WIDGET = 'serviceReviewsWidget';

    /**
     * Retrieve should the native review system be enabled or not
     *
     * @return boolean
     */
    public function isNativePlatformReviewSystem();

    /**
     * Configure should the native review system be enabled or not
     *
     * @param boolean $value
     * @return $this
     */
    public function setNativePlatformReviewSystem($value);

    /**
     * Retrieve should the product review widget be enabled or not
     *
     * @return boolean
     */
    public function isProductReviewsWidget();

    /**
     * Configure should the product review widget be enabled or not
     *
     * @param boolean $value
     * @return $this
     */
    public function setProductReviewsWidget($value);

    /**
     * Retrieve placement of the product widget
     *
     * @return string
     */
    public function getProductWidgetPlacement();

    /**
     * Configure placement of the product widget
     *
     * @param string $placement
     * @return $this
     */
    public function setProductWidgetPlacement($placement);

    /**
     * Retrieve should the product listing rating stars be enabled or not
     *
     * @return boolean
     */
    public function isProductListingStars();

    /**
     * Configure should the product listing rating stars be enabled or not
     *
     * @param boolean $value
     * @return $this
     */
    public function setProductListingStars($value);

    /**
     * Retrieve placement of the product listing stars widget
     *
     * @return $this
     */
    public function getProductListingStarsPlacement();

    /**
     * Configure placement of the product listing stars widget
     *
     * @param $placement
     * @return mixed
     */
    public function setProductListingStarsPlacement($placement);

    /**
     * Retrieve should the service widget be enabled or not
     *
     * @return $this
     */
    public function isServiceReviewsWidget();

    /**
     * Configure should the service widget be enabled or not
     *
     * @param boolean $value
     * @return $this
     */
    public function setServiceReviewsWidget($value);
}