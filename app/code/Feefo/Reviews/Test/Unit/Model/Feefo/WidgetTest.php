<?php

namespace Feefo\Reviews\Test\Unit\Model\Feefo;

use Feefo\Reviews\Api\Feefo\Helper\HmacInterface;
use Feefo\Reviews\Api\Feefo\HttpClientInterface;
use Feefo\Reviews\Api\Feefo\StorageInterface;
use Feefo\Reviews\Model\Feefo\Data\JsonableDataObject;
use Feefo\Reviews\Model\Feefo\Widget;
use Feefo\Reviews\Test\Unit\AbstractTestCase;
use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

/**
 * Class WidgetTest
 */
class WidgetTest extends AbstractTestCase
{
    /**
     * HttpClientInterface mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|HttpClientInterface
     */
    protected $httpClient;

    /**
     * StorageInterface mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|StorageInterface
     */
    protected $storage;

    /**
     * HmacInterface mock
     *
     * @var  \PHPUnit_Framework_MockObject_MockObject|HmacInterface
     */
    protected $hmac;

    /**
     * LoggerInterface mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|LoggerInterface
     */
    protected $logger;

    /**
     * ObjectFactory mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|ObjectFactory
     */
    protected $objectFactory;

    /**
     * ScopeConfigInterface mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Widget
     *
     * @var Widget
     */
    protected $widget;

    /**
     * SetUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->httpClient = $this->basicMock(HttpClientInterface::class);
        $this->storage = $this->basicMock(StorageInterface::class);
        $this->logger = $this->basicMock(LoggerInterface::class);
        $this->hmac = $this->basicMock(HmacInterface::class);
        $this->objectFactory = $this->basicMock(ObjectFactory::class);
        $this->scopeConfig = $this->basicMock(ScopeConfigInterface::class);
        $this->widget = $this->objectManager->getObject(Widget::class, [
            'scopeConfig' => $this->scopeConfig,
            'storage' => $this->storage,
            'hmac' => $this->hmac,
            'httpClient' => $this->httpClient,
            'objectFactory' => $this->objectFactory,
            'logger' => $this->logger
        ]);
    }

    /**
     * Test getSettings method
     *
     * @return void
     */
    public function testGetSettings()
    {
        $sampleValue = 'text';
        $jsonableDataObject = $this->basicMock(JsonableDataObject::class);
        $this->storage->expects($this->once())
            ->method('getPluginId')
            ->willReturn(1);
        $this->hmac->expects($this->once())
            ->method('get')
            ->willReturn(null);
        $this->logger->expects($this->exactly(2))
            ->method('debug')
            ->willReturn(null);
        $this->scopeConfig->expects($this->exactly(2))
            ->method('getValue')
            ->willReturn($sampleValue);
        $this->httpClient->expects($this->once())
            ->method('get')
            ->willReturn(null);
        $this->objectFactory->expects($this->once())
            ->method('create')
            ->willReturn($jsonableDataObject);
        $jsonableDataObject->expects($this->once())
            ->method('setJSON')
            ->willReturn(null);
        $result = $this->widget->getSettings();

        self::assertEquals($jsonableDataObject, $result);
    }
}