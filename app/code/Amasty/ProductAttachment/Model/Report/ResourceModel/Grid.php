<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Report\ResourceModel;

use Amasty\ProductAttachment\Api\Data\FileInterface;
use Amasty\ProductAttachment\Api\Data\FileScopeInterface;
use Amasty\ProductAttachment\Setup\Operation\CreateFileScopeTables;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

class Grid extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = \Amasty\ProductAttachment\Setup\Operation\CreateReportTable::TABLE_NAME,
        $resourceModel = \Amasty\ProductAttachment\Model\Report\ResourceModel\Item::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
        $this->join(
            CreateFileScopeTables::FILE_STORE_TABLE_NAME,
            'main_table.' . FileInterface::FILE_ID .
            ' = ' . CreateFileScopeTables::FILE_STORE_TABLE_NAME . '.' . FileScopeInterface::FILE_ID,
            [
                FileScopeInterface::LABEL,
                FileScopeInterface::FILENAME,
                FileScopeInterface::INCLUDE_IN_ORDER,
                FileScopeInterface::IS_VISIBLE,
            ]
        );
        $this->getSelect()->where(
            CreateFileScopeTables::FILE_STORE_TABLE_NAME . '.' . FileScopeInterface::STORE_ID . ' = 0'
        );
    }
}
