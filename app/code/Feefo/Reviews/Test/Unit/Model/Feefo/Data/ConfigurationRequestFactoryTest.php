<?php

namespace Feefo\Reviews\Test\Unit\Model\Feefo\Data;

use Feefo\Reviews\Api\Feefo\Data\ConfigurationRequestInterface;
use Feefo\Reviews\Api\Feefo\StorageInterface;
use Feefo\Reviews\Model\Feefo\Data\ConfigurationRequestFactory;
use Feefo\Reviews\Test\Unit\AbstractTestCase;
use Magento\Backend\Model\Url;
use Magento\Framework\App\ObjectManager as AppObjectManages;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class ConfigurationRequestFactoryTest
 */
class ConfigurationRequestFactoryTest extends AbstractTestCase
{
    /**
     * ObjectManagerInterface mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|ObjectManagerInterface
     */
    protected $objectManagerMock;

    /**
     * Backend Url Model mock
     *
     * @var  \PHPUnit_Framework_MockObject_MockObject|Url
     */
    protected $urlBuilder;

    /**
     * StorageInterface mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|StorageInterface
     */
    protected $storage;

    /**
     * ConfigurationRequestInterface class name
     *
     * @var string
     */
    protected $instance;

    /**
     * EventManagerInterface mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|EventManagerInterface
     */
    protected $eventManager;

    /**
     * ConfigurationRequestFactory
     *
     * @var ConfigurationRequestFactory
     */
    protected $configurationRequestFactory;

    /**
     * SetUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->objectManagerMock = $this->basicMock(ObjectManagerInterface::class);
        $this->storage = $this->basicMock(StorageInterface::class);
        $this->urlBuilder = $this->basicMock(Url::class);
        $this->eventManager = $this->basicMock(EventManagerInterface::class);
        $this->instance = ConfigurationRequestInterface::class;
        $this->configurationRequestFactory = $this->objectManager->getObject(ConfigurationRequestFactory::class, [
            'objectManager' => $this->objectManagerMock,
            'storage' => $this->storage,
            'urlBuilder' => $this->urlBuilder,
            'eventManager' => $this->eventManager,
            'instance' => $this->instance,
        ]);
    }

    /**
     * Test create method
     *
     * @return void
     */
    public function testCreate()
    {
        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->willReturn(true);
        $this->eventManager->expects($this->once())
            ->method('dispatch')
            ->willReturn(null);

        $result = $this->configurationRequestFactory->create();

        self::assertEquals(true, $result);
    }

    /**
     * Test getRedirectUrl method
     *
     * @return void
     */
    public function testGetRedirectUrl()
    {
        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->willReturn(true);

        $result = $this->configurationRequestFactory->getRedirectUrl();
        self::assertEquals(true, $result);
    }
}