<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Shipping;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Tax\Helper\Data as TaxHelper;
use MageWorx\OrderEditor\Model\Order;
use MageWorx\OrderEditor\Model\Quote;
use Magento\Sales\Block\Adminhtml\Order\Create\Shipping\Method\Form as ShippingMethodForm;

class Method extends ShippingMethodForm
{
    /**
     * @var Quote
     */
    protected $quote;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var \MageWorx\OrderEditor\Api\TaxManagerInterface
     */
    protected $taxManager;

    /**
     * Method constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param TaxHelper $taxData
     * @param \MageWorx\OrderEditor\Api\TaxManagerInterface $taxManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Tax\Helper\Data $taxData,
        \MageWorx\OrderEditor\Api\TaxManagerInterface $taxManager,
        array $data = []
    ) {
        $this->taxManager = $taxManager;
        parent::__construct(
            $context,
            $sessionQuote,
            $orderCreate,
            $priceCurrency,
            $taxData,
            $data
        );
    }

    /**
     * @return Quote
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * Retrieve current selected shipping method
     *
     * @return string
     */
    public function getShippingMethod()
    {
        return $this->getOrder()->getShippingMethod();
    }

    /**
     * @param Quote $quote
     * @return $this
     */
    public function setQuote($quote)
    {
        $this->quote = $quote;

        return $this;
    }

    /**
     * @param Order $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return float
     */
    public function getCurrentShippingPrice()
    {
        return $this->getOrder()->getShippingAmount();
    }

    /**
     * @return float
     */
    public function getCurrentShippingPriceInclTax()
    {
        return $this->order->getShippingAmount() + $this->order->getShippingTaxAmount();
    }

    /**
     * @param float     $price
     * @param bool|null $flag
     * @return float
     */
    public function getShippingPriceFloat($price, $flag)
    {
        return $this->_taxData->getShippingPrice(
            $price,
            $flag,
            $this->getAddress(),
            null,
            $this->getAddress()->getQuote()->getStore()
        );
    }

    /**
     * @return TaxHelper
     */
    public function getTaxHelper(): TaxHelper
    {
        return $this->_taxData;
    }

    /**
     * Get tax rates applied to the order shipping
     *
     * @return \Magento\Tax\Model\Sales\Order\Tax[]
     */
    public function getShippingActiveRates(): array
    {
        try {
            $appliedRates = $this->taxManager->getOrderShippingTaxDetails($this->getOrder());
        } catch (NoSuchEntityException $exception) {
            return [];
        }

        return $appliedRates;
    }

    /**
     * Return all available tax rate codes (whole Magento)
     *
     * @return array
     */
    public function getTaxRatesOptions(): array
    {
        return $this->taxManager->getAllAvailableTaxRateCodes();
    }

    /**
     * Get tax rate codes (classes) for the active shipping
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getShippingTaxRateCodes(): array
    {
        return $this->taxManager->getOrderShippingTaxClasses($this->getOrder());
    }

    /**
     * Returns html of the <option> tag for the tax-rates select/multiselect tag
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function renderTaxRatesOptions(): string
    {
        $options     = $this->getTaxRatesOptions();
        $values      = $this->getShippingTaxRateCodes();
        $optionsHtml = '';

        foreach ($options as $option) {
            $selected    = in_array($option['label'], $values) ? 'selected="selected"' : '';
            $optionsHtml .= '<option 
                value="' . $option['label'] . '" ' .
                'data-percent="' . round($option['percent'], 2) . '"' .
                'data-rate-id="' . $option['id'] . '"' .
                ' ' . $selected . '>' . $option['label'] . '</option>';
        }

        return $optionsHtml;
    }
}
