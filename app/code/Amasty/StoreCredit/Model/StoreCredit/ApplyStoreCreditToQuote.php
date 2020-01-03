<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Model\StoreCredit;

use Amasty\StoreCredit\Api\ApplyStoreCreditToQuoteInterface;
use Amasty\StoreCredit\Api\Data\SalesFieldInterface;

class ApplyStoreCreditToQuote implements ApplyStoreCreditToQuoteInterface
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $cartRepository;

    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository
    ) {
        $this->cartRepository = $cartRepository;
    }

    /**
     * @inheritdoc
     */
    public function apply($cartId, $amount)
    {
        $quote = $this->cartRepository->get($cartId);
        $quote->setData(SalesFieldInterface::AMSC_USE, 1);
        $quote->setData('am_store_credit_set', 1);
        $quote->setAmstorecreditAmount($amount);
        $quote->collectTotals();
        $quote->save();
        return $quote->getAmstorecreditAmount();
    }

    /**
     * @inheritdoc
     */
    public function cancel($cartId)
    {
        $quote = $this->cartRepository->get($cartId);
        $quote->setData(SalesFieldInterface::AMSC_USE, 0);
        $quote->collectTotals();
        $quote->save();
        return $quote->getAmstorecreditAmount();
    }
}
