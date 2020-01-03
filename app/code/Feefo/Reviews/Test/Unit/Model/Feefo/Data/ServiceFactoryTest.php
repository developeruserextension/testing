<?php

namespace Feefo\Reviews\Test\Unit\Model\Feefo\Data;

use Feefo\Reviews\Api\Feefo\Data\ServiceInterface;
use Feefo\Reviews\Model\Feefo\Data\JsonableDataObject;
use Feefo\Reviews\Model\Feefo\Data\ServiceFactory;
use Feefo\Reviews\Test\Unit\AbstractTestCase;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ObjectManager as AppObjectManages;
use Magento\Framework\ObjectManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ServiceFactoryTest
 */
class ServiceFactoryTest extends AbstractTestCase
{
    /**
     * ObjectManagerInterface mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|ObjectManagerInterface
     */
    protected $objectManagerMock;

    /**
     * LoggerInterface mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|LoggerInterface
     */
    protected $logger;

    /**
     * ServiceFactory class name
     *
     * @var string
     */
    protected $instance;

    /**
     * ServiceFactory
     *
     * @var ServiceFactory
     */
    protected $serviceFactory;

    /**
     * SetUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->objectManagerMock = $this->basicMock(ObjectManagerInterface::class);
        $this->logger = $this->basicMock(LoggerInterface::class);
        $this->instance = ServiceInterface::class;
        $this->serviceFactory = $this->objectManager->getObject(ServiceFactory::class, [
            'objectManager' => $this->objectManagerMock,
            'logger' => $this->logger,
            'instance' => $this->instance
        ]);
    }

    /**
     * Test create method
     *
     * @return void
     */
    public function testCreate()
    {
        $serviceDataResult = $this->objectManager->getObject(JsonableDataObject::class);
        $serviceData = $this->basicMock($this->instance);
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->willReturn($serviceDataResult);
        $serviceData->expects($this->any())
            ->method('setJSON')
            ->willReturn($serviceData);
        $result = $this->serviceFactory->create('1');

        self::assertEquals($serviceDataResult, $result);
    }
}