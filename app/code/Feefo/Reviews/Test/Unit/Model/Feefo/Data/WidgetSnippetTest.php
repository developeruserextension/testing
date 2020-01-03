<?php

namespace Feefo\Reviews\Test\Unit\Model\Feefo\Data;

use Feefo\Reviews\Model\Feefo\Data\WidgetSnippet;
use Feefo\Reviews\Test\Unit\AbstractTestCase;

/**
 * Class WidgetSnippetTest
 */
class WidgetSnippetTest extends AbstractTestCase
{
    /**
     * Sample Value
     */
    const SAMPLE_VALUE = 'value';

    /**
     * WidgetSnippet
     *
     * @var WidgetSnippet
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
        $this->widgetConfig = $this->objectManager->getObject(WidgetSnippet::class);
        $this->widgetConfig->setData([
            WidgetSnippet::DATA_SERVICE_SNIPPET => self::SAMPLE_VALUE,
            WidgetSnippet::DATA_PRODUCT_STARS_SNIPPET => self::SAMPLE_VALUE,
            WidgetSnippet::DATA_PRODUCT_BASE_SNIPPET => self::SAMPLE_VALUE,
            WidgetSnippet::DATA_PRODUCT_LIST_SNIPPET => self::SAMPLE_VALUE,
        ]);
    }

    /**
     * Test getServiceSnippet method
     *
     * @return void
     */
    public function testGetServiceSnippet()
    {
        $result = $this->widgetConfig->getServiceSnippet();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setServiceSnippet method
     *
     * @return void
     */
    public function testSetServiceSnippet()
    {
        $this->widgetConfig->setServiceSnippet(self::SAMPLE_VALUE);
        $result = $this->widgetConfig->getData(WidgetSnippet::DATA_SERVICE_SNIPPET);

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test getProductStarsSnippet method
     *
     * @return void
     */
    public function testGetProductStarsSnippet()
    {
        $result = $this->widgetConfig->getProductStarsSnippet();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setProductStarsSnippet method
     *
     * @return void
     */
    public function testSetProductStarsSnippet()
    {
        $this->widgetConfig->setProductStarsSnippet(self::SAMPLE_VALUE);
        $result = $this->widgetConfig->getData(WidgetSnippet::DATA_PRODUCT_STARS_SNIPPET);

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test getProductBaseSnippet method
     *
     * @return void
     */
    public function testGetProductBaseSnippet()
    {
        $result = $this->widgetConfig->getProductBaseSnippet();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setProductBaseSnippet method
     *
     * @return void
     */
    public function testSetProductBaseSnippet()
    {
        $this->widgetConfig->setProductBaseSnippet(self::SAMPLE_VALUE);
        $result = $this->widgetConfig->getData(WidgetSnippet::DATA_PRODUCT_BASE_SNIPPET);

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test getProductListSnippet method
     *
     * @return void
     */
    public function testGetProductListSnippet()
    {
        $result = $this->widgetConfig->getProductListSnippet();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setProductListSnippet method
     *
     * @return void
     */
    public function testSetProductListSnippet()
    {
        $this->widgetConfig->setProductListSnippet(self::SAMPLE_VALUE);
        $result = $this->widgetConfig->getData(WidgetSnippet::DATA_PRODUCT_LIST_SNIPPET);

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }
}