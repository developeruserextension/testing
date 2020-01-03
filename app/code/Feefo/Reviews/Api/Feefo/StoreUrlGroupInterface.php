<?php

namespace Feefo\Reviews\Api\Feefo;

use Feefo\Reviews\Api\Feefo\Data\StoreUrlGroupDataInterface;

/**
 * Interface StoreUrlGroupInterface
 */
interface StoreUrlGroupInterface
{
    /**
     * @return StoreUrlGroupDataInterface[]
     */
    public function getGroups();

    /**
     * @param string $url
     *
     * @return StoreUrlGroupDataInterface
     */
    public function getGroupByUrl($url);
}