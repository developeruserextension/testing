<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Block;

use Amasty\StoreCredit\Api\Data\SalesFieldInterface;
use Amasty\StoreCredit\Model\ConfigProvider;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Template;

class Total extends Template
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    public function __construct(
        ConfigProvider $configProvider,
        PriceCurrencyInterface $priceCurrency,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setInitialFields();
        $this->configProvider = $configProvider;
        $this->priceCurrency = $priceCurrency;
    }

    public function initTotals()
    {
        if (!empty($this->getParentBlock()->getSource()->getData($this->getAmountField()))) {
            $this->getParentBlock()->addTotal(
                new \Magento\Framework\DataObject(
                    [
                        'code' => 'amstorecredit',
                        'strong' => $this->getStrong(),
                        'value' => ($this->getMinus() ? -1 : 1)
                            * $this->getParentBlock()->getSource()->getData($this->getAmountField()),
                        'base_value' => ($this->getMinus() ? -1 : 1)
                            * $this->getParentBlock()->getSource()->getData($this->getBaseAmountField()),
                        'label' => __($this->getLabel()),
                    ]
                ),
                $this->getAfter()
            );
        }

        return $this;
    }

    public function setInitialFields()
    {
        if (!$this->getAmountField()) {
            $this->setAmountField(SalesFieldInterface::AMSC_AMOUNT);
        }

        if (!$this->getBaseAmountField()) {
            $this->setBaseAmountField(SalesFieldInterface::AMSC_BASE_AMOUNT);
        }

        if (!$this->getLabel()) {
            $this->setLabel(__('Store Credit'));
        }

        if ($this->getMinus() === null) {
            $this->setMinus(true);
        }

        if ($this->getStrong() === null) {
            $this->setStrong(false);
        }
    }
}
