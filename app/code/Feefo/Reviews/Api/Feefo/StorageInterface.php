<?php

namespace Feefo\Reviews\Api\Feefo;

use Feefo\Reviews\Api\Feefo\Data\WidgetConfigInterface;
use Feefo\Reviews\Api\Feefo\Data\WidgetSnippetInterface;

/**
 * Interface StorageInterface
 *
 * Service Contract that helps to persists configs for/from Feefo
 */
interface StorageInterface
{
    const XPATH_ACCESS_KEY      = 'feefo/service/access_key';

    const XPATH_USER_ID         = 'feefo/service/user_id';

    const XPATH_PLUGIN_ID       = 'feefo/service/plugin_id';

    const XPATH_WEBSITE_URL     = 'feefo/general/website_url';

    const XPATH_STORE_IDS       = 'feefo/general/sore_ids';

    const XPATH_WIDGET_SETTINGS = 'feefo/widget/settings';

    const XPATH_WIDGET_SNIPPETS = 'feefo/widget/snippets';

    const XPATH_OVERRIDE_TEMPLATE = 'feefo/widget/override_product_listing_template';

    /**
     * Get token for getting access to store
     *
     * @return string
     */
    public function getAccessKey();

    /**
     * Set token for getting access to store
     *
     * @param string $accessKey
     * @return $this
     */
    public function setAccessKey($accessKey);

    /**
     * Get Id of User that uses for Feefo integration
     *
     * @return string
     */
    public function getUserId();

    /**
     * Set Id of User that uses for Feefo integration
     *
     * @param string $userId
     * @return $this
     */
    public function setUserId($userId);

    /**
     * Get plugin id from the local storage
     *
     * @param int $storeId
     *
     * @return string
     */
    public function getPluginId($storeId = null);

    /**
     * Save plugin id to the local storage
     *
     * @param string $pluginId
     * @param array $storeIds
     *
     * @return $this
     */
    public function setPluginId($pluginId, $storeIds = []);

    /**
     * Retrieve configured website URL
     *
     * @param int $storeId
     *
     * @return string
     */
    public function getWebsiteUrl($storeId = null);

    /**
     * Save configured website URL
     *
     * @param string $url
     * @param array $storeIds
     *
     * @return $this
     */
    public function setWebsiteUrl($url, $storeIds = []);

    /**
     * Retrieve configured store Ids
     *
     * @return array
     */
    public function getStoreIds();

    /**
     * Save configured store Ids
     *
     * @param array $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds);

    /** Save settings about widgets to local storage
     *
     * @param WidgetConfigInterface $widgetSettings
     * @param array
     *
     * @return $this
     */
    public function setWidgetSettings($widgetSettings, $storeIds = []);

    /** Save settings about widgets to local storage
     *
     * @param int
     *
     * @return WidgetConfigInterface
     */
    public function getWidgetSettings($storeId = null);

    /**
     * Save snippets of the widgets
     *
     * @param WidgetSnippetInterface $widgetSnippets
     * @param array
     *
     * @return $this
     */
    public function setWidgetSnippets($widgetSnippets, $storeIds = []);

    /**
     * Retrieve snippets of the widgets
     *
     * @param int
     *
     * @return WidgetSnippetInterface
     */
    public function getWidgetSnippets($storeId = null);

    /**
     * Retrieve flag of overriding the default template of product listing
     *
     * @param int $storeId
     *
     * @return bool|null
     */
    public function getOverrideProductListingTemplate($storeId = null);

    /**
     * Configure flag of overriding the default template of product listing
     *
     * @param bool $override
     * @param array $storeIds
     *
     * @return $this
     */
    public function setOverrideProductListingTemplate($override, $storeIds = []);

    /**
     * Unset Current Website
     *
     * @return $this
     */
    public function unsetWebsite();

    /**
     * Remove plugins data from local storage
     *
     * @return void
     */
    public function clearLocalPluginData();
}