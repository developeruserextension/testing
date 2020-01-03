<?php

namespace Feefo\Reviews\Test\Unit\Model\Feefo\Data;

use Feefo\Reviews\Api\Feefo\Data\RegistrationRequestInterface;
use Feefo\Reviews\Api\Feefo\StorageInterface;
use Feefo\Reviews\Model\Feefo\Data\RegistrationRequest;
use Feefo\Reviews\Model\Feefo\Data\RegistrationRequestFactory;
use Feefo\Reviews\Test\Unit\AbstractTestCase;
use Magento\Backend\Model\Url;
use Magento\Framework\App\ObjectManager as AppObjectManages;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class RegistrationRequestFactoryTest
 */
class RegistrationRequestFactoryTest extends AbstractTestCase
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
     * RegistrationRequestInterface class name
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
     * RegistrationRequestFactory
     *
     * @var RegistrationRequestFactory
     */
    protected $registrationRequestFactory;

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
        $this->eventManager = $this->basicMock(EventManagerInterface::class);
        $this->instance = RegistrationRequestInterface::class;

        $this->registrationRequestFactory = $this->objectManager->getObject(RegistrationRequestFactory::class, [
            'objectManager' => $this->objectManagerMock,
            'storage' => $this->storage,
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
        $registrationRequest = $this->basicMock(RegistrationRequest::class);
        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->willReturn($registrationRequest);
        $this->storage->expects($this->once())
            ->method('getAccessKey')
            ->willReturn(true);
        $registrationRequest->expects($this->once())
            ->method('setAccessToken')
            ->willReturn(null);
        $this->eventManager->expects($this->once())
            ->method('dispatch')
            ->willReturn(null);
        $result = $this->registrationRequestFactory->create(1);

        self::assertEquals($registrationRequest, $result);
    }
}