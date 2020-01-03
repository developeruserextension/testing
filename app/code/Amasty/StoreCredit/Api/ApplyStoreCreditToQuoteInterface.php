<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Api;

interface ApplyStoreCreditToQuoteInterface
{
    /**
     * @param int $cartId
     * @param float $amount
     *
     * @return float
     */
    public function apply($cartId, $amount);

    /**
     * @param int $cartId
     *
     * @return float
     */
    public function cancel($cartId);
}
