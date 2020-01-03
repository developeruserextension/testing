<?php

namespace Feefo\Reviews\Test\Unit\Helper;

use Feefo\Reviews\Api\Feefo\Helper\StoreDetailsInterface;
use Feefo\Reviews\Api\Feefo\StorageInterface;
use Feefo\Reviews\Helper\Hmac;
use Feefo\Reviews\Test\Unit\AbstractTestCase;
use Psr\Log\LoggerInterface;


/**
 * Class HmacTest
 */
class HmacTest extends AbstractTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|StorageInterface
     */
    protected $storage;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|StoreDetailsInterface
     */
    protected $storeDetails;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|LoggerInterface
     */
    protected $logger;

    /**
     * @var Hmac
     */
    protected $hmac;

    /**
     * SetUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->storage = $this->basicMock(StorageInterface::class);
        $this->storeDetails = $this->basicMock(StoreDetailsInterface::class);
        $this->logger = $this->basicMock(LoggerInterface::class);
        $this->hmac = $this->objectManager->getObject(Hmac::class, [
            'storage' => $this->storage,
            'storeDetails' => $this->storeDetails,
            'logger' => $this->logger
        ]);
    }

    /**
     * Test get method
     *
     * @return void
     */
    public function testGet()
    {
        $key = 'eew14ioelkqfimjq2c686sswcvxntjs9';
        $merchantDomain = 'demo.feefo.com/reviews';
        $pluginId = '57a334c824ac65a4a7f9c9de';
        $timeStamp = gmdate('Ymd');
        $message =  "pluginId={$pluginId}&host={$merchantDomain}&timeStamp={$timeStamp}";
        $expectedHmac = hash_hmac('sha256', $message, $key);
        $this->storage->expects($this->once())
            ->method('getPluginId')
            ->willReturn($pluginId);
        $this->storeDetails->expects($this->once())
            ->method('getMerchantDomain')
            ->willReturn($merchantDomain);
        $this->storage->expects($this->once())
            ->method('getAccessKey')
            ->willReturn($key);
        $this->logger->expects($this->once())
            ->method('debug')
            ->willReturn(null);
        $resultHmac = $this->hmac->get();

        self::assertEquals($expectedHmac, $resultHmac);
    }
}
