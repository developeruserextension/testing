<?php

namespace Feefo\Reviews\Service;

use Feefo\Reviews\Api\Feefo\Data\JsonableInterfaceFactory;
use Feefo\Reviews\Api\Feefo\StorageInterface as FeefoStorageInterface;
use Feefo\Reviews\Api\Feefo\StoreUrlGroupInterface;
use Feefo\Reviews\Api\Feefo\UninstallPluginRequestInterface;
use Feefo\Reviews\Helper\HmacUninstallPlugin;
use Feefo\Reviews\Model\Feefo\Data\JsonableDataObject;
use Magento\Backend\App\Action as BackendAction;

/**
 * Class UninstallPluginService
 */
class UninstallPluginService
{
    /**
     * Uninstall Plugin Service
     *
     * @var UninstallPluginRequestInterface
     */
    protected $uninstallPluginService;

    /**
     * Hmac Uninstall Plugin
     *
     * @var HmacUninstallPlugin
     */
    protected $hmac;

    /**
     * StoreUrlGroup
     *
     * @var StoreUrlGroupInterface
     */
    protected $storeUrlGroup;

    /**
     * Feefo Storage
     *
     * @var FeefoStorageInterface
     */
    protected $storage;

    /**
     * Jsonable Data Object Factory
     *
     * @var JsonableInterfaceFactory
     */
    protected $jsonableFactory;

    /**
     * UnistallPluginService constructor
     *
     * @param UninstallPluginRequestInterface $uninstallPluginRequest
     * @param HmacUninstallPlugin $hmac
     * @param StoreUrlGroupInterface $storeUrlGroup
     * @param FeefoStorageInterface $storage
     * @param JsonableInterfaceFactory $jsonableFactory
     */
    public function __construct(
        UninstallPluginRequestInterface $uninstallPluginRequest,
        HmacUninstallPlugin $hmac,
        StoreUrlGroupInterface $storeUrlGroup,
        FeefoStorageInterface $storage,
        JsonableInterfaceFactory $jsonableFactory
    ) {
        $this->uninstallPluginRequest = $uninstallPluginRequest;
        $this->storeUrlGroup = $storeUrlGroup;
        $this->hmac = $hmac;
        $this->storage = $storage;
        $this->jsonableFactory = $jsonableFactory;
    }

    /**
     * Uninstall Plugin
     *
     * @param bool $uninstallLocalData
     *
     * @return bool
     */
    public function execute($uninstallLocalData = false)
    {
        $groups = $this->storeUrlGroup->getGroups();

        $isSuccess = false;

        foreach ($groups as $group) {
            $storesIds = $group->getStoreIds();
            if (isset($storesIds[0])) {
                $isSuccess = $this->uninstallPlugin($storesIds[0]) || $isSuccess;
            }
        }

        if ($uninstallLocalData) {
            $this->storage->clearLocalPluginData();
        }

        return $isSuccess;
    }

    /**
     * Uninstall Plugin Request
     *
     * @param $storeId
     *
     * @return bool
     */
    protected function uninstallPlugin($storeId)
    {
        $pluginId = $this->storage->getPluginId($storeId);

        if (!$pluginId) {
            return false;
        }

        $message = $this->hmac->get($pluginId);
        $data = [
            'pluginId' => $pluginId,
            'hmac' => $message,
        ];
        /** @var JsonableDataObject $pluginData */
        $pluginData = $this->jsonableFactory->create();
        $pluginData->setData($data);
        $result = $this->uninstallPluginRequest->uninstall($pluginData);

        return $result;
    }


}