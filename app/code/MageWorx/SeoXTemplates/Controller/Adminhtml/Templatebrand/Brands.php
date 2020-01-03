<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml\TemplateBrand;

use MageWorx\SeoXTemplates\Controller\Adminhtml\Templatebrand as TemplateController;
use Magento\Framework\Registry;
use MageWorx\SeoXTemplates\Model\Template\BrandFactory as TemplateBrandFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\LayoutFactory;

class Brands extends TemplateController
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     *
     * @param LayoutFactory $resultLayoutFactory
     * @param Registry $registry
     * @param TemplateBrandFactory $templateBrandFactory
     * @param Context $context
     */
    public function __construct(
        LayoutFactory $resultLayoutFactory,
        Registry $registry,
        TemplateBrandFactory $templateBrandFactory,
        Context $context
    ) {

        $this->resultLayoutFactory = $resultLayoutFactory;
        parent::__construct($registry, $templateBrandFactory, $context);
    }

    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $this->initTemplateBrand();
        $resultLayout = $this->resultLayoutFactory->create();
        /** @var \MageWorx\SeoXTemplates\Block\Adminhtml\Template\Brand\Edit\Tab\Brands $brandBlock */
        $brandBlock = $resultLayout->getLayout()->getBlock('template_edit_tab_brand');
        if ($brandBlock) {
            $brandBlock->setTemplateProducts($this->getRequest()->getPost('template_brands', null));
        }

        return $resultLayout;
    }
}
