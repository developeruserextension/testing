<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoAll\Block\Adminhtml\Brand\BrandGrid;

/**
 * Class Attribute
 */
class AttributeValue extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * @param \Magento\Framework\DataObject $row
     * @return mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        parent::render($row);

        return $row->getOptionLabel();
    }
}
