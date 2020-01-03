<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrdersGrid\Plugin;


use Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory;

/**
 * Class ChangeGridCollection
 */
class ChangeGridCollection
{
    /**
     * Change orders grid collection to our own
     *
     * @param CollectionFactory $subject
     * @param $requestName
     * @return array
     */
    public function beforeGetReport(
        CollectionFactory $subject,
        $requestName
    ) {
        if ($requestName === 'sales_order_grid_data_source') {
            $requestName = 'mageworx_sales_order_grid_data_source';
        }

        return [$requestName];
    }
}