<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Setup\Operation;

use Amasty\StoreCredit\Api\Data\HistoryInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateHistoryTable
{
    const TABLE_NAME = 'amasty_store_credit_history';

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
                'Amasty Store Credit History Table'
            )->addColumn(
                HistoryInterface::HISTORY_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true
                ]
            )->addColumn(
                HistoryInterface::CUSTOMER_HISTORY_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true, 'nullable' => false
                ]
            )->addColumn(
                HistoryInterface::CUSTOMER_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true, 'nullable' => false
                ]
            )->addColumn(
                HistoryInterface::IS_DEDUCT,
                Table::TYPE_BOOLEAN,
                null,
                [
                    'default' => 0, 'nullable' => false
                ]
            )->addColumn(
                HistoryInterface::DIFFERENCE,
                Table::TYPE_DECIMAL,
                '12,4',
                [
                    'unsigned' => true, 'nullable' => false
                ]
            )->addColumn(
                HistoryInterface::STORE_CREDIT_BALANCE,
                Table::TYPE_DECIMAL,
                '12,4',
                [
                    'unsigned' => true, 'nullable' => false
                ]
            )->addColumn(
                HistoryInterface::ACTION,
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true, 'nullable' => false
                ]
            )->addColumn(
                HistoryInterface::ACTION_DATA,
                Table::TYPE_TEXT,
                255,
                [
                    'default' => '[]',
                ]
            )->addColumn(
                HistoryInterface::MESSAGE,
                Table::TYPE_TEXT,
                null,
                [
                    'nullable' => true,
                ]
            )->addColumn(
                HistoryInterface::CREATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false, 'default' => Table::TIMESTAMP_INIT
                ]
            )->addColumn(
                HistoryInterface::STORE_ID,
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true, 'nullable' => false
                ]
            );
        //TODO foreign keys
    }
}
