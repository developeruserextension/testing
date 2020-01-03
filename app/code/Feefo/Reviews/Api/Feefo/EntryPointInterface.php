<?php

namespace Feefo\Reviews\Api\Feefo;

/**
 * Interface EntryPointInterface
 */
interface EntryPointInterface
{
    const XPATH_ENTRY_POINT_BASE_URL    = 'feefo/general/base_url';

    const XPATH_ENTRY_POINT_PREFIX      = 'feefo/entry_points/';

    /**
     * Get base Url of service
     *
     * @return string
     */
    public function getBaseUrl();

    /**
     * Get URL of entry points by name
     *
     * @param string $key
     * @return string
     */
    public function getEntryPointUrl($key);
}