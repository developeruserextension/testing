<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Plugin;

use Amasty\ProductAttachment\Model\ConfigProvider;
use Magento\Catalog\Block\Product\View\Description;

/**
 * Class TabPosition used to add tab to the specified position on the product page
 */
class TabPosition
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    /**
     * @param Description $block
     * @param $result
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterGetGroupChildNames(Description $block, $result)
    {
        if (!$this->configProvider->isEnabled() || !$this->configProvider->isBlockEnabled()) {
            return $result;
        }

        $layout = $block->getLayout();
        $childNamesSortOrder = [];
        $defaultSortOrder = 0;

        foreach ($result as $childName) {
            $alias = $layout->getElementAlias($childName);
            $sortOrder = (int)$block->getChildData($alias, 'sort_order');

            if (!$sortOrder) {
                $defaultSortOrder += 10;
            }

            $nextTabPositionValue = $this->getNextTabPositionValue(
                $sortOrder ? : $defaultSortOrder,
                $childNamesSortOrder
            );
            $childNamesSortOrder[$nextTabPositionValue] = $childName;
        }

        ksort($childNamesSortOrder, SORT_NUMERIC);

        return $childNamesSortOrder;
    }

    /**
     * @param int $value
     * @param array $childNamesSortOrder
     * @return int
     */
    private function getNextTabPositionValue($value, $childNamesSortOrder)
    {
        while (isset($childNamesSortOrder[$value])) {
            $value++;
        }

        return $value;
    }
}
