<?php

namespace Feefo\Reviews\Block;

use Feefo\Reviews\Api\Feefo\Data\WidgetConfigInterface;
use Feefo\Reviews\Api\Feefo\Data\WidgetSnippetInterface;
use Feefo\Reviews\Api\Feefo\StorageInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store as StoreModel;

/**
 * Class AbstractWidget
 */
abstract class AbstractWidget extends Template
{
    /** @var StorageInterface  */
    protected $storage;

    /** @var false|WidgetConfigInterface */
    protected $widgetSettings = false;

    /** @var false|WidgetSnippetInterface */
    protected $widgetSnippets = false;

    /**
     * AbstractWidget constructor
     *
     * @param TemplateContext $context
     * @param StorageInterface $storage
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        StorageInterface $storage,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->storage = $storage;
    }

    /**
     * Check if the current widget is enabled
     *
     * @return boolean
     */
    public function isEnabledWidget()
    {
        return $this->isWebsiteConfigured();
    }

    /**
     * Check if the current website has configured for widget showing
     *
     * @return bool
     */
    public function isWebsiteConfigured()
    {
        return $this->getConfiguredWebsiteUrl() === $this->getCurrentWebsiteUrl();
    }

    /**
     * Retrieve a code snippet for the current widget
     *
     * @return string
     */
    abstract public function getSnippet();

    /**
     * Retrieve widget settings
     *
     * @return false|WidgetConfigInterface
     */
    protected function getWidgetSettings()
    {
        if (!$this->widgetSettings) {
            $this->widgetSettings = $this->storage->getWidgetSettings($this->getCurrentStoreId());
        }

        return $this->widgetSettings;
    }

    /**
     * Retrieve widget snippets
     *
     * @return false|WidgetSnippetInterface
     */
    protected function getWidgetSnippets()
    {
        if (!$this->widgetSnippets) {
            $this->widgetSnippets = $this->storage->getWidgetSnippets($this->getCurrentStoreId());
        }

        return $this->widgetSnippets;
    }

    /**
     * Retrieve URL of the current site
     *
     * @return string
     */
    protected function getCurrentWebsiteUrl()
    {
        return $this->_scopeConfig->getValue(
            StoreModel::XML_PATH_UNSECURE_BASE_URL,
            ScopeInterface::SCOPE_STORE,
            $this->getCurrentStoreId()
        );
    }

    /**
     * Retrieve ID of configured website
     *
     * @return string
     */
    protected function getConfiguredWebsiteUrl()
    {
        return $this->storage->getWebsiteUrl($this->getCurrentStoreId());
    }

    /**
     * @return int
     */
    protected function getCurrentStoreId()
    {
        /** @var StoreInterface $store */
        $store = $this->_storeManager->getStore();

        return $store->getId();
    }
}