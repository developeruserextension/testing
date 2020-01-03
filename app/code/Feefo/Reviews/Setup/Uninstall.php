<?php

namespace Feefo\Reviews\Setup;

use Exception;
use Feefo\Reviews\Service\UninstallPluginService;
use Feefo\Reviews\Setup\InstallData as FeefoInstallData;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Db\Select;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface as UninstallInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Uninstall
 */
class Uninstall implements UninstallInterface
{
    /**
     * Core Config Data Table Name
     */
    const CORE_CONFIG_TABLE_NAME = 'core_config_data';

    /**
     * Admin User Table Name
     */
    const ADMIN_USER_TABLE_NAME = 'admin_user';

    /**
     * Logger
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Uninstall Plugin Service
     *
     * @var UninstallPluginService
     */
    protected $uninstallPluginService;

    /**
     * Config paths to be removed
     *
     * @var array
     */
    protected $configPaths = [
        'feefo/service/access_key',
        'feefo/service/user_id',
        'feefo/general/website_url',
        'feefo/service/plugin_id',
        'feefo/widget/override_product_listing_template',
        'feefo/widget/settings',
        'feefo/widget/snippets',
        'feefo/general/sore_ids',
    ];

    /**
     * Uninstall constructor
     *
     * @param LoggerInterface $logger
     * @param UninstallPluginService $uninstallPluginService
     */
    public function __construct(
        LoggerInterface $logger,
        UninstallPluginService $uninstallPluginService
    ) {
        $this->uninstallPluginService = $uninstallPluginService;
        $this->logger = $logger;
    }

    /**
     * Invoked when remove-data flag is set during module uninstall.
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $feefoAdminUserEmail = FeefoInstallData::DATA_USER_EMAIL;

        /** @var AdapterInterface $connection */
        $connection = $installer->getConnection();

        try {
            /* Uninstall plugins */
            $this->uninstallPluginService->execute();

            /** @var Select $selectConfig */
            $selectConfig = $connection->select();
            $selectConfig->from(self::CORE_CONFIG_TABLE_NAME);
            $selectConfig->where('path IN (?)', $this->configPaths);

            /** @var string $deleteConfigQuery */
            $deleteConfigQuery = $connection->deleteFromSelect(
                $selectConfig,
                self::CORE_CONFIG_TABLE_NAME
            );
            $connection->query($deleteConfigQuery);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }

        try {
            /** @var Select $selecUser */
            $selectUser = $connection->select();
            $selectUser->from(self::ADMIN_USER_TABLE_NAME);
            $selectUser->where('email = ?', $feefoAdminUserEmail);

            /** @var string $deleteUserQuery */
            $deleteUserQuery = $connection->deleteFromSelect(
                $selectUser,
                self::ADMIN_USER_TABLE_NAME
            );
            $connection->query($deleteUserQuery);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }

        $installer->endSetup();
    }
}