<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Block\Adminhtml\Customer\Edit\Renderer;

use Amasty\StoreCredit\Api\Data\HistoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Backend\Block\Context;

class BalanceChange extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Input
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->priceCurrency = $priceCurrency;
    }

    public function render(\Magento\Framework\DataObject $row)
    {
        $difference = $this->priceCurrency->convertAndFormat(
            $row->getData(HistoryInterface::DIFFERENCE),
            false,
            2
        );

        if ($row->getData(HistoryInterface::IS_DEDUCT)) {
            $difference = '<span class="price" style="color:red">-' . $difference . '</span>';
        } else {
            $difference = '<span class="price" style="color:green">+' . $difference . '</span>';
        }
        return $difference;
    }
}
