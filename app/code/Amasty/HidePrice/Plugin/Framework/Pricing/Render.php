<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Plugin\Framework\Pricing;

use Amasty\HidePrice\Helper\Data;
use Magento\Framework\Pricing\Render as PricingRender;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Framework\Pricing\Price\PriceInterface;

class Render
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * Render constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param Data $helper
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        Data $helper
    ) {
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->coreRegistry = $coreRegistry;
        $this->eventManager = $eventManager;
        $this->helper = $helper;
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * @param PricingRender $subject
     * @param callable $proceed
     * @param $priceCode
     * @param SaleableInterface $saleableItem
     * @param array $arguments
     * @return string
     */
    public function aroundRender(
        PricingRender $subject,
        callable $proceed,
        $priceCode,
        SaleableInterface $saleableItem,
        array $arguments = []
    ) {
        $additional = '';
        if (!$this->isNeedRenderPrice($saleableItem, $arguments)) {
            $this->saveDataToSession($saleableItem);
            $additional = $this->generateJsHideOnCategory($saleableItem);

            if ($this->helper->getModuleConfig('information/hide_price')) {
                $priceHtml = $this->getNewPriceHtmlBox($saleableItem, $priceCode, $arguments);
                return $priceHtml . $additional;
            }
        }

        return $proceed($priceCode, $saleableItem, $arguments) . $additional;
    }

    /**
     * @param PricingRender $subject
     * @param callable $proceed
     * @param AmountInterface $amount
     * @param PriceInterface $price
     * @param SaleableInterface $saleableItem
     * @param array $arguments
     * @return string
     */
    public function aroundRenderAmount(
        PricingRender $subject,
        callable $proceed,
        AmountInterface $amount,
        PriceInterface $price,
        SaleableInterface $saleableItem = null,
        array $arguments = []
    ) {
        if ($this->isNeedRenderPrice($saleableItem, $arguments)) {
            // Show Price Box
            $result = $proceed($amount, $price, $saleableItem, $arguments);
            return $result;
        }

        return '';
    }

    /**
     * @param $saleableItem
     * @param $arguments
     *
     * @return bool
     */
    private function isNeedRenderPrice($saleableItem, $arguments)
    {
        // if Item not a product - show price
        $isNotProduct = !($saleableItem instanceof ProductInterface);
        // is current price block zone is not list or view
        $isNoZone = (key_exists('zone', $arguments)
            && !in_array($arguments['zone'], [PricingRender::ZONE_ITEM_LIST, PricingRender::ZONE_ITEM_VIEW]));

        $isShowPrice = !$this->helper->isModuleEnabled()
            || $isNotProduct
            || $isNoZone
            || !$this->helper->isNeedHideProduct($saleableItem);

        return $isShowPrice;
    }

    private function getNewPriceHtmlBox($saleableItem, $priceCode, $arguments)
    {
        $html = '';

        /* get price replacement only for final price - others is hided*/
        $arguments['id_suffix'] = isset($arguments['id_suffix']) ? $arguments['id_suffix'] : '';
        if (in_array($priceCode, ['final_price', 'wishlist_configured_price']) && $arguments['id_suffix'] != 'copy-') {
            $html = $this->helper->getNewPriceHtmlBox($saleableItem);
        }

        return $html;
    }

    /**
     * @deprecated Hiding product button in another function
     * Js for for hiding product button on category page
     *
     * @param $saleableItem
     *
     * @return string
     * @internal param string $sku
     * @internal param int $id
     */
    private function generateJsHideOnCategory($saleableItem)
    {
        if ($saleableItem->getHidePriceObserved()) {
            return;
        }

        $id = $saleableItem->getId();
        $name = $saleableItem->getName();

        $productId = 'amhideprice-product-id-' . $id . '-' . random_int(1, 10000);
        // TODO - remove js code - button is replaced by observer
        $html = '<span ' . Data::HIDE_PRICE_DATA_ROLE . '  id="'
            . $productId . '" style="display: none !important;"></span>
             <script>
                require([
                    "jquery",
                     "Amasty_HidePrice/js/amhideprice"
                ], function ($, amhideprice) {
                    $( document ).ready(function() {                     
                        $("#' . $productId . '").amhideprice(' .
                            $this->jsonEncoder->encode([
                                'parent' => $this->helper->getModuleConfig('developer/parent'),
                                'button' => $this->helper->getModuleConfig('developer/addtocart'),
                                'html' => $this->helper->getNewAddToCartHtml(null, [
                                    'id' => $id,
                                    'name' => $name
                                ]),
                                'hide_compare' => $this->helper->getModuleConfig('information/hide_compare'),
                                'hide_wishlist' => $this->helper->getModuleConfig('information/hide_wishlist'),
                                'hide_addtocart' => $this->helper->getModuleConfig('information/hide_button')
                            ])
                        . ');
                    });
                });
            </script>';
        $saleableItem->setHidePriceObserved(true);

        return $html;
    }

    /**
     * @param $saleableItem
     */
    private function saveDataToSession(SaleableInterface $saleableItem)
    {
        $dataArray = $this->coreRegistry->registry('amasty_hideprice_data_array');
        if (!$dataArray) {
            $dataArray = [];
        }

        $dataArray[] = [
            'sku' => $saleableItem->getSku(),
            'id' => $saleableItem->getId(),
            'name' => $saleableItem->getName()
        ];

        $this->coreRegistry->unregister('amasty_hideprice_data_array');
        $this->coreRegistry->register('amasty_hideprice_data_array', $dataArray);
    }

    /**
     * @param string $html
     * @param $saleableItem
     * @return string
     */
    private function updatePriceHtml($html, $saleableItem)
    {
        $html .= $this->generateJsHideOnCategory($saleableItem);

        return $html;
    }
}
