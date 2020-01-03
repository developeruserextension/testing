<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Block\Adminhtml;

use Amasty\StoreCredit\Api\Data\SalesFieldInterface;

class Total extends \Magento\Sales\Block\Adminhtml\Order\Totals\Item
{
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $adminHelper, $data);
        $this->priceCurrency = $priceCurrency;
        $this->setInitialFields();
    }

    protected function _initTotals()
    {
        parent::_initTotals();

        $this->addTotal(
            new \Magento\Framework\DataObject(
                [
                    'code' => 'amstorecredit',
                    'strong' => $this->getStrong(),
                    'value' => $this->getSource()->getData($this->getAmountField()),
                    'base_value' => $this->getSource()->getData($this->getBaseAmountField()),
                    'label' => __($this->getLabel()),
                ]
            ),
            $this->getAfter()
        );

        return $this;
    }

    public function getStoreCredit()
    {
        return $this->getSource()->getData(SalesFieldInterface::AMSC_AMOUNT);
    }

    public function getFormatStoreCredit()
    {
        $source = $this->getSource();
        $result = $this->priceCurrency->format(
            ($this->getMinus() ? -1 : 1) * $source->getData($this->getBaseAmountField()),
            null,
            null,
            null,
            $source->getBaseCurrencyCode()
        );
        if ($this->getStrong()) {
            $result = '<strong>' . $result . '</strong>';
        }
        if ($source->getBaseCurrencyCode() !== $source->getOrderCurrencyCode()) {
            $result .= '<br>[<span class="price">'
                . $this->priceCurrency->format(
                    ($this->getMinus() ? -1 : 1) * $source->getData($this->getAmountField()),
                    true,
                    2,
                    null,
                    $source->getOrderCurrencyCode()
                )
                . ']</span>';
        }

        return $result;
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
