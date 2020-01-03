<?php

namespace Feefo\Reviews\Block\Adminhtml;

use Feefo\Reviews\Api\Feefo\Data\JsonableInterface;
use Feefo\Reviews\Api\Feefo\Data\StoreUrlGroupDataInterface;
use Feefo\Reviews\Api\Feefo\Data\WidgetConfigInterface;
use Feefo\Reviews\Api\Feefo\Data\WidgetSnippetInterface;
use Feefo\Reviews\Api\Feefo\Data\WidgetWrapperInterface;
use Feefo\Reviews\Api\Feefo\Helper\StoreDetailsInterface;
use Feefo\Reviews\Api\Feefo\RegistrationInterface;
use Feefo\Reviews\Api\Feefo\StorageInterface;
use Feefo\Reviews\Api\Feefo\StoreUrlGroupInterface;
use Feefo\Reviews\Api\Feefo\WidgetInterface;
use Feefo\Reviews\Model\Feefo\Data\ConfigurationRequestFactory;
use Feefo\Reviews\Model\Feefo\Data\RegistrationRequestFactory;
use Magento\Backend\Block\Template;
use Magento\Framework\Url\EncoderInterface as UrlEncoder;

/**
 * Class Options
 */
class Options extends Template
{
    /**
     * Default template
     * @var string
     */
    protected $_template = 'options.phtml';

    /**
     * @var RegistrationInterface
     */
    protected $registrationAPI;

    /**
     * @var RegistrationRequestFactory
     */
    protected $storeDataFactory;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var ConfigurationRequestFactory
     */
    protected $configRequestDataFactory;

    /**
     * @var WidgetInterface
     */
    protected $widgetAPI;

    /**
     * @var StoreDetailsInterface
     */
    protected $storeDetails;

    /**
     * @var StoreUrlGroupInterface
     */
    protected $storeUrlGroup;

    /**
     * @var UrlEncoder
     */
    protected $urlEncoder;

    /**
     * Options constructor.
     * @param Template\Context $context
     * @param RegistrationInterface $registrationAPI
     * @param WidgetInterface $widgetAPI
     * @param RegistrationRequestFactory $registrationRequestFactory
     * @param StorageInterface $storage
     * @param ConfigurationRequestFactory $configRequestDataFactory
     * @param StoreDetailsInterface $storeDetails
     * @param StoreUrlGroupInterface $storeUrlGroup
     * @param UrlEncoder $urlEncoder
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        RegistrationInterface $registrationAPI,
        WidgetInterface $widgetAPI,
        RegistrationRequestFactory $registrationRequestFactory,
        StorageInterface $storage,
        ConfigurationRequestFactory $configRequestDataFactory,
        StoreDetailsInterface $storeDetails,
        StoreUrlGroupInterface $storeUrlGroup,
        UrlEncoder $urlEncoder,
        array $data
    ) {
        parent::__construct($context, $data);

        $this->registrationAPI = $registrationAPI;
        $this->storeDataFactory = $registrationRequestFactory;
        $this->storage = $storage;
        $this->configRequestDataFactory = $configRequestDataFactory;
        $this->widgetAPI = $widgetAPI;
        $this->storeDetails = $storeDetails;
        $this->storeUrlGroup = $storeUrlGroup;
        $this->urlEncoder = $urlEncoder;
    }

    /**
     * Retrieve registration or configuration URL
     *
     * @return false|string
     */
    public function getOptionsPageLink()
    {
        $this->_logger->debug($this->storeDetails->getMerchantEmail());
        $currentWebsiteUrl = $this->getCurrentWebsiteUrl();

        try {
            if ($this->storage->getPluginId()) {
                $this->updateWidgetSettings();
                $payload = $this->configRequestDataFactory->create();
            } else {
                $payload = $this->storeDataFactory->create($currentWebsiteUrl);
            }

            if ($payload instanceof JsonableInterface) {
                /** @var JsonableInterface $payload */
                $serviceData = $this->registrationAPI->register($payload);

                $websiteUrlGroup = $this->storeUrlGroup->getGroupByUrl($currentWebsiteUrl);
                $storeIds = [];
                if ($websiteUrlGroup) {
                    $storeIds = $websiteUrlGroup->getStoreIds();
                }

                if ($serviceData->getPluginId()) {
                    $this->storage->setPluginId($serviceData->getPluginId(), $storeIds);
                } elseif (!$this->storage->getPluginId() && $pluginId = $serviceData->getIdForRegisteredPlugin()) {
                    $this->storage->setPluginId($pluginId, $storeIds);
                }

                return $serviceData->getPageUrl();
            }
        } catch (\Exception $ex) {
            $this->_logger->debug($ex->getMessage());
        }

        return false;
    }

    /**
     * Retrieve list of the available websites
     *
     * @return array
     */
    public function getAvailableWebsites()
    {
        /** @var StoreUrlGroupDataInterface[] $urlGroup */
        $urlGroups = $this->storeUrlGroup->getGroups();

        $availableWebsites = [];

        /** @var StoreUrlGroupDataInterface $group */
        foreach ($urlGroups as $group) {
            $availableWebsites[] = $group->getUrl();
        }

        return $availableWebsites;
    }

    /**
     * Retrieve the current website id
     *
     * @return string
     */
    public function getCurrentWebsiteUrl()
    {
        return $this->storage->getWebsiteUrl();
    }

    /**
     * Check if a website for widgets is configured
     *
     * @return boolean
     */
    public function isWebsiteReady()
    {
        $currentWebsiteUrl = $this->getCurrentWebsiteUrl();
        if ($currentWebsiteUrl) {
            return true;
        }
        return false;
    }

    /**
     * Update widget settings
     *
     * @return void
     */
    protected function updateWidgetSettings()
    {
        /** @var WidgetWrapperInterface $widgetWrapper */
        $widgetWrapper = $this->widgetAPI->getSettings();

        if (!$widgetWrapper->hasError()) {
            $currentWebsiteUrl = $this->getCurrentWebsiteUrl();
            $websiteUrlGroup = $this->storeUrlGroup->getGroupByUrl($currentWebsiteUrl);
            $storeIds = [];
            if ($websiteUrlGroup) {
                $storeIds = $websiteUrlGroup->getStoreIds();
            }

            /** @var WidgetConfigInterface $receivedWidgetSettings */
            $receivedWidgetSettings = $widgetWrapper->getWidgetSettings();
            /** @var WidgetSnippetInterface $receivedWidgetSnippets */
            $receivedWidgetSnippets = $widgetWrapper->getSnippetsPreview();

            /** @var WidgetConfigInterface $persistedWidgetSettings */
            $persistedWidgetSettings = $this->storage->getWidgetSettings();
            /** @var WidgetSnippetInterface $persistedWidgetSnippets */
            $persistedWidgetSnippets = $this->storage->getWidgetSnippets();

            if (
                $receivedWidgetSettings instanceof JsonableInterface &&
                $receivedWidgetSnippets instanceof JsonableInterface &&
                $persistedWidgetSettings instanceof JsonableInterface &&
                $persistedWidgetSnippets instanceof JsonableInterface
            ) {
                if ($persistedWidgetSettings->hasChanges($receivedWidgetSettings)) {
                    $this->storage->setWidgetSettings($receivedWidgetSettings, $storeIds);
                }

                if ($persistedWidgetSnippets->hasChanges($receivedWidgetSnippets)) {
                    $this->storage->setWidgetSnippets($receivedWidgetSnippets, $storeIds);
                }
            }

        } else {
            $this->_logger->error($widgetWrapper->getError());
        }
    }

    /**
     * Check if the current request is secure
     *
     * @return bool
     */
    public function isSecureRequest()
    {
        return $this->getRequest()->isSecure();
    }

    /**
     * Get Encoded Website Url
     *
     * @param string $url
     *
     * @return mixed
     */
    public function getEncodedUrl($url)
    {
        return $this->urlEncoder->encode($url);
    }
}
