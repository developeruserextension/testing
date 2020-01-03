<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Setup\Operation;

use Magento\Framework\Setup\SchemaSetupInterface;

class RenameOldTables
{
    const AMASTY_FILE_OLD = 'amasty_file' . self::PREFIX;
    const AMASTY_FILE_ICON_OLD = 'amasty_file_icon' . self::PREFIX;
    const AMASTY_FILE_STAT_OLD = 'amasty_file_stat' . self::PREFIX;
    const AMASTY_FILE_STORE_OLD = 'amasty_file_store' . self::PREFIX;
    const AMASTY_FILE_CUSTOMER_GROUP_OLD = 'amasty_file_customer_group' . self::PREFIX;

    const OLD_TABLES = [
        'amasty_file',
        'amasty_file_icon',
        'amasty_file_stat',
        'amasty_file_store',
        'amasty_file_customer_group'
    ];

    const PREFIX = '_old';

    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        foreach (self::OLD_TABLES as $oldTable) {
            if (!$connection->isTableExists($setup->getTable($oldTable . self::PREFIX))) {
                $foreignKeys = $connection->getForeignKeys($setup->getTable($oldTable));
                foreach ($foreignKeys as $foreignKey) {
                    $connection->dropForeignKey($setup->getTable($oldTable), $foreignKey['FK_NAME']);
                }
            }
        }

        foreach (self::OLD_TABLES as $oldTable) {
            if (!$connection->isTableExists($setup->getTable($oldTable . self::PREFIX))) {
                $connection->renameTable($setup->getTable($oldTable), $setup->getTable($oldTable . self::PREFIX));
            }
        }
    }
}
