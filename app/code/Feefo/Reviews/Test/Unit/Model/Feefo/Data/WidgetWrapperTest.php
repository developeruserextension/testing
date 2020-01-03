<?php

namespace Feefo\Reviews\Test\Unit\Model\Feefo\Data;

use Feefo\Reviews\Model\Feefo\Data\WidgetWrapper;
use Feefo\Reviews\Test\Unit\AbstractTestCase;
use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\Json\Helper\Data;

/**
 * Class WidgetWrapperTest
 */
class WidgetWrapperTest extends AbstractTestCase
{
    /**
     * Sample Value
     */
    const SAMPLE_VALUE = 'value';

    /**
     * Object Factory mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|ObjectFactory
     */
    protected $objectFactory;

    /**
     * Json Helper mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|Data
     */
    protected $jsonHelper;

    /**
     * WidgetWrapper
     *
     * @var WidgetWrapper
     */
    protected $widgetWrapper;

    /**
     * SetUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->jsonHelper = $this->basicMock(Data::class);
        $this->objectFactory = $this->basicMock(ObjectFactory::class);
        $data = [];
        $this->widgetWrapper = $this->objectManager->getObject(WidgetWrapper::class, [
            'jsonHelper' => $this->jsonHelper,
            'objectFactory' => $this->objectFactory,
            'data' => $data
        ]);
        $this->widgetWrapper->setData([
            WidgetWrapper::DATA_REDIRECT_URL => self::SAMPLE_VALUE,
            WidgetWrapper::DATA_ACCESS_TOKEN => self::SAMPLE_VALUE,
            WidgetWrapper::DATA_WIDGET_SETTINGS => self::SAMPLE_VALUE,
            WidgetWrapper::DATA_SNIPPET_PREVIEW => self::SAMPLE_VALUE,
        ]);
    }

    /**
     * Test getWidgetSettings method
     *
     * @return void
     */
    public function testGetWidgetSettings()
    {
        $this->objectFactory->expects($this->once())
            ->method('create')
            ->willReturn(true);
        $result = $this->widgetWrapper->getWidgetSettings();

        self::assertEquals(true, $result);
    }

    /**
     * Test getWidgetSettings method
     *
     * @return void
     */
    public function testGetSnippetsPreview()
    {
        $this->objectFactory->expects($this->once())
            ->method('create')
            ->willReturn(true);
        $result = $this->widgetWrapper->getWidgetSettings();

        self::assertEquals(true, $result);
    }

    /**
     * Test getRedirectUrl method
     *
     * @return void
     */
    public function testGetRedirectUrl()
    {
        $result = $this->widgetWrapper->getRedirectUrl();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test getAccessToken method
     *
     * @return void
     */
    public function testGetAccessToken()
    {
        $result = $this->widgetWrapper->getAccessToken();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }
}