<?php

namespace Feefo\Reviews\Api\Feefo;

use Feefo\Reviews\Api\Feefo\Data\JsonableInterface;
use Feefo\Reviews\Api\Feefo\Data\ServiceInterface as RegistrationData;

/**
 * Interface RegistrationInterface
 *
 * Service Contract that describes registration API of Feefo service
 */
interface RegistrationInterface
{
    /**
     * Request API to register a store
     *
     * @param JsonableInterface $storeData
     * @return RegistrationData
     */
    public function register(JsonableInterface $storeData);
}