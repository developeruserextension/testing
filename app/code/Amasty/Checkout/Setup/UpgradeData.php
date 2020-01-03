<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Checkout
 */


namespace Amasty\Checkout\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\State;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Backend\App\Area\FrontNameResolver;

/**
 * Class UpgradeData
 *
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var Operation\UpgradeDataTo203
     */
    private $upgradeDataTo203;

    /**
     * @var State
     */
    private $appState;

    public function __construct(
        Operation\UpgradeDataTo203 $upgradeDataTo203,
        State $appState
    ) {
        $this->upgradeDataTo203 = $upgradeDataTo203;
        $this->appState = $appState;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->appState->emulateAreaCode(
            FrontNameResolver::AREA_CODE,
            [$this, 'upgradeDataWithEmulationAreaCode'],
            [$setup, $context]
        );
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgradeDataWithEmulationAreaCode(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.3', '<')) {
            $this->upgradeDataTo203->execute();
        }

        $setup->endSetup();
    }
}
