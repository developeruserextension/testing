<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Utils;

use Magento\Framework\Locale\CurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;

class Price
{
    /**
     * @var CurrencyInterface
     */
    private $currency;

    /**
     * Price constructor.
     *
     * @param CurrencyInterface     $localeCurrency
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(CurrencyInterface $localeCurrency, StoreManagerInterface $storeManager)
    {
        $this->currency = $localeCurrency->getCurrency(
            $storeManager->getStore()->getBaseCurrencyCode()
        );
    }

    /**
     * @param int|string|float $price
     *
     * @return string
     */
    public function toDefaultCurrency($price = 0)
    {
        return $this->currency->toCurrency(sprintf("%f", $price));
    }
}
