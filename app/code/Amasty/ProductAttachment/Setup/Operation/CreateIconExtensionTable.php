<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Setup\Operation;

use Amasty\ProductAttachment\Api\Data\IconInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class CreateIconExtensionTable
{
    const TABLE_NAME = 'amasty_file_icon_extension';

    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->createTable(
            $this->createTable($setup)
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     *
     * @return Table
     */
    private function createTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable(self::TABLE_NAME);
        $iconTable = $setup->getTable(CreateIconTable::TABLE_NAME);

        return $setup->getConnection()
            ->newTable(
                $table
            )->setComment(
                'Amasty Product Attachment Icon Extensions Table'
            )->addColumn(
                IconInterface::ICON_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true, 'nullable' => false
                ],
                'Icon Id'
            )->addColumn(
                IconInterface::EXTENSION,
                Table::TYPE_TEXT,
                255,
                [
                    'default' => '', 'nullable' => false
                ],
                'Extension'
            )->addIndex(
                $setup->getIdxName(
                    $table,
                    IconInterface::EXTENSION,
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                IconInterface::EXTENSION
            )->addForeignKey(
                $setup->getFkName(
                    $table,
                    IconInterface::ICON_ID,
                    $iconTable,
                    IconInterface::ICON_ID
                ),
                IconInterface::ICON_ID,
                $iconTable,
                IconInterface::ICON_ID,
                Table::ACTION_CASCADE
            );
    }
}
