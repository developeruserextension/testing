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

class File extends Import
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_ProductAttachment::import');
        $resultPage->getConfig()->getTitle()->prepend(__('Upload Your Files'));
        /** @var Steps $steps */
        $steps = $resultPage->getLayout()->getBlock('import-steps');
        $steps->setCurrentStep(Steps::STEP1)
            ->setNextLink($this->getUrl('amfile/import/save'));

        return $resultPage;
    }
}
