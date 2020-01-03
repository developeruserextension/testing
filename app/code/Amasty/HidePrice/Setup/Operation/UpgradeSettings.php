<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Setup\Operation;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Amasty\HidePrice\Model\Source\ReplaceButton;

class UpgradeSettings
{
    /**
     * @param ModuleDataSetupInterface $setup
     * @throws \Zend_Db_Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute(ModuleDataSetupInterface $setup)
    {
        $updateData = [];
        $connection = $setup->getConnection();
        $tableName = $setup->getTable('core_config_data');

        $select = $setup->getConnection()->select()
            ->from($tableName, ['path', 'value', 'scope', 'scope_id'])
            ->where('path = ?', 'amasty_hide_price/information/replace_link');

        $replaceLinks = $connection->fetchAll($select);
        foreach ($replaceLinks as $replaceLink) {
            if (isset($replaceLink['value'])) {
                if ($replaceLink['value'] == 'AmastyHidePricePopup') {
                    $updateData[] = [
                        'value' => ReplaceButton::HIDE_PRICE_POPUP,
                        'path'  => 'amasty_hide_price/information/replace_with',
                        'scope' => $replaceLink['scope'],
                        'scope_id' => $replaceLink['scope_id']
                    ];
                } else {
                    $updateData[] = [
                        'value' => ReplaceButton::REDIRECT_URL,
                        'path'  => 'amasty_hide_price/information/replace_with',
                        'scope' => $replaceLink['scope'],
                        'scope_id' => $replaceLink['scope_id']
                    ];
                    $updateData[] = [
                        'value' => $replaceLink['value'],
                        'path'  => 'amasty_hide_price/information/redirect_link',
                        'scope' => $replaceLink['scope'],
                        'scope_id' => $replaceLink['scope_id']
                    ];
                }
            }
        }

        if (!empty($updateData)) {
            $connection->insertOnDuplicate(
                $tableName,
                $updateData
            );
        }
    }
}
