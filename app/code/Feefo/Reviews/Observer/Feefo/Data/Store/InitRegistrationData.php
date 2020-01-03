<?php

namespace Feefo\Reviews\Observer\Feefo\Data\Store;

use Feefo\Reviews\Api\Feefo\Data\RegistrationRequestInterface;
use Feefo\Reviews\Api\Feefo\Helper\StoreDetailsInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class InitRegistrationData
 */
class InitRegistrationData implements ObserverInterface
{
    /**
     * @var StoreDetailsInterface
     */
    protected $storeDetails;

    /**
     * InitRegistrationData constructor.
     * @param StoreDetailsInterface $storeDetails
     */
    public function __construct(
        StoreDetailsInterface $storeDetails
    ) {
        $this->storeDetails = $storeDetails;
    }

    /**
     * To add store owner data to \Feefo\Reviews\Api\Feefo\Data\RegistrationRequestInterface
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var RegistrationRequestInterface $registrationData */
        $registrationData = $observer->getEvent()->getData("data");
        $websiteId = $observer->getEvent()->getData("website_url");

        $this->storeDetails->initScope([
            "website_url" => $websiteId,
        ]);

        $registrationData->setMerchantDomain($this->storeDetails->getMerchantDomain());
        $registrationData->setMerchantUrl($this->storeDetails->getMerchantUrl());
        $registrationData->setMerchantEmail($this->storeDetails->getMerchantEmail());
        $registrationData->setMerchantName($this->storeDetails->getMerchantName());
        $registrationData->setMerchantLanguage($this->storeDetails->getMerchantLanguage());
        $registrationData->setMerchantShopOwner($this->storeDetails->getMerchantShopOwner());
        $registrationData->setStoreIds($this->storeDetails->getStoreIds());
        $registrationData->setMerchantDescription($this->storeDetails->getMerchantDescription());
        $registrationData->setMerchantImageUrl($this->storeDetails->getMerchantImageUrl());
        $registrationData->setRedirectUrl($this->storeDetails->getRedirectUrl());
    }
}
