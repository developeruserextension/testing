<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Model\StoreCredit;

use Amasty\StoreCredit\Api\Data\SalesFieldInterface;
use Amasty\StoreCredit\Model\ConfigProvider;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;

class CheckoutConfigProvider implements ConfigProviderInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var StoreCreditRepository
     */
    private $storeCreditRepository;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        ConfigProvider $configProvider,
        Session $customerSession,
        CheckoutSession $checkoutSession,
        PriceCurrencyInterface $priceCurrency,
        StoreManagerInterface $storeManager,
        StoreCreditRepository $storeCreditRepository
    ) {
        $this->configProvider = $configProvider;
        $this->customerSession = $customerSession;
        $this->storeCreditRepository = $storeCreditRepository;
        $this->quote = $checkoutSession->getQuote();
        $this->priceCurrency = $priceCurrency;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $result = [];
        $balance = ($this->customerSession->isLoggedIn())
            ? $this->priceCurrency->convertAndRound(
                $this->storeCreditRepository->getByCustomerId($this->customerSession->getCustomerId())
                    ->getStoreCredit()
            )
            : 0;

        $result['amastyStoreCredit'] = [
            'isVisible' => $this->configProvider->isEnabled()
                && $this->customerSession->isLoggedIn()
                && $balance
                && !$this->quote->isVirtual(),
            'amStoreCreditUsed' => (bool)$this->quote->getData(SalesFieldInterface::AMSC_USE),
            'amStoreCreditAmount' => $this->quote->getData(SalesFieldInterface::AMSC_AMOUNT),
            'amStoreCreditAmountAvailable' => $balance
        ];

        return $result;
    }
}
