<?php

namespace Feefo\Reviews\Api\Feefo;

/**
 * Interface HttpClientInterface
 */
interface HttpClientInterface
{
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
    public function makeRequest($method, $url, $body = '', $http_ver = '1.1', $headers = []);

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
    public function get($url, $body = '', $http_ver = '1.1', $headers = []);

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
    public function post($url, $body = '', $http_ver = '1.1', $headers = []);

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
    public function put($url, $body = '', $http_ver = '1.1', $headers = []);

    /**
     * Make DELETE request
     *
     * @param string $url
     * @param string $body
     * @param array $headers
     * @param string $http_ver
     *
     * @return string
     */
    public function delete($url, $body = '', $headers = [], $http_ver = '1.1');
}