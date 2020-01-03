<?php

namespace Feefo\Reviews\Helper;

use Feefo\Reviews\Api\Feefo\Helper\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface as StoreScopeInterface;
use Magento\Store\Model\Website;
use Feefo\Reviews\Api\Feefo\StoreUrlGroupInterface;
use Feefo\Reviews\Api\Feefo\Data\StoreUrlGroupDataInterface;
use Magento\Store\Model\WebsiteFactory;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Class ScopeConfig
 */
class ScopeConfig implements ScopeInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var WebsiteFactory
     */
    protected $websiteFactory;

    /**
     * @var Website $website
     */
    protected $website = false;

    /**
     * @var StoreUrlGroupInterface
     */
    protected $storeUrlGroup;

    /**
     * ScopeConfig constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param WebsiteFactory $websiteFactory
     * @param StoreUrlGroupInterface $storeUrlGroup
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        WebsiteFactory $websiteFactory,
        StoreUrlGroupInterface $storeUrlGroup
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->websiteFactory = $websiteFactory;
        $this->storeUrlGroup = $storeUrlGroup;
        $this->website = $this->websiteFactory->create();
    }

    /**
     * Configure a scope for getting data
     *
     * @param array $data
     *
     * @return void
     */
    public function initScope(array $data)
    {
        if (array_key_exists('website_id', $data)) {
            $websiteId = $data['website_id'];
            $this->initWebsite($websiteId);
        } elseif(array_key_exists('website_url', $data)) {
            $this->initWebsiteByUrl($data['website_url']);
        }
    }

    /**
     * Retrieve configured option from the storage for specific scope
     *
     * @param string $xpath
     * 
     * @return false|string
     */
    public function getConfig($xpath)
    {
        $data = $this->getStoreConfig($xpath);
        if (!$data) {
            $data = $this->getWebsiteConfig($xpath);
            if (!$data) {
                return $this->getDefaultConfig($xpath);
            }
        }

        return $data;
    }

    /**
     * Retrieve the chosen website
     *
     * @return Website
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Retrieve a configured option from the default scope
     *
     * @param string $path
     * @return string|null
     */
    protected function getDefaultConfig($path)
    {
        return $this->scopeConfig->getValue($path);
    }

    /**
     * Retrieve a configured option from the website scope
     *
     * @param string $path
     * @return false|string
     */
    protected function getWebsiteConfig($path)
    {
        if ($this->website->getId()) {
            return $this->scopeConfig->getValue($path, StoreScopeInterface::SCOPE_WEBSITE, $this->website->getId());
        }

        return false;
    }

    /**
     * Retrieve a configured option from the store scope
     *
     * @param string $path
     * @return false|string
     */
    protected function getStoreConfig($path)
    {
        if ($this->website->getId()) {
            $defaultStore = $this->website->getDefaultStore();
            if (!is_null($defaultStore) && $defaultStore->getId()) {
                return $this->scopeConfig->getValue($path, StoreScopeInterface::SCOPE_STORE, $defaultStore->getId());
            }
        }

        return false;
    }

    /**
     * @param int|string $id
     *
     * @return void
     */
    protected function initWebsite($id)
    {
        if (!$this->website || $this->website->getId() != $id) {
            $this->website->load($id);
        }
    }

    /**
     * @param $url
     */
    protected function initWebsiteByUrl($url)
    {
        /** @var StoreUrlGroupDataInterface $group */
        $group = $this->storeUrlGroup->getGroupByUrl($url);
        $stores = $group->getStores();

        if (count($stores)) {
            /** @var StoreInterface $store */
            $store = current($stores);
            $websiteId = $store->getWebsiteId();
            $this->initWebsite($websiteId);
        }
    }
}