<?php

namespace Feefo\Reviews\Api\Feefo\Data;

use Magento\Store\Api\Data\StoreInterface;

/**
 * Interface StoreUrlGroupDataInterface
 */
interface StoreUrlGroupDataInterface
{
    /**
     * @param int $key
     *
     * @return void
     */
    public function setKey($key);

    /**
     * @return int
     */
    public function getKey();

    /**
     * @param string $url
     *
     * @return void
     */
    public function setUrl($url);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param StoreInterface $store
     *
     * @return void
     */
    public function addStore(StoreInterface $store);

    /**
     * @return StoreInterface[]
     */
    public function getStores();

    /**
     * @param string $code
     *
     * @return StoreInterface
     */
    public function getStoreByCode($code);

    /**
     * @return int[]
     */
    public function getStoreIds();
}