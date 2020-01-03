<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Controller\Adminhtml\Report;

class Downloads extends \Amasty\ProductAttachment\Controller\Adminhtml\Report
{
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_ProductAttachment::downloads');
        $resultPage->addBreadcrumb(__('Product Attachment Report'), __('Product Attachment Report'));
        $resultPage->addBreadcrumb(__('Downloads'), __('Downloads'));
        $resultPage->getConfig()->getTitle()->prepend(__('Product Attachment Report Downloads'));

        return $resultPage;
    }
}
