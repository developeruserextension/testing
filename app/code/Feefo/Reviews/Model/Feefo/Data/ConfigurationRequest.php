<?php

namespace Feefo\Reviews\Model\Feefo\Data;

use Feefo\Reviews\Api\Feefo\Data\ConfigurationRequestInterface;

/**
 * Class ConfigurationRequest
 */
class ConfigurationRequest extends JsonableDataObject implements ConfigurationRequestInterface
{
    /**
     * Returns plugin id
     *
     * @return string
     */
    public function getPluginId()
    {
        return $this->getData(static::PLUGIN_ID);
    }

    /**
     * Set plugin id
     *
     * @param string $pluginId
     * @return $this
     */
    public function setPluginId($pluginId)
    {
        $this->setData(static::PLUGIN_ID, $pluginId);

        return $this;
    }

    /**
     * Returns redirect url
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->getData(static::REDIRECT_URL);
    }

    /**
     * Set redirect url
     *
     * @param string $redirectUrl
     * @return $this
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->setData(static::REDIRECT_URL, $redirectUrl);

        return $this;
    }
}