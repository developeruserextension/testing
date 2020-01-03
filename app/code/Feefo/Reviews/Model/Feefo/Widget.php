<?php

namespace Feefo\Reviews\Model\Feefo;

use Feefo\Reviews\Api\Feefo\Data\JsonableInterface;
use Feefo\Reviews\Api\Feefo\Data\WidgetWrapperInterface;
use Feefo\Reviews\Api\Feefo\Helper\HmacInterface;
use Feefo\Reviews\Api\Feefo\HttpClientInterface;
use Feefo\Reviews\Api\Feefo\StorageInterface;
use Feefo\Reviews\Api\Feefo\StorageInterface as FeefoStorageInterface;
use Feefo\Reviews\Api\Feefo\WidgetInterface;
use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Widget
 *
 * Service Contract that describes widgets API of Feefo service
 */
class Widget extends AbstractEntryPoint implements WidgetInterface
{
    const API_GET_SETTINGS = 'get_widget_configs';

    const API_SET_SETTINGS = 'set_widget_configs';

    const PLACEHOLDER_PLUGIN_ID = ':{pluginId}';

    const PLACEHOLDER_HMAC = ':{hmac}';
    
    /**
     * @var HttpClientInterface
     */
    protected $httpClient;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var  HmacInterface
     */
    protected $hmac;

    /**
     * @var LoggerInterface
     *  */
    protected $logger;

    /**
     * @var ObjectFactory
     */
    protected $objectFactory;

    /**
     * Widget constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param FeefoStorageInterface $storage
     * @param HmacInterface $hmac
     * @param HttpClientInterface $httpClient
     * @param ObjectFactory $objectFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        FeefoStorageInterface $storage,
        HmacInterface $hmac,
        HttpClientInterface $httpClient,
        ObjectFactory $objectFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($scopeConfig);

        $this->httpClient = $httpClient;
        $this->storage = $storage;
        $this->logger = $logger;
        $this->hmac = $hmac;
        $this->objectFactory = $objectFactory;
    }

    /**
     * Retrieve the settings of the widgets
     *
     * @return WidgetWrapperInterface
     */
    public function getSettings()
    {
        $pluginId = $this->storage->getPluginId();
        $hmac = $this->hmac->get();

        $this->logger->debug($hmac);

        $params = [
            static::PLACEHOLDER_PLUGIN_ID => $pluginId,
            static::PLACEHOLDER_HMAC => $hmac
        ];

        $apiUrl = $this->getApiUrlWithParams(static::API_GET_SETTINGS, $params);
        $jsonResponse = $this->httpClient->get($apiUrl);

        $this->logger->debug($jsonResponse);

        /** @var $widgetSettingsWrapper WidgetWrapperInterface */
        $widgetSettingsWrapper = $this->objectFactory->create(WidgetWrapperInterface::class, []);
        if ($widgetSettingsWrapper instanceof JsonableInterface) {
            /** @var $widgetSettingsWrapper JsonableInterface */
            $widgetSettingsWrapper->setJSON($jsonResponse);
        }

        return $widgetSettingsWrapper;
    }
}