<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Plugin\Block\Sales\Adminhtml\Order\View\Items;

use Magento\Backend\Block\Template;

/**
 * Class DefaultRenderer
 */
class DefaultRenderer
{
    /**
     * @param Template $originalBlock
     * @param array $after
     * @return array
     */
    public function afterGetColumns(Template $originalBlock, array $after): array
    {
        $after = ['thumbnail' => "col-thumbnail"] + $after;

        return $after;
    }
}
