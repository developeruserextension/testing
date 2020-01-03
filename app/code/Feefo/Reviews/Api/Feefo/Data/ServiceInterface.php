<?php

namespace Feefo\Reviews\Api\Feefo\Data;

/**
 * Interface MerchantInterface
 *
 * Data Service Contract that describes service information about the store of Feefo service
 */
interface ServiceInterface
{
    const PLUGIN_ID = 'pluginId';

    const REGISTRATION_URL = 'registrationUrl';

    const REDIRECT_URL = 'redirectUrl';

    const REGISTRATION_URI = 'registrationUri';

    const CONFIGURATION_URL = 'configurationUrl';

    const CONFIGURATION_URI = 'configurationUri';

    /**
     * Retrieve plugin ID
     *
     * @return string
     */
    public function getPluginId();

    /**
     * Retrieve either registration or configuration page URL depends on which state of the flow you are
     *
     * @return string
     */
    public function getPageUrl();

    /**
     * @return string
     */
    public function getConfigurationUri();

    /**
     * @return string
     */
    public function getRedirectUrl();

    /**
     * @return string
     */
    public function getIdForRegisteredPlugin();
}