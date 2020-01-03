<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Checkout
 */


namespace Amasty\Checkout\Plugin\View\Page\Config;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\View\Asset\File;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Config\Renderer as MagentoRenderer;
use Amasty\Checkout\Model\Config as ConfigProvider;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Asset\GroupedCollection;

/**
 * Class Renderer
 */
class Renderer
{
    const CACHE_KEY = 'amasty_checkout_should_load_css_file';

    protected $filesToCheck = ['css/styles-l.css', 'css/styles-m.css'];

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var Repository
     */
    private $assetRepo;

    /**
     * @var GroupedCollection
     */
    private $pageAssets;

    /**
     * @var ConfigProvider
     */
    private $checkoutConfig;

    public function __construct(
        Config $config,
        CacheInterface $cache,
        ConfigProvider $checkoutConfig,
        Repository $assetRepo,
        GroupedCollection $pageAssets
    ) {
        $this->config = $config;
        $this->cache = $cache;
        $this->checkoutConfig = $checkoutConfig;
        $this->assetRepo = $assetRepo;
        $this->pageAssets = $pageAssets;
    }

    /**
     * Add our css file if less functionality is missing
     *
     * @param MagentoRenderer $subject
     * @param array $resultGroups
     *
     * @return array
     */
    public function beforeRenderAssets(MagentoRenderer $subject, $resultGroups = [])
    {
        if (!$this->checkoutConfig->isEnabled()) {
            $file = 'Amasty_Checkout::js/amastyCheckoutDisabled.js';
            $asset = $this->assetRepo->createAsset($file);
            $this->pageAssets->insert($file, $asset, 'requirejs/require.js');
            return [$resultGroups];
        }

        $shouldLoad = $this->cache->load(self::CACHE_KEY);

        if ($shouldLoad === false) {
            $shouldLoad = $this->isShouldLoadCss();
            $this->cache->save($shouldLoad, self::CACHE_KEY);
        }

        if ($shouldLoad) {
            $this->config->addPageAsset('Amasty_Checkout::css/source/mkcss/am-checkout.css');
        }

        return [$resultGroups];
    }

    /**
     * @return int
     */
    private function isShouldLoadCss()
    {
        /** @var GroupedCollection $collection */
        $collection = $this->config->getAssetCollection();
        $found = 0;
        /** @var File $item */
        foreach ($collection->getAll() as $item) {
            if ($item instanceof File
                && in_array($item->getFilePath(), $this->filesToCheck)
            ) {
                $found++;
            }
        }

        return (int)($found < count($this->filesToCheck));
    }
}
