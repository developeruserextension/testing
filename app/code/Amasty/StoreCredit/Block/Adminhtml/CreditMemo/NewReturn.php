<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Block\Adminhtml\CreditMemo;

use Amasty\StoreCredit\Api\Data\SalesFieldInterface;
use Amasty\StoreCredit\Model\ConfigProvider;
use Magento\Backend\Block\Template;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;

class NewReturn extends Template
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var PriceCurrencyInterface
     */
    private $currency;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Registry $coreRegistry,
        Template\Context $context,
        PriceCurrencyInterface $currency,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $coreRegistry;
        $this->currency = $currency;
        $this->configProvider = $configProvider;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        $memo = $this->coreRegistry->registry('current_creditmemo');
        if (!$this->configProvider->isEnabled()
            || !($memo && $memo->getCustomerId())
        ) {
            return '';
        }

        return parent::toHtml();
    }

    /**
     * @return bool
     */
    public function isUseStoreCredit()
    {
        return (bool)$this->coreRegistry->registry('current_creditmemo')->getData(SalesFieldInterface::AMSC_USE);
    }

    /**
     * @return float|int
     */
    public function getMaxStoreCredit()
    {
        if ($memo = $this->coreRegistry->registry('current_creditmemo')) {
            if ($amount = $memo->getData('amstorecredit_base_amount')) {
                return $this->currency->round($memo->getData('amstorecredit_base_amount'));
            }
            return $this->currency->round($memo->getBaseGrandTotal());
        }

        return 0;
    }
}
