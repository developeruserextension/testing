<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Checkout
 */


namespace Amasty\Checkout\Model;

use Amasty\Checkout\Api\ItemManagementInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Framework\Json\Helper\Data;
use Magento\Catalog\Helper\Image;
use Amasty\Checkout\Helper\Item;
use Magento\Quote\Api\ShipmentEstimationInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Quote\Model\Cart\ShippingMethod;
use Magento\Quote\Api\Data\TotalsInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class ItemManagement
 */
class ItemManagement implements ItemManagementInterface
{
    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var CartTotalRepositoryInterface
     */
    protected $cartTotalRepository;

    /**
     * @var CustomerCart
     */
    protected $cart;

    /**
     * @var TotalsFactory
     */
    protected $totalsFactory;

    /**
     * @var Data
     */
    protected $jsonHelper;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var Item
     */
    protected $itemHelper;

    /**
     * @var PaymentMethodManagementInterface
     */
    protected $paymentMethodManagement;

    /**
     * @var ShipmentEstimationInterface
     */
    protected $shipmentEstimation;

    /**
     * @var \Zend_Oauth_Http_Utility
     */
    private $utility;

    public function __construct(
        CartRepositoryInterface $cartRepository,
        CartTotalRepositoryInterface $cartTotalRepository,
        CustomerCart $cart,
        TotalsFactory $totalsFactory,
        Data $jsonHelper,
        Image $imageHelper,
        Item $itemHelper,
        ShipmentEstimationInterface $shipmentEstimation,
        PaymentMethodManagementInterface $paymentMethodManagement
    ) {
        $this->cartRepository = $cartRepository;
        $this->cartTotalRepository = $cartTotalRepository;
        $this->cart = $cart;
        $this->totalsFactory = $totalsFactory;
        $this->jsonHelper = $jsonHelper;
        $this->imageHelper = $imageHelper;
        $this->itemHelper = $itemHelper;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->shipmentEstimation = $shipmentEstimation;
        $this->utility = new \Zend_Oauth_Http_Utility();
    }

    /**
     * @inheritdoc
     */
    public function remove($cartId, $itemId, AddressInterface $address)
    {
        /** @var Quote $quote */
        $quote = $this->cartRepository->get($cartId);
        $initialVirtualState = $quote->isVirtual();
        /** @var QuoteItem $item */
        $item = $quote->getItemById($itemId);

        if ($item && $item->getId()) {
            $quote->deleteItem($item);
            $this->cartRepository->save($quote);
        }

        if ($quote->isVirtual() && !$initialVirtualState) {
            return false;
        }

        /** @var ShippingMethod $shippingMethods */
        $shippingMethods = $this->shipmentEstimation->estimateByExtendedAddress($cartId, $address);
        /** @var Totals $totals */
        $totals = $this->totalsFactory->create(
            [
                'data' => [
                    'totals' => $this->cartTotalRepository->get($cartId),
                    'shipping' => $shippingMethods,
                    'payment' => $this->paymentMethodManagement->getList($cartId)
                ]
            ]
        );

        return $totals;
    }

    /**
     * @inheritdoc
     */
    public function update($cartId, $itemId, $formData, AddressInterface $address)
    {
        /** @var Quote $quote */
        $quote = $this->cartRepository->get($cartId);
        $initialVirtualState = $quote->isVirtual();

        $this->cart->setQuote($quote);
        $params = $this->parseStr($formData);
        /** @var QuoteItem $item */
        $item = $this->cart->getQuote()->getItemById($itemId);

        if (!$item) {
            throw new LocalizedException(__('We can\'t find the quote item.'));
        }

        $params = $this->prepareParams($params, $itemId);

        $item = $this->cart->updateItem($itemId, new DataObject($params));
        if (is_string($item)) {
            throw new LocalizedException(__($item));
        }
        if ($item->getHasError()) {
            throw new LocalizedException(__($item->getMessage()));
        }

        $this->cart->save();

        if ($quote->isVirtual() && !$initialVirtualState) {
            return false;
        }

        /** @var ShippingMethod $shippingMethods */
        $shippingMethods = $this->shipmentEstimation->estimateByExtendedAddress($cartId, $address);
        /** @var TotalsInterface[] $items */
        $items = $this->cartTotalRepository->get($cartId);
        $optionsData = [];

        /** @var QuoteItem $item */
        foreach ($quote->getAllVisibleItems() as $item) {
            $optionsData[$item->getId()] = $this->itemHelper->getItemOptionsConfig($quote, $item);
        }

        $imageData = $this->getImageData($quote);

        return $this->prepareTotals($items, $imageData, $optionsData, $shippingMethods, $cartId);
    }

    /**
     * @param string $str
     *
     * @return array
     */
    public function parseStr($str)
    {
        $params = $this->utility->parseQueryString($str);

        foreach ($params as $key => $param) {
            $keyParts = preg_split("/[\[\]']/", $key, -1, PREG_SPLIT_NO_EMPTY);
            if (isset($keyParts[0])
                && (
                    $keyParts[0] === 'super_attribute'
                    || $keyParts[0] === 'bundle_option'
                    || $keyParts[0] === 'bundle_option_qty')
            ) {
                $params[$keyParts[0]][$keyParts[1]] = $param;
            }
        }

        return $params;
    }

    /**
     * @param Quote $quote
     *
     * @return array
     */
    private function getImageData(Quote $quote)
    {
        $imageData = [];

        /** @var QuoteItem $item */
        foreach ($quote->getAllVisibleItems() as $item) {
            $imageData[$item->getId()] = [
                'src' => $this->imageHelper->init(
                    $item->getProduct(),
                    'mini_cart_product_thumbnail',
                    [
                        'type' => 'thumbnail',
                        'width' => 75,
                        'height' => 75
                    ]
                )->getUrl(),
                'width' => 75,
                'height' => 75,
                'alt' => $item->getName()
            ];
        }

        return $imageData;
    }

    /**
     * @param TotalsInterface[] $items
     * @param array $imageData
     * @param array $optionsData
     * @param ShippingMethod $shippingMethods
     * @param int $cartId
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    private function prepareTotals($items, $imageData, $optionsData, $shippingMethods, $cartId)
    {
        return $this->totalsFactory->create(['data' => [
            'totals' => $items,
            'imageData' => $this->jsonHelper->jsonEncode($imageData),
            'options' => $this->jsonHelper->jsonEncode($optionsData),
            'shipping' => $shippingMethods,
            'payment' => $this->paymentMethodManagement->getList($cartId)
        ]]);
    }

    /**
     * @param array $params
     * @param int $itemId
     *
     * @return array
     */
    private function prepareParams($params, $itemId)
    {
        if (isset($params['qty'])) {
            $params['qty'] = (int)$params['qty'];
            $params['reset_count'] = true;
        }

        if (!isset($params['options'])) {
            $params['options'] = [];
        }

        $params['id'] = $itemId;

        return $params;
    }
}
