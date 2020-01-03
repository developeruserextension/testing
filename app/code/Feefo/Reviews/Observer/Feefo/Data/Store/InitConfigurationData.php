<?php

namespace Feefo\Reviews\Observer\Feefo\Data\Store;

use Feefo\Reviews\Api\Feefo\Data\ConfigurationRequestInterface;
use Feefo\Reviews\Api\Feefo\Helper\StoreDetailsInterface;
use Feefo\Reviews\Model\Feefo\Storage as FeefoStorage;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class InitConfigurationData
 */
class InitConfigurationData implements ObserverInterface
{
    /**
     * @var StoreDetailsInterface
     */
    protected $storeDetails;

    /**
     * @var FeefoStorage
     */
    protected $storage;

    /**
     * InitConfigurationData constructor.
     *
     * @param FeefoStorage $storage
     * @param StoreDetailsInterface $storeDetails
     */
    public function __construct(
        FeefoStorage $storage,
        StoreDetailsInterface $storeDetails
    ) {
        $this->storeDetails = $storeDetails;
        $this->storage = $storage;
    }

    /**
     * To add store owner data to \Feefo\Reviews\Api\Feefo\Data\RegistrationRequestInterface
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var ConfigurationRequestInterface $configurationData */
        $configurationData = $observer->getEvent()->getData('data');
        $configurationData->setPluginId($this->storage->getPluginId());
        $configurationData->setRedirectUrl($this->storeDetails->getRedirectUrl());
    }
}
