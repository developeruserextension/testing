<?php

namespace Feefo\Reviews\Model\Feefo\Data;

use Feefo\Reviews\Api\Feefo\Data\ConfigurationRequestInterface;
use Feefo\Reviews\Api\Feefo\StorageInterface;
use Magento\Backend\Model\Url;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class ConfigurationRequestFactory
 */
class ConfigurationRequestFactory
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var  Url
     */
    protected $urlBuilder;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var string
     */
    protected $instance;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * ConfigurationRequestFactory constructor.
     * @param ObjectManagerInterface $objectManager
     * @param StorageInterface $storage
     * @param Url $urlBuilder
     * @param ManagerInterface $eventManager
     * @param $instance
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        StorageInterface $storage,
        Url $urlBuilder,
        ManagerInterface $eventManager,
        $instance = ConfigurationRequestInterface::class
    ) {
        $this->objectManager = $objectManager;
        $this->storage = $storage;
        $this->urlBuilder = $urlBuilder;
        $this->instance = $instance;
        $this->eventManager = $eventManager;
    }

    /**
     * Create instance of ConfigurationRequestInterface
     *
     * @return ConfigurationRequestInterface
     */
    public function create()
    {
        /** @var $instance $configRequestData */
        $configRequestData = $this->objectManager->get($this->instance);

        $this->eventManager->dispatch('feefo_reviews_init_configuration_data', [
            'data' => $configRequestData,
        ]);

        return $configRequestData;
    }

    /**
     * Retrieve redirection URL
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->urlBuilder->getUrl('feefo/options/index');
    }

}