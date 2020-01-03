<?php

namespace Feefo\Reviews\Model\Feefo\Data;

use Feefo\Reviews\Api\Feefo\Data\JsonableInterface;
use Feefo\Reviews\Api\Feefo\Data\ServiceInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ServiceFactory
 */
class ServiceFactory
{
    /** @var ObjectManagerInterface  */
    protected $objectManager;

    /** @var LoggerInterface  */
    protected $logger;

    /** @var string */
    protected $instance;

    /**
     * ServiceFactory constructor.
     * @param ObjectManagerInterface $objectManager
     * @param LoggerInterface $logger
     * @param $instance
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        LoggerInterface $logger,
        $instance = ServiceInterface::class
    ) {
        $this->objectManager = $objectManager;
        $this->logger = $logger;
        $this->instance = $instance;
    }

    /**
     * Create an instance of ServiceInterface
     *
     * @param $json string
     * @return ServiceInterface
     */
    public function create($json)
    {
        /** @var ServiceInterface $serviceData */
        $registerData = $this->objectManager->create($this->instance);
        try {
            if ($registerData instanceof JsonableInterface) {
                /** @var JsonableInterface $serviceData */
                $registerData->setJSON($json);
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $registerData;
    }
}