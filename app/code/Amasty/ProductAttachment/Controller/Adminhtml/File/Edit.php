<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Controller\Adminhtml\File;

use Amasty\ProductAttachment\Controller\Adminhtml\File;
use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Amasty\ProductAttachment\Model\File\FileFactory;
use Amasty\ProductAttachment\Model\File\Repository;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;

class Edit extends File
{
    /**
     * @var FileFactory
     */
    private $iconFactory;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        FileFactory $iconFactory,
        Repository $repository,
        Registry $registry,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->iconFactory = $iconFactory;
        $this->repository = $repository;
        $this->registry = $registry;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        //TODO check ACL
        $resultPage->setActiveMenu('Amasty_ProductAttachment::files_list');

        if ($fileId = (int) $this->getRequest()->getParam(RegistryConstants::FORM_FILE_ID)) {
            try {
                $this->repository->getById($fileId);
                $resultPage->getConfig()->getTitle()->prepend(__('Edit Attachment'));
                $resultPage->getLayout()->addBlock(
                    \Magento\Backend\Block\Store\Switcher::class,
                    'store_switcher',
                    'page.main.actions'
                );
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This attachment no longer exists.'));

                return $this->_redirect('*/*/index');
            }
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Attachment'));
        }

        return $resultPage;
    }
}
