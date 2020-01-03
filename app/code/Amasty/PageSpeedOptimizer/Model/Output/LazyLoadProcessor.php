<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_PageSpeedOptimizer
 */


namespace Amasty\PageSpeedOptimizer\Model\Output;

use Amasty\PageSpeedOptimizer\Model\ConfigProvider;
use Amasty\PageSpeedOptimizer\Model\Image\OutputImage;
use Amasty\PageSpeedOptimizer\Model\OptionSource\LazyLoadScript;
use Amasty\PageSpeedOptimizer\Model\OptionSource\PreloadStrategy;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Layout;

class LazyLoadProcessor implements OutputProcessorInterface
{
    const HOME = 'cms_index_index';
    const CATEGORY = 'catalog_category_view';
    const PRODUCT = 'catalog_product_view';
    const CMS = 'cms_page_view';
    const GENERAL = 'general';

    const PAGE_CONFIG = [
        self::HOME => 'lazy_load_home',
        self::CATEGORY => 'lazy_load_categories',
        self::PRODUCT => 'lazy_load_products',
        self::CMS => 'lazy_load_cms',
        self::GENERAL => 'lazy_load_general'
    ];

    /**
     * @var string
     */
    public $pageType;

    /**
     * @var Layout
     */
    private $layout;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Repository
     */
    private $assetRepo;

    /**
     * @var OutputImage
     */
    private $outputImage;

    /**
     * @var string
     */
    private $deviceType;

    /**
     * @var false
     */
    private $isWebpSupport;

    public function __construct(
        Layout $layout,
        ConfigProvider $configProvider,
        Repository $assetRepo,
        OutputImage $outputImage
    ) {
        $this->layout = $layout;
        $this->configProvider = $configProvider;
        $this->assetRepo = $assetRepo;
        $this->outputImage = $outputImage;
    }

    /**
     * @inheritdoc
     */
    //phpcs:ignore
    public function process(&$output, $deviceDetect)
    {
        //TODO
        $this->deviceType = $deviceDetect->getDeviceType();
        $this->isWebpSupport = $deviceDetect->isUseWebP();

        $this->detectPage($this->layout->getUpdate()->getHandles());
        if ($this->configProvider->isSimpleOptimization()) {
            $isLazy = $this->configProvider->isLazyLoad();
        } else {
            $isLazy = $this->configProvider->getConfig(
                self::PAGE_CONFIG[$this->pageType] . ConfigProvider::PART_IS_LAZY
            );
        }

        if ($isLazy) {
            $regExp = "<img(.*?)src=(\"|\'|)(.*?)(\"|\'| )(.*?)>";

            $skipImages = false;
            $skipStrategy = PreloadStrategy::SKIP_IMAGES;
            $userAgentIgnoreList = [];

            if ($this->configProvider->isReplaceImagesUsingUserAgent() && !empty($this->deviceType)) {
                $type = '_' . $this->deviceType;
                $userAgentIgnoreList = $this->configProvider->getReplaceImagesUsingUserAgentIgnoreList();
            } else {
                $type = '';
            }
            if (!$this->configProvider->isSimpleOptimization()) {
                $lazyScript = $this->configProvider->getConfig(
                    self::PAGE_CONFIG[$this->pageType] . ConfigProvider::PART_SCRIPT
                );
                $ignoreList = $this->configProvider->getConfig(
                    self::PAGE_CONFIG[$this->pageType] . ConfigProvider::PART_IGNORE
                );
                if (empty($ignoreList)) {
                    $ignoreList = [];
                } else {
                    $ignoreList = array_map('trim', explode(PHP_EOL, $ignoreList));
                }

                if ($this->configProvider->getConfig(
                    self::PAGE_CONFIG[$this->pageType] . ConfigProvider::PART_PRELOAD
                )) {
                    $skipImages = $this->configProvider->getConfig(
                        self::PAGE_CONFIG[$this->pageType] . ConfigProvider::PART_SKIP . $type
                    );
                    $skipStrategy = $this->configProvider->getConfig(
                        self::PAGE_CONFIG[$this->pageType] . ConfigProvider::PART_STRATEGY
                    );
                }
            } else {
                $lazyScript = $this->configProvider->lazyLoadScript();
                $ignoreList = $this->configProvider->getIgnoreImages();
                if ($this->configProvider->isPreloadImages()) {
                    $skipImages = $this->configProvider->skipImagesCount($type);
                    $skipStrategy = $this->configProvider->getSkipStrategy();
                }
            }

            if ($skipImages === false) {
                $skipImages = 0;
            }

            $tempOutput = preg_replace('/<script[^>]*>(?>.*?<\/script>)/is', '', $output);
            if (preg_match_all('/' . $regExp . '/is', $tempOutput, $images)) {
                $skipCounter = 1;
                foreach ($images[0] as $key => $image) {
                    $skip = false;
                    foreach ($ignoreList as $item) {
                        if (strpos($image, $item) !== false) {
                            $skip = true;
                            break;
                        }
                    }

                    foreach ($userAgentIgnoreList as $item) {
                        if (strpos($image, $item) !== false) {
                            $skip = true;
                            break;
                        }
                    }

                    if ($skip) {
                        continue;
                    }

                    if ($skipCounter < $skipImages) {
                        if ($this->configProvider->isReplaceImagesUsingUserAgent()) {
                            $newImg = $this->replaceWithBest($image, $images[3][$key]);
                            $output = str_replace($image, $newImg, $output);
                        } else {
                            if ($skipStrategy == PreloadStrategy::SKIP_IMAGES) {
                                $skipCounter++;
                                continue;
                            }
                            $newImg = $this->replaceWithPictureTag($image, $images[3][$key]);
                            $output = str_replace($image, $newImg, $output);
                        }
                        $skipCounter++;
                        continue;
                    }

                    $replace = 'src=' . $images[2][$key] . $images[3][$key] . $images[4][$key];
                    $placeholder = 'src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABC'
                        . 'AQAAAC1HAwCAAAAC0lEQVR4nGP6zwAAAgcBApocMXEAAAAASUVORK5CYII="';

                    $newImg = str_replace($replace, $placeholder . ' data-am' . $replace, $image);
                    if ($this->configProvider->isReplaceImagesUsingUserAgent()) {
                        $newImg = $this->replaceWithBest($newImg, $images[3][$key]);
                    }
                    $output = str_replace($image, $newImg, $output);
                }
            }
            $this->addLazyScript($output, $lazyScript);
        } else {
            if ($this->configProvider->isSimpleOptimization()) {
                $isReplaceImages = $this->configProvider->isReplaceWithWebp();
            } else {
                $isReplaceImages = (bool)$this->configProvider->getConfig(
                    self::PAGE_CONFIG[$this->pageType] . ConfigProvider::PART_REPLACE_WITH_WEBP
                );
            }

            if ($isReplaceImages || $this->configProvider->isReplaceImagesUsingUserAgent()) {
                $this->replaceImages($output);
            }
        }

        return true;
    }

    public function replaceWithPictureTag($image, $imagePath)
    {
        $outputImage = $this->outputImage->setPath($imagePath);
        if ($outputImage->process() && $sourceSet = $outputImage->getSourceSet()) {
            return '<picture>' . $sourceSet . $image . '</picture>';
        }

        return $image;
    }

    public function replaceWithBest($image, $imagePath)
    {
        $outputImage = $this->outputImage->setPath($imagePath);
        if ($outputImage->process()) {
            return str_replace($imagePath, $outputImage->getBest($this->deviceType, $this->isWebpSupport), $image);
        }

        return $image;
    }

    /**
     * @param string $output
     *
     * @return void
     */
    public function replaceImages(&$output)
    {
        $regExp = "<img(.*?)src=(\"|\'|)(.*?)(\"|\'| )(.*?)>";
        if ($this->configProvider->isReplaceImagesUsingUserAgent()) {
            $ignoreList = $this->configProvider->getReplaceImagesUsingUserAgentIgnoreList();
        } else {
            if (!$this->configProvider->isSimpleOptimization()) {
                $ignoreList = $this->configProvider->getConfig(
                    self::PAGE_CONFIG[$this->pageType] . ConfigProvider::PART_REPLACE_IGNORE
                );

                if (empty($ignoreList)) {
                    $ignoreList = [];
                } else {
                    $ignoreList = array_map('trim', explode(PHP_EOL, $ignoreList));
                }
            } else {
                $ignoreList = $this->configProvider->getReplaceIgnoreList();
            }
        }

        $tempOutput = preg_replace('/<script.*?>.*?<\/script.*?>/is', '', $output);
        if (preg_match_all('/' . $regExp . '/is', $tempOutput, $images)) {
            foreach ($images[0] as $key => $image) {
                $skip = false;
                foreach ($ignoreList as $item) {
                    if (strpos($image, $item) !== false) {
                        $skip = true;
                        break;
                    }
                }

                if ($skip) {
                    continue;
                }

                if ($this->configProvider->isReplaceImagesUsingUserAgent()) {
                    $newImg = $this->replaceWithBest($image, $images[3][$key]);
                } else {
                    $newImg = $this->replaceWithPictureTag($image, $images[3][$key]);
                }

                $output = str_replace($image, $newImg, $output);
            }
        }
    }

    public function addLazyScript(&$output, $lazyScript)
    {
        switch ($lazyScript) {
            case LazyLoadScript::NATIVE_LAZY:
                $lazy = '<script>' . \Amasty\PageSpeedOptimizer\Model\Js\NativeJsUglify::SCRIPT . '</script>';
                break;
            case LazyLoadScript::JQUERY_LAZY:
            default:
                $lazy = '<script>
                        require(["jquery"], function (jquery) {
                            require(["Amasty_PageSpeedOptimizer/js/jquery.lazy"], function(lazy) {
                                if (document.readyState === "complete") {
                                    window.jQuery("img[data-amsrc]").lazy({"bind":"event", "attribute": "data-amsrc"});
                                } else {
                                    window.jQuery("img[data-amsrc]").lazy({"attribute": "data-amsrc"});
                                }
                            })
                        });
                    </script>';
                break;
        }
        $output = str_ireplace('</body', $lazy . '</body', $output);
    }

    /**
     * @param array $handles
     */
    public function detectPage($handles = [])
    {
        if (in_array(self::HOME, $handles)) {
            $this->pageType = self::HOME;
        } elseif (in_array(self::CMS, $handles)) {
            $this->pageType = self::CMS;
        } elseif (in_array(self::CATEGORY, $handles)) {
            $this->pageType = self::CATEGORY;
        } elseif (in_array(self::PRODUCT, $handles)) {
            $this->pageType = self::PRODUCT;
        } else {
            $this->pageType = self::GENERAL;
        }
    }
}
