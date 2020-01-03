<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Setup\Operation;

use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeSelector
{
    const FORM_SELECTOR = 'form[data-role="tocart-form"],';
    const NEW_FORM_SELECTOR = 'form[data-role="tocart-form"] button,';

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
            ->where('path = ?', 'amasty_hide_price/developer/addtocart');

        $replaceLinks = $connection->fetchAll($select);
        foreach ($replaceLinks as $replaceLink) {
            if (isset($replaceLink['value'])) {
                if (strpos($replaceLink['value'], self::FORM_SELECTOR) !== false) {
                    $value = str_replace(self::FORM_SELECTOR, self::NEW_FORM_SELECTOR, $replaceLink['value']);
                    $updateData[] = [
                        'value' => $value,
                        'path'  => $replaceLink['path'],
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
