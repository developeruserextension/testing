<?php

namespace Feefo\Reviews\Test\Unit\Model\Feefo\Data;

use Feefo\Reviews\Model\Feefo\Data\WidgetConfig;
use Feefo\Reviews\Test\Unit\AbstractTestCase;

/**
 * Class WidgetConfigTest
 */
class WidgetConfigTest extends AbstractTestCase
{
    /**
     * Sample Value
     */
    const SAMPLE_VALUE = 'value';

    /**
     * WidgetConfig
     *
     * @var WidgetConfig
     */
    protected $widgetConfig;

    /**
     * SetUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->widgetConfig = $this->objectManager->getObject(WidgetConfig::class);
        $this->widgetConfig->setData([
            WidgetConfig::NATIVE_PLATFORM_REVIEW_SYSTEM => self::SAMPLE_VALUE,
            WidgetConfig::PRODUCT_REVIEWS_WIDGET => self::SAMPLE_VALUE,
            WidgetConfig::PRODUCT_WIDGET_PLACEMENT => self::SAMPLE_VALUE,
            WidgetConfig::PRODUCT_LISTING_STARS => self::SAMPLE_VALUE,
            WidgetConfig::PRODUCT_LISTING_STARS_PLACEMENT => self::SAMPLE_VALUE,
            WidgetConfig::SERVICE_REVIEWS_WIDGET => self::SAMPLE_VALUE,
        ]);
    }

    /**
     * Test isNativePlatformReviewSystem method
     *
     * @return void
     */
    public function testIsNativePlatformReviewSystem()
    {
        $result = $this->widgetConfig->isNativePlatformReviewSystem();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setNativePlatformReviewSystem method
     *
     * @return void
     */
    public function testSetNativePlatformReviewSystem()
    {
        $this->widgetConfig->setNativePlatformReviewSystem(self::SAMPLE_VALUE);
        $result = $this->widgetConfig->getData(WidgetConfig::NATIVE_PLATFORM_REVIEW_SYSTEM);

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test isProductReviewsWidget method
     *
     * @return void
     */
    public function testIsProductReviewsWidget()
    {
        $result = $this->widgetConfig->isProductReviewsWidget();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setProductReviewsWidget method
     *
     * @return void
     */
    public function testSetProductReviewsWidget()
    {
        $this->widgetConfig->setProductReviewsWidget(self::SAMPLE_VALUE);
        $result = $this->widgetConfig->getData(WidgetConfig::PRODUCT_REVIEWS_WIDGET);

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test getProductWidgetPlacement method
     *
     * @return void
     */
    public function testGetProductWidgetPlacement()
    {
        $result = $this->widgetConfig->getProductWidgetPlacement();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setProductWidgetPlacement method
     *
     * @return void
     */
    public function testSetProductWidgetPlacement()
    {
        $this->widgetConfig->setProductWidgetPlacement(self::SAMPLE_VALUE);
        $result = $this->widgetConfig->getData(WidgetConfig::PRODUCT_WIDGET_PLACEMENT);

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test isProductListingStars method
     *
     * @return void
     */
    public function testIsProductListingStars()
    {
        $result = $this->widgetConfig->isProductListingStars();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setProductListingStars method
     *
     * @return void
     */
    public function testSetProductListingStars()
    {
        $this->widgetConfig->setProductListingStars(self::SAMPLE_VALUE);
        $result = $this->widgetConfig->getData(WidgetConfig::PRODUCT_LISTING_STARS);

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test getProductListingStarsPlacement method
     *
     * @return void
     */
    public function testGetProductListingStarsPlacement()
    {
        $result = $this->widgetConfig->getProductListingStarsPlacement();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setProductListingStarsPlacement method
     *
     * @return void
     */
    public function testSetProductListingStarsPlacement()
    {
        $this->widgetConfig->setProductListingStarsPlacement(self::SAMPLE_VALUE);
        $result = $this->widgetConfig->getData(WidgetConfig::PRODUCT_LISTING_STARS_PLACEMENT);

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test isServiceReviewsWidget method
     *
     * @return void
     */
    public function testIsServiceReviewsWidget()
    {
        $result = $this->widgetConfig->isServiceReviewsWidget();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setServiceReviewsWidget method
     *
     * @return void
     */
    public function testSetServiceReviewsWidget()
    {
        $this->widgetConfig->setServiceReviewsWidget(self::SAMPLE_VALUE);
        $result = $this->widgetConfig->getData(WidgetConfig::SERVICE_REVIEWS_WIDGET);

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }
}