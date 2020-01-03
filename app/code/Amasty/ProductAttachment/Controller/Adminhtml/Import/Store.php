<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Controller\Adminhtml\Import;

use Amasty\ProductAttachment\Block\Adminhtml\Steps;
use Amasty\ProductAttachment\Controller\Adminhtml\Import;
use Magento\Framework\Controller\ResultFactory;

class Store extends Import
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_ProductAttachment::import');
        $resultPage->getConfig()->getTitle()->prepend(__('Select Stores For Configuration'));
        /** @var Steps $steps */
        $steps = $resultPage->getLayout()->getBlock('import-steps');
        $steps->setCurrentStep(Steps::STEP2)
            ->setNextLink($this->getUrl('amfile/import/save'))
            ->setBackLink($this->getUrl(
                'amfile/import/file',
                ['import_id' => $this->getRequest()->getParam('import_id')]
            ));

        return $resultPage;
    }
}
