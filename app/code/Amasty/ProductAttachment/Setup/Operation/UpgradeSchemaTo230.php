<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Setup\Operation;

use Amasty\ProductAttachment\Api\Data\FileInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchemaTo230
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->getConnection()
            ->addColumn(
                $setup->getTable(CreateFileTable::TABLE_NAME),
                FileInterface::URL_HASH,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 32,
                    'default' => '',
                    'nullable' => false,
                    'comment' => 'md5 random hash for url creation'
                ]
            );
        $setup->getConnection()->addIndex(
            $setup->getTable(CreateFileTable::TABLE_NAME),
            $setup->getIdxName(
                $setup->getTable(CreateFileTable::TABLE_NAME),
                FileInterface::URL_HASH,
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            FileInterface::URL_HASH,
            AdapterInterface::INDEX_TYPE_INDEX
        );
    }
}
