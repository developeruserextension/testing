<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Model\Total\Quote;

use Amasty\StoreCredit\Api\Data\SalesFieldInterface;
use Amasty\StoreCredit\Model\ConfigProvider;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;

class StoreCredit extends AbstractTotal
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var \Amasty\StoreCredit\Api\StoreCreditRepositoryInterface
     */
    private $storeCreditRepository;

    public function __construct(
        \Amasty\StoreCredit\Model\ConfigProvider $configProvider,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Amasty\StoreCredit\Api\StoreCreditRepositoryInterface $storeCreditRepository,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->setCode('amstorecredit');
        $this->request = $request;
        $this->configProvider = $configProvider;
        $this->priceCurrency = $priceCurrency;
        $this->storeCreditRepository = $storeCreditRepository;
    }

    /**
     * @inheritdoc
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        if ($this->configProvider->isEnabled()
            && $shippingAssignment->getShipping()->getAddress()->getAddressType() == AbstractAddress::TYPE_SHIPPING
            && $quote->getCustomerId()
            && $quote->getBaseToQuoteRate()
            && $total->getGrandTotal() > 0
        ) {
            $availableBaseStoreCredit = $this->storeCreditRepository->getByCustomerId(
                $quote->getCustomerId()
            )->getStoreCredit();

            if ($quote->isVirtual() || !$availableBaseStoreCredit) {
                return $this;
            }

            if (!($storeCredit = $this->request->getParam('am_store_credit_amount'))
                || $quote->getData('am_store_credit_set')
            ) {
                $storeCredit = (double)$quote->getAmstorecreditAmount();
            }

            $availableStoreCredit = $this->priceCurrency->convertAndRound(
                $availableBaseStoreCredit,
                null,
                $quote->getQuoteCurrencyCode(),
                2
            );

            $maxStoreCredit = $total->getGrandTotal();
            if (!$this->configProvider->isAllowOnShipping($quote->getStoreId())) {
                $maxStoreCredit -= $total->getShippingAmount();
            }

            if (!$this->configProvider->isAllowOnTax($quote->getStoreId())) {
                $maxStoreCredit -= $total->getTaxAmount();
            }

            if ($storeCredit > $availableStoreCredit) {
                $storeCredit = $availableStoreCredit;
            }

            if ($storeCredit > $maxStoreCredit) {
                $storeCredit = $maxStoreCredit;
            }

            if ($this->request->getParam('am_use_store_credit') !== null) {
                $quote->setData(SalesFieldInterface::AMSC_USE, (bool)$this->request->getParam('am_use_store_credit'));
            }

            if (!$quote->getData(SalesFieldInterface::AMSC_USE)) {
                $storeCredit = 0;
            }

            if ($storeCredit <= 0) {
                $storeCredit = min($maxStoreCredit, $availableStoreCredit);
                $quote->setData(SalesFieldInterface::AMSC_USE, 0);
            }

            $quote->setAmstorecreditAmount($storeCredit);
            $quote->setAmstorecreditBaseAmount(
                $this->priceCurrency->round($total->getBaseGrandTotal() * $storeCredit / $total->getGrandTotal())
            );

            if ($quote->getData(SalesFieldInterface::AMSC_USE)) {
                $grandTotal = $total->getGrandTotal() - $quote->getAmstorecreditAmount();
                $grandBaseTotal = $total->getBaseGrandTotal() - $quote->getAmstorecreditBaseAmount();
                if ($grandTotal < 0.0001) {
                    $grandTotal = $grandBaseTotal = 0;
                }

                $total->setGrandTotal($grandTotal);

                $total->setBaseGrandTotal($grandBaseTotal);
            }
        }

        return $this;
    }

    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        if ($this->configProvider->isEnabled()) {
            if ($quote->getData(SalesFieldInterface::AMSC_USE)) {
                return [
                    'code' => $this->getCode(),
                    'title' => __('Store Credit'),
                    'value' => -$quote->getAmstorecreditAmount()
                ];
            } else {
                return [
                    'code' => $this->getCode() . '_max',
                    'title' => __('Store Credit Max'),
                    'value' => $quote->getAmstorecreditAmount()
                ];
            }
        }

        return null;
    }
}
