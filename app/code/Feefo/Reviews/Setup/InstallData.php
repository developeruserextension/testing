<?php

namespace Feefo\Reviews\Setup;

use Feefo\Reviews\Api\Feefo\StorageInterface;
use Magento\Framework\App\State;
use Magento\Framework\Math\Random;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Integration\Api\AdminTokenServiceInterface;
use Magento\User\Model\User as UserModel;
use Psr\Log\LoggerInterface;

/**
 * Class InstallData
 */
class InstallData implements InstallDataInterface
{
    /**
     * Admin User Data Constants
     */
    const DATA_USERNAME = 'feefo';

    const DATA_USER_EMAIL = 'technical@feefo.com';

    const DATA_USER_FIRST_NAME = 'Feefo';

    const DATA_USER_LAST_NAME = 'Feefo';

    const DATA_IS_ACTIVE = 1;

    const DATA_IS_INACTIVE = 0;

    const PREFIX_PASSWORD = 'feefo';

    /** @var State */
    protected $appState;

    /** @var \Magento\User\Model\UserFactory  */
    protected $userFactory;

    /** @var AdminTokenServiceInterface  */
    protected $adminTokenService;

    /** @var \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory  */
    protected $roleCollectionFactory;

    /** @var LoggerInterface  */
    protected $logger;

    /** @var Random */
    protected $mathRandom;

    /** @var StorageInterface */
    protected $storage;

    /**
     * InstallData constructor.
     * @param State $appState
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param AdminTokenServiceInterface $adminTokenService
     * @param \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory $roleCollectionFactory
     * @param LoggerInterface $logger
     * @param Random $mathRandom
     * @param StorageInterface $storage
     */
    public function __construct(
        State $appState,
        \Magento\User\Model\UserFactory $userFactory,
        AdminTokenServiceInterface $adminTokenService,
        \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory $roleCollectionFactory,
        LoggerInterface $logger,
        Random $mathRandom,
        StorageInterface $storage
    ) {
        $this->appState = $appState;
        $this->userFactory = $userFactory;
        $this->adminTokenService = $adminTokenService;
        $this->roleCollectionFactory = $roleCollectionFactory;
        $this->logger = $logger;
        $this->mathRandom = $mathRandom;
        $this->storage = $storage;
    }

    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        try {
            /** @var string $password */
            $password = $this->generatePassword(static::PREFIX_PASSWORD);

            /** @var UserModel $userModel */
            $userModel = $this->createAdminUser([
                'username' => static::DATA_USERNAME,
                'email' => static::DATA_USER_EMAIL,
                'firstname' => static::DATA_USER_FIRST_NAME,
                'lastname' => static::DATA_USER_LAST_NAME,
                'password' => $password,
                'is_active' => static::DATA_IS_ACTIVE,
            ]);

            if ($userModel->getId()) {
                $token = $this->createAdminToken($userModel->getUserName(), $password);
                $this->makeUserInactive($userModel);
                $this->logger->debug(__('Feefo token( %s ) has been created', $token));

                $this->storage->setAccessKey($token);
                $this->storage->setUserId($userModel->getId());
            } else {
                $this->logger->error(__('User couldn\'t create'));
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * Get any admin group if if exists
     *
     * @return boolean|string
     */
    protected function getAdminRoleId()
    {
        try {
            /** @var \Magento\Authorization\Model\ResourceModel\Role\Collection $roleCollection */
            $roleCollection = $this->roleCollectionFactory->create();
            $roleCollection
                ->setRolesFilter()
                ->setUserFilter(null, UserContextInterface::USER_TYPE_ADMIN);
            $roleCollection->load();
            if ($roleCollection->getSize() > 0) {
                /** @var \Magento\Authorization\Model\Role $adminRole */
                $adminRole = $roleCollection->getFirstItem();

                return $adminRole->getId();
            }

        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return false;
    }

    /**
     * Check admin user existing
     *
     * @param string $username
     * @return string
     */
    protected function isAdminUserAlreadyExists($username)
    {
        /** @var UserModel $userModel */
        $userModel = $this->userFactory->create();
        $userModel->loadByUsername($username);

        return $userModel->getId();
    }

    /**
     * Create a admin user for integration with Feefo
     *
     * @param $data
     * @return UserModel
     */
    protected function createAdminUser($data)
    {
        if ($this->isAdminUserAlreadyExists($data['username'])) {
            $data['username'] = $this->generateNewAdminUserName($data['username']);
        }

        /** @var UserModel $userModel */
        $userModel = $this->userFactory->create();
        $userModel->setData($data);
        $roleId = $this->getAdminRoleId();
        if ($roleId) {
            $userModel->setRoleId($roleId);
        }
        $userModel->getResource()->save($userModel);

        return $userModel;
    }

    /**
     * Create access token for the $username
     *
     * @param string $username
     * @param string $password
     *
     * @return string
     */
    protected function createAdminToken($username, $password)
    {
        return $this->appState->emulateAreaCode(
            FrontNameResolver::AREA_CODE,
            [$this->adminTokenService, 'createAdminAccessToken'],
            [$username, $password]
        );
    }

    /**
     * Inactivate a admin user
     *
     * @param $userModel UserModel
     * @return void
     */
    protected function makeUserInactive($userModel)
    {
        $userModel->setIsActive(static::DATA_IS_INACTIVE);
        $userModel->getResource()->save($userModel);
    }

    /**
     * Generate password for the feefo user
     *
     * @param string $prefix
     * @return string
     */
    protected function generatePassword($prefix)
    {
        return $this->mathRandom->getUniqueHash($prefix);
    }

    /**
     * Generate username for the feefo user
     *
     * @param string $username
     * @return string
     */
    protected function generateNewAdminUserName($username)
    {
        return uniqid($username);
    }
}