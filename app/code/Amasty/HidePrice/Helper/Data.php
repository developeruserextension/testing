<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Helper;

use Amasty\HidePrice\Model\Source\HideButton;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\DataObject;
use Amasty\HidePrice\Model\Source\ReplaceButton;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ROOT_CATEGORY_ID = 1;
    const NOT_LOGGED_KEY = '00';
    const DISABLED_GROUP_KEY = -1;
    const HIDE_PRICE_DATA_ROLE = 'data-role="amhideprice-hide-button"';
    const HIDE_PRICE_POPUP_IDENTIFICATOR = 'AmastyHidePricePopup';

    protected $currentCustomerGroup;
    protected $matchedCategories;
    protected $cache = [];

    /**
     * @var CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;
    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    private $sessionFactory;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    private $filterManager;
    
    /**
     * @var \Magento\Customer\Model\Url
     */
    private $customerUrl;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        CollectionFactory $categoryCollectionFactory,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Customer\Model\Url $customerUrl
    ) {
        parent::__construct($context);
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
        $this->jsonEncoder = $jsonEncoder;
        $this->sessionFactory = $sessionFactory;

        $this->currentCustomerGroup = $this->getCustomerSession()->getCustomerGroupId();
        if (!$this->currentCustomerGroup) {
            $this->currentCustomerGroup = self::NOT_LOGGED_KEY;
        }
        $this->filterManager = $filterManager;
        $this->customerUrl = $customerUrl;
    }

    private function getCustomerSession()
    {
        return $this->sessionFactory->create();
    }

    /**
     * @param string $path
     * @return string
     */
    public function getModuleConfig($path)
    {
        return $this->scopeConfig->getValue('amasty_hide_price/' . $path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isModuleEnabled()
    {
        return $this->getModuleConfig('general/enabled') && $this->isModuleOutputEnabled();
    }

    /**
     * @param ProductInterface $product
     * @return bool
     */
    public function isApplied(ProductInterface $product)
    {
        if (!$this->isModuleEnabled()) {
            return false;
        }

        if (!$this->issetCachedResult($product->getId())) {
            /* Checking settings by product and customer group. Order is important.*/
            $result = $this->checkGlobalSettings($product);
            $result = $this->checkStockStatus($result, $product);
            $result = $this->checkCategorySettings($result, $product);
            $result = $this->checkProductSettings($result, $product);
            $result = $this->checkIgnoreSettings($result, $product);
            /* save result to cache for multiple places: price button add to wishlist and other*/
            $this->saveResultToCache($result, $product->getId());
        } else {
            $result = $this->getResultFromCache($product->getId());
        }

        return $result;
    }

    /**
     * Checking module setting and output
     * @param ProductInterface $product
     * @return bool
     */
    public function isNeedHideProduct(ProductInterface $product)
    {
        $isConfigEnabled = $this->getModuleConfig('information/hide_price')
            || $this->getModuleConfig('information/hide_button');
        return $isConfigEnabled && $this->isApplied($product);
    }

    /**
     * Hide Price depend on selected categories and customer groups in configuration
     * @param ProductInterface $product
     * @return bool
     */
    private function checkGlobalSettings(ProductInterface $product)
    {
        $result = false;

        $settingCustomerGroup = $this->convertStringSettingToArray('general/customer_group');
        if (in_array($this->currentCustomerGroup, $settingCustomerGroup)) {
            $productCategories = $product->getCategoryIds();
            $settingCategories = $this->convertStringSettingToArray('general/category');

            //check for root category - hide price for all
            $result = in_array(self::ROOT_CATEGORY_ID, $settingCategories)
                || count(array_uintersect($productCategories, $settingCategories, "strcmp")) > 0
                ? true: false;
        }

        return $result;
    }

    /**
     * Plugin in stock status must work
     *
     * @param $result
     * @param ProductInterface $product
     *
     * @return bool
     */
    public function checkStockStatus($result, ProductInterface $product)
    {
        return $result;
    }

    /**
     *  Hide Price depend on selected individual category settings
     * @param $result
     * @param ProductInterface $product
     * @return bool
     */
    private function checkCategorySettings($result, ProductInterface $product)
    {
        $productCategories = $product->getCategoryIds();
        if (!$this->matchedCategories) {
            /* get categories only with not empty attributes customer_gr_cat and mode_cat */
            $collection =  $this->categoryCollectionFactory->create()
                ->addAttributeToSelect('am_hide_price_mode_cat')
                ->addAttributeToSelect('am_hide_price_customer_gr_cat')
                ->addAttributeToFilter('am_hide_price_mode_cat', ['notnull' => true])
                ->addAttributeToFilter('am_hide_price_customer_gr_cat', ['notnull' => true]);
            $this->matchedCategories = $collection->getData();
        }

        if (!empty($this->matchedCategories)) {
            foreach ($this->matchedCategories as $category) {
                if (!in_array($category['entity_id'], $productCategories)) {
                    continue;
                }
                $customerGroups = $this->trimAndExplode($category['am_hide_price_customer_gr_cat']);
                if (in_array($this->currentCustomerGroup, $customerGroups)) {
                    $result = !(bool)$category['am_hide_price_mode_cat'];
                }
            }
        }

        return $result;
    }

    /**
     *  Hide Price depend on selected individual product settings
     * @param $result
     * @param ProductInterface $product
     * @return bool
     */
    private function checkProductSettings($result, ProductInterface $product)
    {
        $mode = $product->getData('am_hide_price_mode');
        $customerGroups = $product->getData('am_hide_price_customer_gr');

        if ($mode !== null && $customerGroups) {
            $customerGroups = $this->trimAndExplode($customerGroups);
            if (in_array($this->currentCustomerGroup, $customerGroups)) {
                $result = !(bool)$mode;
            }
        }

        return $result;
    }

    /**
     * Check ignore settings - the most important
     * @param $result
     * @param ProductInterface $product
     * @return bool
     */
    private function checkIgnoreSettings($result, ProductInterface $product)
    {
        $currentCustomerId = $this->getCustomerSession()->getCustomerId();
        if ($currentCustomerId) {
            $ignoredCustomers = $this->convertStringSettingToArray('general/ignore_customer');
            if (in_array($currentCustomerId, $ignoredCustomers)) {
                return false;
            }
        }

        $ignoredProductIds = $this->convertStringSettingToArray('general/ignore_products');
        if (in_array($product->getId(), $ignoredProductIds)) {
            return false;
        }

        return $result;
    }

    /**
     * Generate button html depend on module configuration
     * @param $product
     * @return string
     */
    public function getNewPriceHtmlBox($product)
    {
        // help for magento swatches detect category page
        $html = sprintf('<div class="price-box price-final_price" data-product-id="%d"></div>', $product->getId());

        $text = $this->filterManager->stripTags(
            $this->getModuleConfig('frontend/text'),
            [
                'allowableTags' => null,
                'escape' => true
            ]
        );
        $image = $this->getModuleConfig('frontend/image');
        if ($text || $image) {
            $href = (string)$this->getModuleConfig('frontend/link');
            if ($href) {
                if ($href == self::HIDE_PRICE_POPUP_IDENTIFICATOR) {
                    $tag = $this->generatePopup($product);
                } else {
                    $href = $this->checkLoginUrl($href);
                    $tag = '<a href="' . $href . '" ';
                }
                $closeTag = '</a>';
            } else {
                $tag = '<div ';
                $closeTag = '</div>';
            }

            $customStyles = $this->getModuleConfig('frontend/custom_css');
            if ($customStyles) {
                $customStyles = 'style="' . $customStyles . '"';
            }
            $html .= $tag . ' class="amasty-hide-price-container" ' . $customStyles . '>';

            if ($image) {
                $mediaPath = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
                $image = $mediaPath . '/amasty/hide_price/' . $image;
                $html .= '<img class="amasty-hide-price-image" src="' . $image .'">';
            }

            if ($text) {
                $html .= '<span class="amasty-hide-price-text">' . $text . '</span>';
            }

            $html .= $closeTag;
        }

        return $html;
    }
    
    /**
     * @param string $href
     *
     * @return string|string
     */
    protected function checkLoginUrl(string $href)
    {
        if (strpos($href, 'customer/account/login') !== false) {
            $href = $this->customerUrl->getLoginUrl();
        }

        return $href;
    }

    /**
     * Generate button replacement html
     * @param Product|null $product
     * @param array|null $productData
     * @return string
     */
    public function getNewAddToCartHtml($product = null, $productData = null)
    {
        // help for magento swatches detect category page
        $result = '';

        if ($this->getModuleConfig('information/hide_button') == HideButton::REPLACE_WITH_NEW_ONE) {
            $text = $this->getModuleConfig('information/replace_text');
            $link = $this->getModuleConfig('information/replace_link') ?
                $this->getModuleConfig('information/replace_link') :
                '';
            if (!$product && isset($productData['id'])) {
                $product = new DataObject([
                    'id' => $productData['id'],
                    'name' => $productData['name']
                ]);
            }
            switch ($this->getModuleConfig('information/replace_with')) {
                case ReplaceButton::REDIRECT_URL:
                    $href = (string)$this->getModuleConfig('information/redirect_link');
                    $href = $this->checkLoginUrl($href);
                    $href = $href ?: '#';
                    $tag = '<a href="' . $href . '"';
                    break;
                case ReplaceButton::HIDE_PRICE_POPUP:
                case ReplaceButton::CUSTOM_FORM:
                    $tag = $this->generatePopup($product);
                    break;
            }

            $styles = $this->getModuleConfig('information/replace_css');
            $styles = $styles ? ' style="' . $styles . '"' : '';

            $result .=
                 $tag . ' class="amasty-hide-price-button" ' . $styles . '>
                    <span>' . $text . '</span>
                </a>';
        }

        return $result;
    }

    /**
     * generate Js code for Get a Quote Form
     * @param Product|DataObject $product
     * @return string
     */
    private function generateFormJs($product)
    {
        $js = '<script>';
        $js .= 'require([
                "jquery",
                 "Amasty_HidePrice/js/amhidepriceForm"
            ], function ($, amhidepriceForm) {
                amhidepriceForm.addProduct(' . $this->generateFormConfig($product) . ');                 
            });';
        $js .= '</script>';

        return $js;
    }

    private function generateFormConfig($product)
    {
        $customer = $this->getCustomerSession()->getCustomer();
        return $this->jsonEncoder->encode([
            'url' => $this->_getUrl('amasty_hide_price/request/add'),
            'id' => $product->getId(),
            'name'   => $product->getName(),
            'customer' => [
                'name'  => $customer->getName(),
                'email' => $customer->getEmail(),
                'phone' => $customer->getPhone()
            ]
        ]);
    }

    private function convertStringSettingToArray($name)
    {
        $setting = $this->getModuleConfig($name);
        $setting = $this->trimAndExplode($setting);

        return $setting;
    }

    /**
     * @param $string
     * @return array
     */
    private function trimAndExplode($string)
    {
        $string = str_replace(' ', '', $string);
        $array = explode(',', $string);

        return $array;
    }

    /**
     * @param $productId
     * @return bool
     */
    private function issetCachedResult($productId)
    {
        if (!array_key_exists($this->currentCustomerGroup, $this->cache)) {
            return false;
        }

        return array_key_exists($productId, $this->cache[$this->currentCustomerGroup]);
    }

    /**
     * @param $productId
     * @return mixed
     */
    private function getResultFromCache($productId)
    {
        return $this->cache[$this->currentCustomerGroup][$productId];
    }

    /**
     * @param $result
     * @param $productId
     */
    private function saveResultToCache($result, $productId)
    {
        if (!array_key_exists($this->currentCustomerGroup, $this->cache)) {
            $this->cache[$this->currentCustomerGroup] = [];
        }

        $this->cache[$this->currentCustomerGroup][$productId] = $result;
    }

    /**
     * @return bool
     */
    public function isGDPREnabled()
    {
        return (bool)$this->getModuleConfig('gdpr/enabled');
    }

    /**
     * @return string
     */
    public function getGDPRText()
    {
        return $this->getModuleConfig('gdpr/text');
    }

    /**
     * @param Product|DataObject $product
     * @return string
     */
    private function generatePopup($product)
    {
        $popupHtml = $this->generateFormJs($product)
            . '<a data-product-id="' . $product->getId() . '" data-amhide="'
            . self::HIDE_PRICE_POPUP_IDENTIFICATOR . '" ';

        return $popupHtml;
    }

    /**
     * @return bool
     */
    public function isCustomFormOn()
    {
        return $this->_moduleManager->isEnabled('Amasty_Customform');
    }

    /**
     * Checking module setting and output
     * @param ProductInterface $product
     * @return bool
     */
    public function isNeedHidePrice(ProductInterface $product)
    {
        return $this->getModuleConfig('information/hide_price') && $this->isApplied($product);
    }
}
