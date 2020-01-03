<?php

namespace Feefo\Reviews\Test\Unit\Model\Feefo;

use Feefo\Reviews\Api\Feefo\HttpClientInterface;
use Feefo\Reviews\Model\Feefo\Data\JsonableDataObject;
use Feefo\Reviews\Model\Feefo\Data\ServiceFactory;
use Feefo\Reviews\Model\Feefo\Registration;
use Feefo\Reviews\Test\Unit\AbstractTestCase;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

/**
 * Class RegistrationTest
 */
class RegistrationTest extends AbstractTestCase
{
    /**
     * Sample Value
     */
    const SAMPLE_VALUE = 'value';

    /**
     * HttpClientInterface mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|HttpClientInterface
     */
    protected $httpClient;

    /**
     * ServiceFactory mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|ServiceFactory
     */
    protected $serviceFactory;

    /**
     * LoggerInterface mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|LoggerInterface
     */
    protected $logger;

    /**
     * ScopeConfigInterface mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Registration
     *
     * @var Registration
     */
    protected $registration;

    /**
     * SetUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->scopeConfig = $this->basicMock(ScopeConfigInterface::class);
        $this->httpClient = $this->basicMock(HttpClientInterface::class);
        $this->serviceFactory = $this->basicMock(ServiceFactory::class);
        $this->logger = $this->basicMock(LoggerInterface::class);
        $this->registration = $this->objectManager->getObject(Registration::class, [
            'scopeConfig' => $this->scopeConfig,
            'httpClient' => $this->httpClient,
            'serviceFactory' => $this->serviceFactory,
            'logger' => $this->logger
        ]);
    }

    /**
     * Test register method
     *
     * @return void
     */
    public function testRegister()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|JsonableDataObject $jsonableDataObject */
        $jsonableDataObject = $this->basicMock(JsonableDataObject::class);
        $this->scopeConfig->expects($this->exactly(2))
            ->method('getValue')
            ->willReturn(self::SAMPLE_VALUE);
        $jsonableDataObject->expects($this->exactly(2))
            ->method('asJson')
            ->willReturn(null);
        $this->logger->expects($this->exactly(2))
            ->method('debug')
            ->willReturn(null);
        $this->httpClient->expects($this->once())
            ->method('post')
            ->willReturn(null);
        $this->serviceFactory->expects($this->once())
            ->method('create')
            ->willReturn($jsonableDataObject);
        $result = $this->registration->register($jsonableDataObject);

        self::assertEquals($jsonableDataObject, $result);
    }
}