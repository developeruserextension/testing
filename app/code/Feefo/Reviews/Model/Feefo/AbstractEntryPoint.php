<?php

namespace Feefo\Reviews\Model\Feefo;
use Feefo\Reviews\Api\Feefo\EntryPointInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class AbstractEntryPoint
 */
abstract class AbstractEntryPoint implements EntryPointInterface
{
    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    /**
     * AbstractEntryPoint constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get base Url of service
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->scopeConfig->getValue(static::XPATH_ENTRY_POINT_BASE_URL);
    }

    /**
     * Get URL of entry points by name
     *
     * @param string $key
     * @return string
     */
    public function getEntryPointUrl($key)
    {
        return $this->scopeConfig->getValue(static::XPATH_ENTRY_POINT_PREFIX . $key);
    }

    /**
     * Retrieve URL of API from config
     *
     * @param $key
     * @return string
     */
    protected function getApiUrl($key)
    {
        $entryPointUrl = $this->getEntryPointUrl($key);

        return $this->getBaseUrl() . $entryPointUrl;
    }

    /**
     * Retrieve URL of API with parameters
     *
     * @param $key string
     * @param $params array
     * @return string
     */
    protected function getApiUrlWithParams($key, array $params)
    {
        $placeholders = array_keys($params);
        $values = array_values($params);

        $entryPointUrl = $this->getEntryPointUrl($key);
        $builtUrlTemplate = $this->getBaseUrl() . $entryPointUrl;

        return str_replace($placeholders, $values, $builtUrlTemplate);
    }

}