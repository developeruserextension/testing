<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Controller\Adminhtml\Import;

use Amasty\ProductAttachment\Block\Adminhtml\Steps;
use Amasty\ProductAttachment\Controller\Adminhtml\Import;

class FileImport extends Import
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_ProductAttachment::import');
        $resultPage->getConfig()->getTitle()->prepend(__('Import Attachments'));
        $resultPage->addBreadcrumb(__('Import Attachments'), __('Import Attachments'));

        /** @var Steps $steps */
        $steps = $resultPage->getLayout()->getBlock('import-steps');
        $steps->setCurrentStep(Steps::STEP3)
            ->setBackLink($this->getUrl(
                'amfile/import/store',
                ['import_id' => $this->getRequest()->getParam('import_id')]
            ));

        return $resultPage;
    }
}
