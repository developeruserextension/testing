<?php

namespace Feefo\Reviews\Model\Feefo;

use Feefo\Reviews\Api\Feefo\Data\JsonableInterface;
use Feefo\Reviews\Api\Feefo\HttpClientInterface;
use Feefo\Reviews\Api\Feefo\UninstallPluginRequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Psr\Log\LoggerInterface;

/**
 * Class UninstallPlugin
 */
class UninstallPluginRequest extends AbstractEntryPoint implements UninstallPluginRequestInterface
{
    /**
     * Uninstall resource route
     */
    const API_UNINSTALL = 'uninstall';

    /**
     * Http Client
     *
     * @var HttpClientInterface
     */
    protected $httpClient;

    /**
     * Service Factory
     *
     * @var Data\ServiceFactory
     */
    protected $serviceFactory;

    /**
     * Logger
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Json Helper
     *
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * UninstallPlugin constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param HttpClientInterface $httpClient
     * @param JsonHelper $jsonHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        HttpClientInterface $httpClient,
        JsonHelper $jsonHelper,
        LoggerInterface $logger
    ) {
        parent::__construct($scopeConfig);
        $this->httpClient = $httpClient;
        $this->jsonHelper = $jsonHelper;
        $this->logger = $logger;
    }

    /**
     * Request API to uninstall plugin
     *
     * @param JsonableInterface $pluginData
     *
     * @return bool
     */
    public function uninstall(JsonableInterface $pluginData)
    {
        $apiUrl = $this->getApiUrl(static::API_UNINSTALL);
        $jsonStoreData = $pluginData->asJSON();
        $this->logger->debug($jsonStoreData);
        $response = $this->httpClient->delete($apiUrl, $jsonStoreData, ['Content-Type' => 'application/json']);
        $isSuccess = $this->checkIsSuccess($response);

        return $isSuccess;
    }

    /**
     * Check whether the response is success
     *
     * @param $response
     *
     * @return bool
     */
    public function checkIsSuccess($response)
    {
        $data = preg_split('/(\r?\n){2}/', $response, 2);
        $headers = $data[0];
        $headersArray = preg_split('/\r?\n/', $headers);
        $status = explode(' ', $headersArray[0]);

        return $status[1] == '200' ? true : false;
    }
}