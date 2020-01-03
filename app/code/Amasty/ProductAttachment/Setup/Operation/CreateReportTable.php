<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Setup\Operation;

use Amasty\ProductAttachment\Model\Report\Item;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class CreateReportTable
{
    const TABLE_NAME = 'amasty_file_report';

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
                'Amasty Product Attachment Report Table'
            )->addColumn(
                Item::ITEM_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true
                ],
                'Item Id'
            )->addColumn(
                Item::FILE_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true, 'nullable' => false
                ],
                'File Id'
            )->addColumn(
                Item::STORE_ID,
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true, 'nullable' => true
                ],
                'Store ID'
            )->addColumn(
                Item::CUSTOMER_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true, 'nullable' => true
                ],
                'Customer ID'
            )->addColumn(
                Item::DOWNLOAD_SOURCE,
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true, 'nullable' => true
                ],
                'Download Source'
            )->addColumn(
                Item::PRODUCT_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true, 'nullable' => true
                ],
                'Product Id'
            )->addColumn(
                Item::CATEGORY_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true, 'nullable' => true
                ],
                'Category Id'
            )->addColumn(
                Item::ORDER_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true, 'nullable' => true
                ],
                'Order Id'
            )->addColumn(
                Item::DOWNLOADED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'default' => Table::TIMESTAMP_INIT, 'nullable' => false
                ],
                'Downloaded at'
            );
    }
}
