<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Controller\Adminhtml\Icon;

use Amasty\ProductAttachment\Controller\Adminhtml\Icon;
use Magento\Framework\Controller\ResultFactory;

class Index extends Icon
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_ProductAttachment::icon');
        $resultPage->addBreadcrumb(__('Icon'), __('Icon'));
        $resultPage->addBreadcrumb(__('Icon Management'), __('Icon Management'));
        $resultPage->getConfig()->getTitle()->prepend(__('Icon Management'));

        return $resultPage;
    }
}
