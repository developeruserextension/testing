<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Checkout
 */


namespace Amasty\Checkout\Model;

use Amasty\Checkout\Api\QuoteManagementInterface;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\Session;
use Magento\Quote\Api\BillingAddressManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ShippingAddressManagementInterface;
use Psr\Log\LoggerInterface;
use Magento\Quote\Model\ResourceModel\Quote\Address as ResourceAddress;

/**
 * Class QuoteManagement
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @codingStandardsIgnoreStart
 */
class QuoteManagement implements QuoteManagementInterface
{

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ShippingAddressManagementInterface
     */
    private $shippingAddressManagement;

    /**
     * @var BillingAddressManagementInterface
     */
    private $billingAddressManagement;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var ResourceAddress
     */
    private $address;

    public function __construct(
        LoggerInterface $logger,
        CartRepositoryInterface\Proxy $quoteRepository,
        ShippingAddressManagementInterface\Proxy $shippingAddressManagement,
        BillingAddressManagementInterface\Proxy $billingAddressManagement,
        ResourceAddress $address,
        Session $session
    ) {
        $this->logger = $logger;
        $this->quoteRepository = $quoteRepository;
        $this->shippingAddressManagement = $shippingAddressManagement;
        $this->billingAddressManagement = $billingAddressManagement;
        $this->address = $address;
        $this->session = $session;
    }

    /**
     * @inheritdoc
     */
    function saveInsertedInfo(
        $cartId,
        AddressInterface $shippingAddressFromData = null,
        AddressInterface $newCustomerBillingAddress = null,
        $selectedPaymentMethod = null,
        $selectedShippingRate = null,
        $validatedEmailValue = null
    ) {
        $quote = null;

        if ($this->session->isLoggedIn()) {
            list($shippingAddressFromData, $newCustomerBillingAddress) = $this->retrieveAddressFromCustomer(
                $cartId,
                $shippingAddressFromData,
                $newCustomerBillingAddress
            );
        }

        if ($validatedEmailValue) {
            $shippingAddressFromData->setEmail($validatedEmailValue);
        }

        if ($selectedShippingRate) {
            /** @var Quote $quote */
            $quote = $this->quoteRepository->getActive($cartId);
            $shippingAddressFromData->setShippingMethod($selectedShippingRate);
            $shippingAddressFromData->setShippingDescription($quote->getShippingAddress()->getShippingDescription());
        }

        $this->saveInfo($cartId, $shippingAddressFromData, $newCustomerBillingAddress, $selectedPaymentMethod, $quote);

        return true;
    }

    /**
     * @param int $cartId
     * @param AddressInterface|null $shippingAddressFromData
     * @param AddressInterface|null $newCustomerBillingAddress
     *
     * @return array
     */
    private function retrieveAddressFromCustomer(
        $cartId,
        AddressInterface $shippingAddressFromData = null,
        AddressInterface $newCustomerBillingAddress = null
    ) {
        if ($shippingAddressFromData && $newCustomerBillingAddress) {
            return [$shippingAddressFromData, $newCustomerBillingAddress];
        }

        $customerAddresses = $this->session->getCustomerData()->getAddresses();
        $billingAddress = null;
        $shippingAddress = null;

        /** @var Address $customerAddress */
        foreach ($customerAddresses as $customerAddress) {
            if ($customerAddress->isDefaultBilling()) {
                $billingAddress = $customerAddress->__toArray();
            }

            if ($customerAddress->isDefaultShipping()) {
                $shippingAddress = $customerAddress->__toArray();
            }
        }

        if ($newCustomerBillingAddress === null) {
            /** @var AddressInterface $newCustomerBillingAddress */
            $newCustomerBillingAddress = $this->billingAddressManagement->get($cartId);
            $newCustomerBillingAddress->addData($billingAddress);
        }

        if ($shippingAddressFromData === null) {
            /** @var AddressInterface $shippingAddressFromData */
            $shippingAddressFromData = $this->shippingAddressManagement->get($cartId);
            $shippingAddressFromData->addData($shippingAddress);
        }

        return [$shippingAddressFromData, $newCustomerBillingAddress];
    }

    /**
     * @param int $cartId
     * @param AddressInterface|null $shippingAddressFromData
     * @param AddressInterface|null $newCustomerBillingAddress
     * @param string|null $selectedPaymentMethod
     * @param Quote $quote
     */
    private function saveInfo(
        $cartId,
        AddressInterface $shippingAddressFromData = null,
        AddressInterface $newCustomerBillingAddress = null,
        $selectedPaymentMethod = null,
        Quote $quote = null
    ) {
        try {
            if ($shippingAddressFromData) {
                $this->shippingAddressManagement->assign($cartId, $shippingAddressFromData);
            }

            if ($newCustomerBillingAddress) {
                $this->address->save($newCustomerBillingAddress);
            }

            if ($selectedPaymentMethod) {
                if (!$quote) {
                    /** @var Quote $quote */
                    $quote = $this->quoteRepository->getActive($cartId);
                }
                $quote->getPayment()->setMethod($selectedPaymentMethod);
                $quote->setDataChanges(true);
                $this->quoteRepository->save($quote);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }
}
