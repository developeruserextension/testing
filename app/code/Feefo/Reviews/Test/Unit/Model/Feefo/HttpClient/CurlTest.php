<?php

namespace Feefo\Reviews\Test\Unit\Model\Feefo\HttpClient;

use Feefo\Reviews\Model\Feefo\HttpClient\Curl as HttpClientCurl;
use Feefo\Reviews\Test\Unit\AbstractTestCase;
use Magento\Framework\HTTP\Adapter\Curl;
use Magento\Framework\HTTP\Adapter\CurlFactory;

/**
 * Class CurlTest
 */
class CurlTest extends AbstractTestCase
{
    /**
     * CurlFactory mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|CurlFactory
     */
    protected $curlFactory;

    /**
     * HttpClientCurl
     *
     * @var  HttpClientCurl
     */
    protected $httpClientCurl;

    /**
     * SetUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->curlFactory = $this->mixedMock(CurlFactory::class, ['create']);
        $this->httpClientCurl = $this->objectManager->getObject(HttpClientCurl::class, [
            'curlFactory' => $this->curlFactory
        ]);
    }

    /**
     * Test makeRequest method
     *
     * @return void
     */
    public function testMakeRequest()
    {
        $curlResponse = file_get_contents(__DIR__ . '/_files/curl_response.txt');
        $expectedResult = 'VERIFIED';
        $curlMock = $this->basicMock(Curl::class);
        $this->curlFactory->expects($this->once())
            ->method('create')
            ->willReturn($curlMock);
        $curlMock->expects($this->once())
            ->method('write')
            ->willReturn(null);
        $curlMock->expects($this->once())
            ->method('read')
            ->willReturn($curlResponse);
        $curlMock->expects($this->once())
            ->method('close')
            ->willReturn(null);
        $result = $this->httpClientCurl->makeRequest('1', '1');

        self::assertEquals($expectedResult, $result);
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $curlResponse = 'sample';
        $curlMock = $this->basicMock(Curl::class);
        $this->curlFactory->expects($this->once())
            ->method('create')
            ->willReturn($curlMock);
        $curlMock->expects($this->exactly(2))
            ->method('addOption');
        $curlMock->expects($this->once())
            ->method('write');
        $curlMock->expects($this->once())
            ->method('read')
            ->willReturn($curlResponse);
        $curlMock->expects($this->once())
            ->method('close');
        $result = $this->httpClientCurl->delete('url', 'body');

        self::assertEquals($curlResponse, $result);
    }
}