<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templatebrand;

use MageWorx\SeoXTemplates\Controller\Adminhtml\Templatebrand as TemplateController;
use Magento\Backend\App\Action\Context;
use MageWorx\SeoXTemplates\Model\Template\BrandFactory as TemplateBrandFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Create extends TemplateController
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     *
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     * @param TemplateBrandFactory $templateBrandFactory
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        PageFactory $resultPageFactory,
        TemplateBrandFactory $templateBrandFactory,
        Context $context
    ) {

        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($registry, $templateBrandFactory, $context);
    }

    /**
     * Create brand page template
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $templateId = $this->getRequest()->getParam('template_id');
        /** @var \MageWorx\SeoXTemplate\Model\Template\Brand $template */
        $template = $this->initTemplateBrand();
        /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set((__('Create Template')));
        if ($templateId) {
            $template->load($templateId);
            if (!$template->getId()) {
                $this->messageManager->addError(__('The template no longer exists.'));

                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath(
                    'mageworx_seoxtemplates/*/edit',
                    [
                        'template_id' => $template->getId(),
                        '_current'    => true
                    ]
                );

                return $resultRedirect;
            }
        }

        $title = $template->getId() ? $template->getName() : __('New Brand Page Template');
        $data  = $this->_session->getData('mageworx_seoxtemplates_template_data', true);

        $resultPage->getConfig()->getTitle()->append($title);
        if (!empty($data)) {
            $template->setData($data);
        }

        return $resultPage;
    }
}
