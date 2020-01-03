<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Setup;

use Amasty\HidePrice\Model\Source\Group;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var Operation\UpgradeSettings
     */
    private $upgradeSettings;

    /**
     * @var Operation\UpgradeSelector
     */
    private $upgradeSelector;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        Operation\UpgradeSettings $upgradeSettings,
        Operation\UpgradeSelector $upgradeSelector
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->upgradeSettings = $upgradeSettings;
        $this->upgradeSelector = $upgradeSelector;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {

        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->updateAttribute(
                'catalog_product',
                'am_hide_price_customer_gr',
                'source_model',
                Group::class
            );
        }

        /* set used on product listing to TRUE for correct work on category pages*/
        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->updateAttribute(
                'catalog_product',
                'am_hide_price_mode',
                'used_in_product_listing',
                true
            );

            $eavSetup->updateAttribute(
                'catalog_product',
                'am_hide_price_customer_gr',
                'used_in_product_listing',
                true
            );
        }

        if (version_compare($context->getVersion(), '1.5.0', '<')) {
            $this->upgradeSettings->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.5.5', '<')) {
            $this->upgradeSelector->execute($setup);
        }

        $setup->endSetup();
    }
}
