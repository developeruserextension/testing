<?php

namespace Feefo\Reviews\Model\Feefo;

use Feefo\Reviews\Api\Feefo\Data\StoreUrlGroupDataInterface;
use Feefo\Reviews\Api\Feefo\Data\StoreUrlGroupDataInterfaceFactory;
use Feefo\Reviews\Api\Feefo\StoreUrlGroupInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store as StoreModel;

/**
 * Class StoreUrlGroup
 */
class StoreUrlGroup implements StoreUrlGroupInterface
{
    /**
     * @var StoreUrlGroupDataInterface[]
     */
    protected $groups = [];

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var StoreUrlGroupDataInterfaceFactory
     */
    protected $storeUrlGroupFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        StoreRepositoryInterface $storeRepository,
        StoreUrlGroupDataInterfaceFactory $storeUrlGroupFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->storeRepository = $storeRepository;
        $this->storeUrlGroupFactory = $storeUrlGroupFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritDoc
     */
    public function getGroups()
    {
        if (!count($this->groups)) {
            $storeList = $this->storeRepository->getList();
            unset($storeList['admin']);

            /** @var StoreInterface $store */
            foreach ($storeList as $store) {
                $storeId = $store->getId();
                $url = $this->scopeConfig->getValue(
                    StoreModel::XML_PATH_UNSECURE_BASE_URL,
                    ScopeInterface::SCOPE_STORE,
                    $storeId
                );
                $group = $this->prepareGroup($url);
                $group->addStore($store);
                $this->updateGroup($group);
            }
        }

        return $this->groups;
    }

    /**
     * @inheritDoc
     */
    public function getGroupByUrl($url)
    {
        /** @var StoreUrlGroupDataInterface $group */
        foreach ($this->getGroups() as $group) {
            if ($group->getUrl() === $url) {
                return $group;
            }
        }

        return null;
    }

    /**
     * @param string $url
     *
     * @return StoreUrlGroupDataInterface
     */
    protected function prepareGroup($url)
    {
        /** @var StoreUrlGroupDataInterface $group */
        foreach ($this->groups as $group) {
            if ($group->getUrl() === $url) {
                return $group;
            }
        }

        /** @var StoreUrlGroupDataInterface $newGroup */
        $newGroup = $this->storeUrlGroupFactory->create();
        $newGroup->setUrl($url);
        $newGroup->setKey((int)count($this->groups));

        return $newGroup;
    }

    /**
     * @param StoreUrlGroupDataInterface $group
     *
     * @return void
     */
    protected function updateGroup($group)
    {
        $this->groups[$group->getKey()] = $group;
    }
}