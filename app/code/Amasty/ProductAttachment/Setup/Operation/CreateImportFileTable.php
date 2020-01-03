<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Setup\Operation;

use Amasty\ProductAttachment\Api\Data\FileInterface;
use Amasty\ProductAttachment\Api\Data\FileScopeInterface;
use Amasty\ProductAttachment\Model\Import\Import;
use Amasty\ProductAttachment\Model\Import\ImportFile;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class CreateImportFileTable
{
    const TABLE_NAME = 'amasty_file_import_file';

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
        $importTable = $setup->getTable(CreateImportTable::TABLE_NAME);
        return $setup->getConnection()
            ->newTable(
                $table
            )->setComment(
                'Amasty Product Attachment Import File Table'
            )->addColumn(
                ImportFile::IMPORT_FILE_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true
                ]
            )->addColumn(
                ImportFile::IMPORT_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true, 'nullable' => false
                ]
            )->addColumn(
                FileInterface::FILE_PATH,
                Table::TYPE_TEXT,
                255,
                [
                    'default' => null
                ]
            )->addColumn(
                FileScopeInterface::FILENAME,
                Table::TYPE_TEXT,
                255,
                [
                    'default' => null
                ]
            )->addColumn(
                FileScopeInterface::LABEL,
                Table::TYPE_TEXT,
                255,
                [
                    'default' => null
                ]
            )->addColumn(
                FileScopeInterface::IS_VISIBLE,
                Table::TYPE_BOOLEAN,
                null,
                [
                    'default' => null, 'nullable' => true,
                ]
            )->addColumn(
                FileScopeInterface::CUSTOMER_GROUPS,
                Table::TYPE_TEXT,
                255,
                [
                    'default' => null, 'nullable' => true,
                ]
            )->addColumn(
                FileScopeInterface::INCLUDE_IN_ORDER,
                Table::TYPE_BOOLEAN,
                null,
                [
                    'default' => null, 'nullable' => true,
                ]
            )->addForeignKey(
                $setup->getFkName(
                    $table,
                    ImportFile::IMPORT_ID,
                    $importTable,
                    Import::IMPORT_ID
                ),
                ImportFile::IMPORT_ID,
                $importTable,
                Import::IMPORT_ID,
                Table::ACTION_CASCADE
            );
    }
}
