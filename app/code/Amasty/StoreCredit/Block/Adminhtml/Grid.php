<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Block\Adminhtml;

class Grid extends \Magento\Backend\Block\Template
{
    public function toHtml()
    {
        return $this->getChildHtml('amstorecredit-history');
    }
}
