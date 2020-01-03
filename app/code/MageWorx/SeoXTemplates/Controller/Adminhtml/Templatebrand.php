<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use MageWorx\SeoXTemplates\Model\Template\BrandFactory as TemplateBrandFactory;
use Magento\Framework\Registry;

abstract class Templatebrand extends Action
{
    /**
     * Template Brand factory
     *
     * @var TemplateBrandFactory
     */
    protected $templateBrandFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Templatebrand constructor.
     *
     * @param Registry $registry
     * @param TemplateBrandFactory $templateBrandFactory
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        TemplateBrandFactory $templateBrandFactory,
        Context $context
    ) {

        $this->coreRegistry         = $registry;
        $this->templateBrandFactory = $templateBrandFactory;
        parent::__construct($context);
    }

    /**
     * @param null $forceTemplateId
     * @return \MageWorx\SeoXTemplates\Model\Template\Brand
     */
    protected function initTemplateBrand($forceTemplateId = null)
    {
        $templateId = is_null($forceTemplateId) ? $this->getRequest()->getParam('template_id') : $forceTemplateId;

        /** @var \MageWorx\SeoXTemplates\Model\Template\Brand $template */
        $template = $this->templateBrandFactory->create();
        if ($templateId) {
            $template->load($templateId);
        } else {
            $template->setStoreId($this->getTemplateStoreId());
            $template->setTypeId($this->getTemplateTypeId());
        }

        if (is_null($forceTemplateId)) {
            $this->coreRegistry->register('mageworx_seoxtemplates_template', $template);
        }

        return $template;
    }

    /**
     *
     * @return int|null
     */
    protected function getTemplateStoreId()
    {
        $storeId = $this->getRequest()->getParam('store_id', -1);

        if ($storeId != -1) {
            return $storeId;
        }

        return null;
    }

    /**
     *
     * @return int|null
     */
    protected function getTemplateTypeId()
    {
        $typeId = $this->getRequest()->getParam('type_id', -1);

        if ($typeId != -1) {
            return $typeId;
        }

        return null;
    }

    /**
     *
     * @param array $data
     * @return array
     */
    protected function filterData($data)
    {
        if ($data['store_id'] == 'default') {
            $data['store_id']              = 0;
            $data['use_for_default_value'] = true;
        }

        return $data;
    }

    /**
     * Is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_SeoXTemplates::templatebrand');
    }
}
