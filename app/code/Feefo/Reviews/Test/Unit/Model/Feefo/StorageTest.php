<?php

namespace Feefo\Reviews\Test\Unit\Model\Feefo\Data;

use Feefo\Reviews\Api\Feefo\StorageInterface;
use Feefo\Reviews\Model\Feefo\Data\WidgetConfig;
use Feefo\Reviews\Model\Feefo\Data\WidgetSnippet;
use Feefo\Reviews\Model\Feefo\Storage;
use Feefo\Reviews\Test\Unit\AbstractTestCase;
use Magento\Framework\App\Cache\Manager as CacheManager;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Module\Status as ModuleStatus;

/**
 * Class StorageTest
 */
class StorageTest extends AbstractTestCase
{
    /**
     * Sample value
     */
    const SAMPLE_VALUE = 'value';

    /**
     * ScopeConfigInterface
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * WriterInterface mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|WriterInterface
     */
    protected $storageWriter;

    /**
     * CacheManager mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|CacheManager
     */
    protected $cacheManager;

    /**
     * JsonHelper mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|JsonHelper
     */
    protected $jsonHelper;

    /**
     * ModuleStatus mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|ModuleStatus
     */
    protected $moduleStatus;

    /**
     * WidgetConfig mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|WidgetConfig
     */
    protected $widgetConfig;

    /**
     * WidgetSnippet
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|WidgetSnippet
     */
    protected $widgetSnippet;

    /**
     * Config Mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|WidgetSnippet
     */
    protected $configMock;

    /**
     * Storage
     *
     * @var Storage
     */
    protected $storage;

    /**
     * SetUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->scopeConfig = $this->basicMock(ScopeConfigInterface::class);
        $this->storageWriter = $this->basicMock(WriterInterface::class);
        $this->jsonHelper = $this->basicMock(JsonHelper::class);
        $this->cacheManager = $this->basicMock(CacheManager::class);
        $this->moduleStatus = $this->basicMock(ModuleStatus::class);
        $this->widgetConfig = $this->basicMock(WidgetConfig::class);
        $this->widgetSnippet = $this->basicMock(WidgetSnippet::class);
        $this->configMock = $this->basicMock(ConfigInterface::class);
        $this->storage = $this->objectManager->getObject(Storage::class, [
            'scopeConfig' => $this->scopeConfig,
            'storageWriter' => $this->storageWriter,
            'jsonHelper' => $this->jsonHelper,
            'cacheManager' => $this->cacheManager,
            'moduleStatus' => $this->moduleStatus,
            'widgetConfig' => $this->widgetConfig,
            'widgetSnippet' => $this->widgetSnippet
        ]);
    }

    /**
     * Test getAccessKey method
     *
     * @return void
     */
    public function testGetAccessKey()
    {
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->willReturn(self::SAMPLE_VALUE);
        $result = $this->storage->getAccessKey();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setAccessKey method
     *
     * @return void
     */
    public function testSetAccessKey()
    {
        $this->storageWriter->expects($this->once())
            ->method('save')
            ->willReturn(null);
        $this->cacheManager->expects($this->once())
            ->method('clean')
            ->willReturn(null);
        $result = $this->storage->setAccessKey(self::SAMPLE_VALUE);

        self::assertEquals($this->storage, $result);
    }

    /**
     * Test getUserId method
     *
     * @return void
     */
    public function testGetUserId()
    {
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->willReturn(self::SAMPLE_VALUE);
        $result = $this->storage->getUserId();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setUserId method
     *
     * @return void
     */
    public function testSetUserId()
    {
        $this->storageWriter->expects($this->once())
            ->method('save')
            ->willReturn(null);
        $this->cacheManager->expects($this->once())
            ->method('clean')
            ->willReturn(null);
        $result = $this->storage->setUserId(self::SAMPLE_VALUE);

        self::assertEquals($this->storage, $result);
    }

    /**
     * Test setWidgetSettings method
     *
     * @return void
     */
    public function testSetWidgetSettings()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|WidgetConfig $widgetSettings */
        $widgetSettings = $this->basicMock(WidgetConfig::class);
        $this->jsonHelper->expects($this->once())
            ->method('jsonEncode')
            ->willReturn(null);
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->willReturn(self::SAMPLE_VALUE);
        $this->jsonHelper->expects($this->once())
            ->method('jsonDecode')
            ->willReturn(null);
        $this->widgetConfig->expects($this->once())
            ->method('setData')
            ->willReturn(null);
        $this->widgetConfig->expects($this->once())
            ->method('isNativePlatformReviewSystem')
            ->willReturn(true);
        $widgetSettings->expects($this->exactly(2))
            ->method('isNativePlatformReviewSystem')
            ->willReturn(false);
        $this->moduleStatus->expects($this->once())
            ->method('setIsEnabled')
            ->willReturn(null);
        $this->cacheManager->expects($this->once())
            ->method('getAvailableTypes')
            ->willReturn([1]);
        $this->storageWriter->expects($this->once())
            ->method('save')
            ->willReturn(null);
        $this->cacheManager->expects($this->once())
            ->method('clean')
            ->willReturn(null);
        $result = $this->storage->setWidgetSettings($widgetSettings);

        self::assertEquals(true, $result);
    }

    /**
     * Test getWidgetSettings method
     *
     * @return void
     */
    public function testGetWidgetSettings()
    {
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->willReturn(self::SAMPLE_VALUE);
        $this->jsonHelper->expects($this->once())
            ->method('jsonDecode')
            ->willReturn(null);
        $this->widgetConfig->expects($this->once())
            ->method('setData')
            ->willReturn(null);
        $result = $this->storage->getWidgetSettings();

        self::assertEquals($this->widgetConfig, $result);
    }

    /**
     * Test setWidgetSnippets method
     *
     * @return void
     */
    public function testSetWidgetSnippets()
    {
        $this->widgetSnippet->expects($this->once())
            ->method('getData')
            ->willReturn(null);
        $this->jsonHelper->expects($this->once())
            ->method('jsonEncode')
            ->willReturn(null);
        $this->storageWriter->expects($this->once())
            ->method('save')
            ->willReturn(null);
        $this->cacheManager->expects($this->once())
            ->method('clean')
            ->willReturn(null);
        $result = $this->storage->setWidgetSnippets($this->widgetSnippet);

        self::assertEquals(true, $result);
    }

    /**
     * Test getWidgetSnippets method
     *
     * @return void
     */
    public function testGetWidgetSnippets()
    {
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->willReturn(self::SAMPLE_VALUE);
        $this->jsonHelper->expects($this->once())
            ->method('jsonDecode')
            ->willReturn(null);
        $this->widgetSnippet->expects($this->once())
            ->method('setData')
            ->willReturn(null);
        $result = $this->storage->getWidgetSnippets();

        self::assertEquals($this->widgetSnippet, $result);
    }

    /**
     * Test getPluginId method
     *
     * @return void
     */
    public function testGetPluginId()
    {
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->willReturn(self::SAMPLE_VALUE);
        $result = $this->storage->getPluginId();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setPluginId method
     *
     * @return void
     */
    public function testSetPluginId()
    {
        $this->storageWriter->expects($this->once())
            ->method('save')
            ->willReturn(null);
        $this->cacheManager->expects($this->once())
            ->method('clean')
            ->willReturn(null);
        $result = $this->storage->setPluginId(self::SAMPLE_VALUE);

        self::assertEquals($this->storage, $result);
    }
}