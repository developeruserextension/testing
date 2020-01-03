<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Controller\Adminhtml\Report;

use Magento\Backend\App\Action;

class Clear extends \Amasty\ProductAttachment\Controller\Adminhtml\Report
{
    /**
     * @var \Amasty\ProductAttachment\Model\Report\ResourceModel\Item
     */
    private $item;

    public function __construct(
        \Amasty\ProductAttachment\Model\Report\ResourceModel\Item $item,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->item = $item;
    }

    public function execute()
    {
        try {
            $this->item->clear();
            $this->messageManager->addSuccessMessage(__('Downloads report has been cleared.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $this->_redirect('*/*/downloads');
    }
}
