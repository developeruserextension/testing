<?php

namespace Feefo\Reviews\Model\Feefo\Data;

use Feefo\Reviews\Api\Feefo\Data\RegistrationRequestInterface;
use Feefo\Reviews\Api\Feefo\StorageInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class RegistrationRequestFactory
 */
class RegistrationRequestFactory
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var  ManagerInterface
     */
    protected $eventManager;

    /**
     * @var string
     */
    protected $instance;

    /**
     * RegistrationRequestFactory constructor.
     * @param ObjectManagerInterface $objectManager
     * @param StorageInterface $storage
     * @param ManagerInterface $eventManager
     * @param $instance
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        StorageInterface $storage,
        ManagerInterface $eventManager,
        $instance = RegistrationRequestInterface::class
    ) {
        $this->objectManager = $objectManager;
        $this->storage = $storage;
        $this->eventManager = $eventManager;
        $this->instance = $instance;
    }

    /**
     * Create an instance of RegistrationRequestInterface
     *
     * @param string $websiteUrl
     * 
     * @return RegistrationRequestInterface
     */
    public function create($websiteUrl)
    {
        /** @var RegistrationRequestInterface $storeData */
        $storeData = $this->objectManager->get($this->instance);
        if ($storeData instanceof RegistrationRequestInterface) {
            $storeData->setAccessToken($this->getAccessToken());

            $this->eventManager->dispatch('feefo_reviews_init_registration_data', [
                'data' => $storeData,
                'website_url' => $websiteUrl,
            ]);
        }

        return $storeData;
    }

    /**
     * Retrieve access token
     *
     * @return string
     */
    protected function getAccessToken()
    {
        return $this->storage->getAccessKey();
    }
}