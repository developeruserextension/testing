<?php

namespace Feefo\Reviews\Helper;

use Feefo\Reviews\Api\Feefo\Helper\HmacInterface;
use Feefo\Reviews\Api\Feefo\Helper\StoreDetailsInterface;
use Feefo\Reviews\Api\Feefo\StorageInterface as FeefoStorageInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Hmac
 *
 * Logic of generating hmac token
 */
class Hmac implements HmacInterface
{
    const TEMPLATE_HMAC = 'pluginId={$pluginId}&host={$merchantDomain}&timeStamp={$timeStamp}';

    const PLACEHOLDER_PLUGIN_ID = '{$pluginId}';

    const PLACEHOLDER_HOST = '{$merchantDomain}';

    const PLACEHOLDER_TIMESTAMP = '{$timeStamp}';

    /**
     * Feefo Storage
     *
     * @var FeefoStorageInterface
     */
    protected $storage;

    /**
     * Message
     *
     * @var string|false
     */
    protected $hmac = false;

    /**
     * Store Details
     *
     * @var StoreDetailsInterface
     */
    protected $storeDetails;

    /**
     * Logger
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Hmac constructor
     *
     * @param FeefoStorageInterface $storage
     * @param StoreDetailsInterface $storeDetails
     * @param LoggerInterface $logger
     */
    public function __construct(
        FeefoStorageInterface $storage,
        StoreDetailsInterface $storeDetails,
        LoggerInterface $logger
    ) {
        $this->storage = $storage;
        $this->storeDetails = $storeDetails;
        $this->logger = $logger;
    }

    /**
     * Generate hmac token
     *
     * @return string
     */
    public function get()
    {
        if (!$this->hmac) {
            $pluginId = $this->getPluginId();
            $host = $this->getDomain();
            $timestamp = $this->getTimeStamp();
            $accessToken = $this->getAccessToken();

            $sourceMessage = $this->buildHmacTemplate($pluginId, $host, $timestamp);
            $this->logger->debug($sourceMessage);
            $this->hmac = hash_hmac('sha256', $sourceMessage, $accessToken);
        }

        return $this->hmac;
    }

    /**
     * Build a template for hmac token
     *
     * @param string $pluginId
     * @param string $domain
     * @param string $timestamp
     *
     * @return string
     */
    protected function buildHmacTemplate($pluginId, $domain, $timestamp)
    {
        $placeholders = [static::PLACEHOLDER_PLUGIN_ID, static::PLACEHOLDER_HOST, static::PLACEHOLDER_TIMESTAMP];
        $values = [$pluginId, $domain, $timestamp];

        return str_replace($placeholders, $values, static::TEMPLATE_HMAC);
    }

    /**
     * Retrieve plugin ID
     *
     * @return string
     */
    protected function getPluginId()
    {
        return $this->storage->getPluginId();
    }

    /**
     * Retrieve domain
     *
     * @return string
     */
    protected function getDomain()
    {
        return $this->storeDetails->getMerchantDomain();
    }

    /**
     * Retrieve timestamp
     *
     * @return string
     */
    protected function getTimeStamp()
    {
        return gmdate('Ymd');
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