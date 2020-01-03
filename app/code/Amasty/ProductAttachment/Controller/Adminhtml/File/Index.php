<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Controller\Adminhtml\File;

use Amasty\ProductAttachment\Controller\Adminhtml\File;
use Magento\Framework\Controller\ResultFactory;

class Index extends File
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_ProductAttachment::files_list');
        $resultPage->addBreadcrumb(__('File'), __('File'));
        $resultPage->addBreadcrumb(__('Attachments Management'), __('Attachments Management'));
        $resultPage->getConfig()->getTitle()->prepend(__('Attachments Management'));

        return $resultPage;
    }
}
