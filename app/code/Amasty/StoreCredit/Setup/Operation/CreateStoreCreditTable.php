<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Setup\Operation;

use Amasty\StoreCredit\Api\Data\StoreCreditInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateStoreCreditTable
{
    const TABLE_NAME = 'amasty_store_credit';

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
                'Amasty Store Credit Table'
            )->addColumn(
                StoreCreditInterface::STORE_CREDIT_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true
                ]
            )->addColumn(
                StoreCreditInterface::CUSTOMER_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true, 'nullable' => false
                ]
            )->addColumn(
                StoreCreditInterface::STORE_CREDIT,
                Table::TYPE_DECIMAL,
                '12,4',
                [
                    'unsigned' => true, 'nullable' => false
                ]
            );
        //TODO foreign keys
    }
}
