<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Setup;

use Amasty\StoreCredit\Api\Data\SalesFieldInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var Operation\CreateStoreCreditTable
     */
    private $createStoreCreditTable;

    /**
     * @var Operation\CreateHistoryTable
     */
    private $createHistoryTable;

    /**
     * @var \Magento\Sales\Setup\SalesSetupFactory
     */
    private $salesSetupFactory;

    /**
     * @var \Magento\Quote\Setup\QuoteSetupFactory
     */
    private $quoteSetupFactory;

    public function __construct(
        Operation\CreateStoreCreditTable $createStoreCreditTable,
        Operation\CreateHistoryTable $createHistoryTable,
        \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory,
        \Magento\Quote\Setup\QuoteSetupFactory $quoteSetupFactory
    ) {

        $this->createStoreCreditTable = $createStoreCreditTable;
        $this->createHistoryTable = $createHistoryTable;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
    }

    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->createStoreCreditTable->execute($setup);
        $this->createHistoryTable->execute($setup);

        /** @var \Magento\Sales\Setup\SalesSetup $salesSetup */
        $salesSetup = $this->salesSetupFactory->create();

        $salesSetup->addAttribute('order', SalesFieldInterface::AMSC_BASE_AMOUNT, ['type' => 'decimal']);
        $salesSetup->addAttribute('order', SalesFieldInterface::AMSC_AMOUNT, ['type' => 'decimal']);
        $salesSetup->addAttribute('order', SalesFieldInterface::AMSC_INVOICED_BASE_AMOUNT, ['type' => 'decimal']);
        $salesSetup->addAttribute('order', SalesFieldInterface::AMSC_INVOICED_AMOUNT, ['type' => 'decimal']);
        $salesSetup->addAttribute('order', SalesFieldInterface::AMSC_REFUNDED_BASE_AMOUNT, ['type' => 'decimal']);
        $salesSetup->addAttribute(
            'order',
            SalesFieldInterface::AMSC_REFUNDED_AMOUNT,
            ['type' => 'decimal', 'grid' => true]
        );

        $salesSetup->addAttribute('invoice', SalesFieldInterface::AMSC_BASE_AMOUNT, ['type' => 'decimal']);
        $salesSetup->addAttribute('invoice', SalesFieldInterface::AMSC_AMOUNT, ['type' => 'decimal']);

        $salesSetup->addAttribute('creditmemo', SalesFieldInterface::AMSC_BASE_AMOUNT, ['type' => 'decimal']);
        $salesSetup->addAttribute('creditmemo', SalesFieldInterface::AMSC_AMOUNT, ['type' => 'decimal']);

        /** @var \Magento\Quote\Setup\QuoteSetup $quoteSetup */
        $quoteSetup = $this->quoteSetupFactory->create();
        $quoteSetup->addAttribute('quote', SalesFieldInterface::AMSC_USE, ['type' => 'boolean']);
        $quoteSetup->addAttribute('quote', SalesFieldInterface::AMSC_BASE_AMOUNT, ['type' => 'decimal']);
        $quoteSetup->addAttribute('quote', SalesFieldInterface::AMSC_AMOUNT, ['type' => 'decimal']);

        $setup->endSetup();
    }
}
