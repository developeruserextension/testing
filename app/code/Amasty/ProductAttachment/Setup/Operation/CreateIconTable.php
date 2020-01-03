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

class CreateIconTable
{
    const TABLE_NAME = 'amasty_file_icon';

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

        return $setup->getConnection()
            ->newTable(
                $table
            )->setComment(
                'Amasty Product Attachment Icon Table'
            )->addColumn(
                IconInterface::ICON_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true
                ],
                'Icon Id'
            )->addColumn(
                IconInterface::FILE_TYPE,
                Table::TYPE_TEXT,
                255,
                [
                    'default' => '', 'nullable' => false
                ],
                'Type of File'
            )->addColumn(
                IconInterface::IMAGE,
                Table::TYPE_TEXT,
                255,
                [
                    'default' => '', 'nullable' => false
                ],
                'Image name'
            )->addColumn(
                IconInterface::IS_ACTIVE,
                Table::TYPE_BOOLEAN,
                null,
                [
                    'nullable' => false, 'default' => '1'
                ],
                'Is Active'
            );
    }
}
