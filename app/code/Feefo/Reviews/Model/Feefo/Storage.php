<?php

namespace Feefo\Reviews\Model\Feefo;

use Feefo\Reviews\Api\Feefo\Data\WidgetConfigInterface;
use Feefo\Reviews\Api\Feefo\Data\WidgetSnippetInterface;
use Feefo\Reviews\Api\Feefo\StorageInterface;
use Magento\Framework\App\Cache\Manager as CacheManager;
use Magento\Framework\App\Cache\Type\Block as CacheTypeBlock;
use Magento\Framework\App\Cache\Type\Config as CacheTypeConfig;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Module\Status as ModuleStatus;
use Magento\PageCache\Model\Cache\Type as CacheType;
use Magento\Store\Model\ScopeInterface as StoreScopeInterface;

/**
 * Class Storage
 */
class Storage implements StorageInterface
{
    /**
     * Review Module Name
     */
    const REVIEW_MODULE_NAME = 'Magento_Review';

    /**
     * Core Config Data Table Name
     */
    const CORE_CONFIG_TABLE_NAME = 'core_config_data';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var WriterInterface
     */
    protected $storageWriter;

    /**
     * @var CacheManager
     */
    protected $cacheManager;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var ModuleStatus
     */
    protected $moduleStatus;

    /**
     * @var WidgetConfigInterface
     */
    protected $widgetConfig;

    /**
     * @var ConfigInterface
     */
    protected $configResource;

    /**
     * @var WidgetSnippetInterface
     */
    protected $widgetSnippet;

    /**
     * Config paths to be removed
     *
     * @var array
     */
    protected $configPaths = [
        'feefo/general/website_url',
        'feefo/service/plugin_id',
        'feefo/widget/override_product_listing_template',
        'feefo/widget/settings',
        'feefo/widget/snippets',
        'feefo/general/sore_ids',
    ];

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param WriterInterface $storageWriter
     * @param JsonHelper $jsonHelper
     * @param CacheManager $cacheManager
     * @param ModuleStatus $moduleStatus
     * @param WidgetConfigInterface $widgetConfig
     * @param WidgetSnippetInterface $widgetSnippet
     * @param ConfigInterface $configResource
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        WriterInterface $storageWriter,
        JsonHelper $jsonHelper,
        CacheManager $cacheManager,
        ModuleStatus $moduleStatus,
        WidgetConfigInterface $widgetConfig,
        WidgetSnippetInterface $widgetSnippet,
        ConfigInterface $configResource
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storageWriter = $storageWriter;
        $this->jsonHelper = $jsonHelper;
        $this->cacheManager = $cacheManager;
        $this->moduleStatus = $moduleStatus;
        $this->widgetConfig = $widgetConfig;
        $this->widgetSnippet = $widgetSnippet;
        $this->configResource = $configResource;
    }

    /**
     * @inheritdoc
     */
    public function getAccessKey()
    {
        return $this->scopeConfig->getValue(static::XPATH_ACCESS_KEY);
    }

    /**
     * @inheritdoc
     */
    public function setAccessKey($accessKey)
    {
        $this->storageWriter->save(static::XPATH_ACCESS_KEY, $accessKey);
        $this->flushCache();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUserId()
    {
        return $this->scopeConfig->getValue(static::XPATH_USER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setUserId($userId)
    {
        $this->storageWriter->save(static::XPATH_USER_ID, $userId);
        $this->flushCache();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setWidgetSettings($widgetSettings, $storeIds = [])
    {
        $types = [
            CacheTypeConfig::TYPE_IDENTIFIER,
            CacheTypeBlock::TYPE_IDENTIFIER,
            CacheType::TYPE_IDENTIFIER
        ];
        $settings = $this->jsonHelper->jsonEncode($widgetSettings->getData());
        $currentSettings = $this->getWidgetSettings();
        if ($currentSettings->isNativePlatformReviewSystem() != $widgetSettings->isNativePlatformReviewSystem()) {
            $enableNativeReview = !$widgetSettings->isNativePlatformReviewSystem();
            $this->moduleStatus->setIsEnabled($enableNativeReview, [static::REVIEW_MODULE_NAME]); // $enableNativeReview should be type of bool
            $types = $this->cacheManager->getAvailableTypes();
        }
        if ($currentSettings->getProductListingStarsPlacement() != $widgetSettings->getProductListingStarsPlacement()) {
            $override = $widgetSettings->getProductListingStarsPlacement() === WidgetConfigInterface::PLACEMENT_AUTO;
            $this->setOverrideProductListingTemplate($override, $storeIds);
        }

        if (count($storeIds)) {
            $this->setByStoreIds($settings, $storeIds, static::XPATH_WIDGET_SETTINGS);
        }

        $this->storageWriter->save(static::XPATH_WIDGET_SETTINGS, $settings);
        $this->flushCache($types);

        return true;
    }


    /**
     * @inheritdoc
     */
    public function getWidgetSettings($storeId = null)
    {
        if ($storeId) {
            $settings = $this->getByStoreId($storeId, static::XPATH_WIDGET_SETTINGS);
        } else {
            $settings = $this->scopeConfig->getValue(static::XPATH_WIDGET_SETTINGS);
        }

        if ($settings) {
            $settings = $this->jsonHelper->jsonDecode($settings);
            $this->widgetConfig->setData($settings);
        }

        return $this->widgetConfig;
    }

    /**
     * @inheritdoc
     */
    public function setWidgetSnippets($widgetSettings, $storeIds = [])
    {
        $types = [
            CacheTypeConfig::TYPE_IDENTIFIER,
            CacheTypeBlock::TYPE_IDENTIFIER,
            CacheType::TYPE_IDENTIFIER
        ];

        $settings = $this->jsonHelper->jsonEncode($widgetSettings->getData());

        if (count($storeIds)) {
            $this->setByStoreIds($settings, $storeIds, static::XPATH_WIDGET_SNIPPETS);
        }

        $this->storageWriter->save(static::XPATH_WIDGET_SNIPPETS, $settings);
        $this->flushCache($types);

        return true;
    }


    /**
     * @inheritdoc
     */
    public function getWidgetSnippets($storeId = null)
    {
        if ($storeId) {
            $settings = $this->getByStoreId($storeId, static::XPATH_WIDGET_SNIPPETS);
        } else {
            $settings = $this->scopeConfig->getValue(static::XPATH_WIDGET_SNIPPETS);
        }

        if ($settings) {
            $settings = $this->jsonHelper->jsonDecode($settings);
            $this->widgetSnippet->setData($settings);
        }

        return $this->widgetSnippet;
    }

    /**
     * @inheritdoc
     */
    public function getPluginId($storeId = null)
    {
        if ($storeId) {
            return $this->getByStoreId($storeId, static::XPATH_PLUGIN_ID);
        }

        return $this->scopeConfig->getValue(static::XPATH_PLUGIN_ID);
    }

    /**
     * @inheritdoc
     */
    public function setPluginId($pluginId, $storeIds = [])
    {
        if (count($storeIds)) {
            $this->setByStoreIds($pluginId, $storeIds, static::XPATH_PLUGIN_ID);
        }

        // Always save default config value for currently selected website
        $this->storageWriter->save(static::XPATH_PLUGIN_ID, $pluginId);
        $this->flushCache();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getWebsiteUrl($storeId = null)
    {
        if ($storeId) {
            return $this->getByStoreId($storeId, static::XPATH_WEBSITE_URL);
        }

        return $this->scopeConfig->getValue(static::XPATH_WEBSITE_URL);
    }

    /**
     * @inheritdoc
     */
    public function setWebsiteUrl($url, $storeIds = [])
    {
        if (count($storeIds)) {
            $this->setByStoreIds($url, $storeIds, static::XPATH_WEBSITE_URL);
        }

        $this->storageWriter->save(static::XPATH_WEBSITE_URL, $url);
        $this->flushCache();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStoreIds()
    {
        return explode(',', $this->scopeConfig->getValue(static::XPATH_STORE_IDS));
    }

    /**
     * @inheritdoc
     */
    public function setStoreIds($storeIds)
    {
        $this->storageWriter->save(static::XPATH_STORE_IDS, implode(',', $storeIds));

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOverrideProductListingTemplate($storeId = null)
    {
        if ($storeId) {
            return $this->getByStoreId($storeId, static::XPATH_OVERRIDE_TEMPLATE);
        }

        return $this->scopeConfig->getValue(static::XPATH_OVERRIDE_TEMPLATE);
    }

    /**
     * @inheritdoc
     */
    public function setOverrideProductListingTemplate($override, $storeIds = [])
    {
        if (count($storeIds)) {
            $this->setByStoreIds($override, $storeIds, static::XPATH_OVERRIDE_TEMPLATE);
        }

        $this->storageWriter->save(static::XPATH_OVERRIDE_TEMPLATE, $override);
        $this->flushCache();

        return $this;
    }

    /**
     * @return $this
     */
    public function unsetWebsite()
    {
        $this->storageWriter->delete(static::XPATH_WEBSITE_URL);
        $this->storageWriter->delete(static::XPATH_STORE_IDS);
        $this->storageWriter->delete(static::XPATH_PLUGIN_ID);
        $this->flushCache();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function clearLocalPluginData()
    {
        foreach ($this->configPaths as $path) {
            $this->storageWriter->delete($path);
        }

        /** @var AdapterInterface $connection */
        $connection = $this->configResource->getConnection();

        /** @var Select $selectConfig */
        $selectConfig = $connection->select();
        $selectConfig->from(self::CORE_CONFIG_TABLE_NAME);
        $selectConfig->where('path IN (?)', $this->configPaths);

        /** @var string $deleteConfigQuery */
        $deleteConfigQuery = $connection->deleteFromSelect(
            $selectConfig,
            self::CORE_CONFIG_TABLE_NAME
        );
        $connection->query($deleteConfigQuery);

        $this->flushCache();
    }

    /**
     * @param int $storeId
     * @param string $path
     *
     * @return string
     */
    protected function getByStoreId($storeId, $path)
    {
        return $this->scopeConfig->getValue($path, StoreScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param string $value
     * @param array $storeIds
     * @param string $path
     */
    protected function setByStoreIds($value, $storeIds, $path)
    {
        foreach ($storeIds as $storeId) {
            $this->storageWriter->save($path, $value, StoreScopeInterface::SCOPE_STORES, $storeId);
        }
    }

    /**
     * Flush cache of certain types
     * @param string[] $cacheTypes
     * @return void
     */
    protected function flushCache(array $cacheTypes = [CacheTypeConfig::TYPE_IDENTIFIER])
    {
        $this->cacheManager->clean($cacheTypes);
    }
}