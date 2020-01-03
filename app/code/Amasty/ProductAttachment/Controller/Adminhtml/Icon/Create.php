<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Controller\Adminhtml\Icon;

use Amasty\ProductAttachment\Controller\Adminhtml\Icon;

class Create extends Icon
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
