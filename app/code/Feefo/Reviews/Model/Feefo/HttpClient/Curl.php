<?php

namespace Feefo\Reviews\Model\Feefo\HttpClient;

use Feefo\Reviews\Api\Feefo\HttpClientInterface;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\HTTP\Adapter\Curl as CurlHttpAdapter;

/**
 * Class Curl
 */
class Curl implements HttpClientInterface
{
    /**
     * @var CurlFactory
     */
    protected $curlFactory;

    /**
     * Curl constructor.
     * @param CurlFactory $curlFactory
     */
    public function __construct(
        CurlFactory $curlFactory
    ) {
        $this->curlFactory = $curlFactory;
    }

    /**
     * Make request of any type
     *
     * @param $method
     * @param string $url
     * @param string $body
     * @param string $http_ver
     * @param string[] $headers
     *
     * @return string
     */
    public function makeRequest($method, $url, $body = '', $http_ver = '1.1', $headers = [])
    {
        /** @var CurlHttpAdapter $curl */
        $curl = $this->curlFactory->create();
        $curl->addOption(CURLOPT_FOLLOWLOCATION, true);
        $curl->write($method, $url, $http_ver, $headers, $body);
        $data = $curl->read();
        if ($data === false) {
            return false;
        }

        $data = preg_split('/^\r?$/m', $data, 2);
        $data = trim($data[1]);

        $curl->close();

        return $data;
    }

    /**
     * Make GET request
     *
     * @param string $url
     * @param string $body
     * @param string $http_ver
     * @param string[] $headers
     *
     * @return string
     */
    public function get($url, $body = '', $http_ver = '1.1', $headers = [])
    {
        return $this->makeRequest(\Zend_Http_Client::GET, $url, $body, $http_ver, $headers);
    }

    /**
     * Make POST request
     *
     * @param string $url
     * @param string $body
     * @param string $http_ver
     * @param array $headers
     *
     * @return string
     */
    public function post($url, $body = '', $http_ver = '1.1', $headers = [])
    {
        return $this->makeRequest(\Zend_Http_Client::POST, $url, $body, $http_ver, $headers);
    }

    /**
     * Make PUT request
     *
     * @param string $url
     * @param string $body
     * @param string $http_ver
     * @param array $headers
     *
     * @return string
     */
    public function put($url, $body = '', $http_ver = '1.1', $headers = [])
    {
        return $this->makeRequest(\Zend_Http_Client::PUT, $url, $body, $http_ver, $headers);
    }

    /**
     * Make DELETE request
     *
     * @param string $url
     * @param string $body
     * @param array $headers
     * @param string $http_ver
     *
     * @return string|bool
     */
    public function delete($url, $body = '', $headers = [], $http_ver = '1.1')
    {
        /** @var CurlHttpAdapter $curl */
        $curl = $this->curlFactory->create();
        $curl->addOption(CURLOPT_POSTFIELDS, $body);
        $curl->addOption(CURLOPT_CUSTOMREQUEST, \Zend_Http_Client::DELETE);
        $curl->write(\Zend_Http_Client::DELETE, $url, $http_ver, $headers, $body);
        $data = $curl->read();

        if ($data === false) {
            return false;
        }

        $curl->close();

        return $data;
    }
}