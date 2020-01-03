<?php

namespace Feefo\Reviews\Api\Feefo\Data;

/**
 * Interface RequestInterface
 */
interface ConfigurationRequestInterface
{
    const PLUGIN_ID = 'pluginId';
    
    const REDIRECT_URL = 'redirectUrl';

    /**
     * Returns plugin id
     *
     * @return string
     */
    public function getPluginId();

    /**
     * Set plugin id
     *
     * @param string $pluginId
     * @return $this
     */
    public function setPluginId($pluginId);

    /**
     * Returns redirect url
     *
     * @return string
     */
    public function getRedirectUrl();

    /**
     * Set redirect url
     *
     * @param string $redirectUrl
     * @return $this
     */
    public function setRedirectUrl($redirectUrl);
}