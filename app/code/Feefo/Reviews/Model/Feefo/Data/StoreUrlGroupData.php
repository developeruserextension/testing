<?php

namespace Feefo\Reviews\Model\Feefo\Data;

use Feefo\Reviews\Api\Feefo\Data\StoreUrlGroupDataInterface;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Class StoreUrlGroupData
 */
class StoreUrlGroupData implements StoreUrlGroupDataInterface
{
    /**
     * @var int
     */
    protected $key;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var StoreInterface[]
     */
    protected $stores = [];

    /**
     * @inheritDoc
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @inheritDoc
     */
    public function getKey()
    {
        return $this->key;
    }


    /**
     * @inheritDoc
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @inheritDoc
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @inheritDoc
     */
    public function addStore(StoreInterface $store)
    {
        $storeId = $store->getCode();
        $this->stores[$storeId] = $store;
    }

    /**
     * @inheritDoc
     */
    public function getStores()
    {
        return $this->stores;
    }

    /**
     * @inheritDoc
     */
    public function getStoreByCode($code)
    {
        if (!array_key_exists($code, $this->stores)) {
            return null;
        }

        return $this->stores[$code];
    }

    /**
     * @inheritDoc
     */
    public function getStoreIds()
    {
        $storeIds = [];

        /** @var StoreInterface $store */
        foreach ($this->stores as $store) {
            $storeIds[] = (int)$store->getId();
        }

        return $storeIds;
    }
}