<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Checkout
 */


namespace Amasty\Checkout\Api;

use Magento\Quote\Api\Data\AddressInterface;

interface GuestItemManagementInterface
{
    /**
     * @param string $cartId
     * @param int    $itemId
     * @param AddressInterface $address
     *
     * @return \Amasty\Checkout\Api\Data\TotalsInterface|boolean
     */
    public function remove($cartId, $itemId, AddressInterface $address);

    /**
     * @param string $cartId
     * @param int    $itemId
     * @param string $formData
     * @param AddressInterface $address
     *
     * @return \Amasty\Checkout\Api\Data\TotalsInterface|boolean
     */
    public function update($cartId, $itemId, $formData, AddressInterface $address);
}
