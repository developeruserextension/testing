<?php

namespace Feefo\Reviews\Helper;

use Feefo\Reviews\Api\Feefo\StorageInterface as FeefoStorageInterface;
use Psr\Log\LoggerInterface;

/**
 * Class HmacUninstallPlugin
 *
 * Logic of generating hmac token for plugin uninstallation
 */
class HmacUninstallPlugin
{
    /**
     * HMAC template for plugin uninstallation
     */
    const TEMPLATE_HMAC_UNINSTALL_PLUGIN = 'pluginId={$pluginId}&timeStamp={$timeStamp}&accessToken={$accessToken}';

    /**
     * Access token placeholder
     */
    const PLACEHOLDER_ACCESS_TOKEN = '{$accessToken}';

    /**
     * Timestamp placeholder
     */
    const PLACEHOLDER_TIMESTAMP = '{$timeStamp}';

    /**
     * Plugin id placeholder
     */
    const PLACEHOLDER_PLUGIN_ID = '{$pluginId}';

    /**
     * Message
     *
     * @var string|false
     */
    protected $hmac = false;

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
     * @param LoggerInterface $logger
     */
    public function __construct(
        FeefoStorageInterface $storage,
        LoggerInterface $logger
    ) {
        $this->storage = $storage;
        $this->logger = $logger;
    }

    /**
     * Generate hmac token
     *
     * @param $pluginId
     *
     * @return string
     */
    public function get($pluginId)
    {
        $timestamp = $this->getTimeStamp();
        $accessToken = $this->getAccessToken();

        $sourceMessage = $this->buildHmacTemplate($pluginId, $timestamp, $accessToken);
        $this->logger->debug($sourceMessage);
        $this->hmac = hash_hmac('sha256', $sourceMessage, $accessToken);

        return $this->hmac;
    }

    /**
     * Build a template for hmac token
     *
     * @param string $pluginId
     * @param string $timestamp
     * @param string $accessToken
     *
     * @return string
     */
    protected function buildHmacTemplate($pluginId, $timestamp, $accessToken)
    {
        $placeholders = [static::PLACEHOLDER_PLUGIN_ID, static::PLACEHOLDER_TIMESTAMP, static::PLACEHOLDER_ACCESS_TOKEN];
        $values = [$pluginId, $timestamp, $accessToken];

        return str_replace($placeholders, $values, static::TEMPLATE_HMAC_UNINSTALL_PLUGIN);
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