<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Block\Adminhtml;

use Amasty\StoreCredit\Api\Data\SalesFieldInterface;
use Amasty\StoreCredit\Model\ConfigProvider;
use Magento\Backend\Block\Template;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\AdminOrder\Create;

class StoreCreditBillingMethod extends Template
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Create
     */
    private $orderCreate;

    /**
     * @var int
     */
    private $customerId;

    /**
     * @var PriceCurrencyInterface
     */
    private $currency;

    public function __construct(
        Template\Context $context,
        ConfigProvider $configProvider,
        Create $orderCreate,
        PriceCurrencyInterface $currency,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configProvider = $configProvider;
        $this->orderCreate = $orderCreate;
        $this->currency = $currency;
    }

    public function isUsedStoreCredit()
    {
        return (bool)$this->orderCreate->getQuote()->getData(SalesFieldInterface::AMSC_USE);
    }

    public function getCurrentStoreCredit()
    {
        return $this->orderCreate->getQuote()->getAmstorecreditAmount();
    }

    public function getCurrencySymbol()
    {
        if ($symbol = $this->currency->getCurrency(
            null,
                $this->orderCreate->getQuote()->getQuoteCurrencyCode()
            )->getCurrencySymbol()
        ) {
            return $symbol;
        } else {
            return $this->orderCreate->getQuote()->getQuoteCurrencyCode();
        }
    }

    public function getCustomerId()
    {
        if ($this->customerId === null) {
            $this->customerId = $this->orderCreate->getQuote()->getCustomerId();
        }

        return $this->customerId;
    }

    public function canUseStoreCredit()
    {
        return $this->getCustomerId() && $this->orderCreate->getQuote()->getAmstorecreditAmount() !== null;
    }
}
