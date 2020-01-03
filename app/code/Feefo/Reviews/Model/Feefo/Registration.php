<?php

namespace Feefo\Reviews\Model\Feefo;

use Feefo\Reviews\Api\Feefo\Data\JsonableInterface;
use Feefo\Reviews\Api\Feefo\Data\ServiceInterface;
use Feefo\Reviews\Api\Feefo\HttpClientInterface;
use Feefo\Reviews\Api\Feefo\RegistrationInterface;
use Feefo\Reviews\Api\Feefo\Data\ServiceInterface as RegistrationData;
use Feefo\Reviews\Model\Feefo\Data\ServiceFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Registration
 */
class Registration extends AbstractEntryPoint implements RegistrationInterface
{
    const API_REGISTRATION = 'registration';

    /**
     * @var HttpClientInterface
     */
    protected $httpClient;

    /**
     * @var Data\ServiceFactory
     */
    protected $serviceFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Registration constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param HttpClientInterface $httpClient
     * @param Data\ServiceFactory $serviceFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        HttpClientInterface $httpClient,
        ServiceFactory $serviceFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($scopeConfig);
        $this->httpClient = $httpClient;
        $this->serviceFactory = $serviceFactory;
        $this->logger = $logger;
    }

    /**
     * Request API to register a store
     *
     * @param JsonableInterface $storeData
     * @return RegistrationData
     */
    public function register(JsonableInterface $storeData)
    {
        $apiUrl = $this->getApiUrl(static::API_REGISTRATION);
        $jsonStoreData = $storeData->asJSON();
        $this->logger->debug($jsonStoreData);
        $jsonResponse = $this->httpClient->post($apiUrl, $jsonStoreData);
        /** @var ServiceInterface $serviceData */
        $serviceData = $this->serviceFactory->create($jsonResponse);

        if ($serviceData instanceof JsonableInterface) {
            /** @var JsonableInterface $registrationData */
            $this->logger->debug($serviceData->asJSON());
        }

        return $serviceData;
    }
}