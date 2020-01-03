<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrdersGrid\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use MageWorx\OrdersGrid\Helper\Data as Helper;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->modifyColumnTable($setup);
        $this->addColumnDiscountAmountTable($setup);
        $this->addIndexColumnDiscountAmount($setup);
        $this->addColumnProductQuantityTable($setup);

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    protected function modifyColumnTable(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->modifyColumn(
            $setup->getTable(Helper::TABLE_NAME_EXTENDED_GRID),
            'applied_tax_percent',
            [
                'nullable' => true,
                'length'   => '12,4',
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'comment'  => 'Applied Tax Percent'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    protected function addColumnDiscountAmountTable(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $connection->addColumn(
            $setup->getTable(Helper::TABLE_NAME_EXTENDED_GRID),
            'discount_amount',
            [
                'type'     => Table::TYPE_DECIMAL,
                'length'   => '12,4',
                'nullable' => true,
                'comment'  => 'Discount Amount'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    protected function addIndexColumnDiscountAmount(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addIndex(
            $setup->getTable(Helper::TABLE_NAME_EXTENDED_GRID),
            $setup->getIdxName(
                Helper::TABLE_NAME_EXTENDED_GRID,
                'discount_amount',
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            'discount_amount',
            AdapterInterface::INDEX_TYPE_INDEX
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    protected function addColumnProductQuantityTable(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $connection->addColumn(
            $setup->getTable(Helper::TABLE_NAME_EXTENDED_GRID),
            'product_quantity',
            [
                'type'    => Table::TYPE_TEXT,
                'length'  => '32k',
                'comment' => 'Product Quantity'
            ]
        );
    }
}