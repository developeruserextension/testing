<?php

namespace Feefo\Reviews\Api\Feefo;

use Feefo\Reviews\Api\Feefo\Data\JsonableInterface;
use Feefo\Reviews\Api\Feefo\Data\ServiceInterface as RegistrationData;

/**
 * Interface UninstallPluginRequestInterface
 *
 * Service Contract that describes registration API of Feefo service
 */
interface UninstallPluginRequestInterface
{
    /**
     * Request API to uninstall plugin
     *
     * @param JsonableInterface $pluginData
     *
     * @return RegistrationData
     */
    public function uninstall(JsonableInterface $pluginData);
}