<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templatebrand;

use MageWorx\SeoXTemplates\Controller\Adminhtml\Templatebrand;

class Delete extends Templatebrand
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id             = $this->getRequest()->getParam('template_id');
        if ($id) {
            $name = "";
            try {
                /** @var \MageWorx\SeoXTemplates\Model\Template\Brand $template */
                $template = $this->templateBrandFactory->create();
                $template->load($id);
                $name = $template->getName();
                $template->delete();
                $this->messageManager->addSuccess(__('The "%1" template has been deleted.', $name));
                $this->_eventManager->dispatch(
                    'adminhtml_mageworx_seoxtemplates_template_brand_on_delete',
                    ['name' => $name, 'status' => 'success']
                );
                $resultRedirect->setPath('mageworx_seoxtemplates/*/');

                return $resultRedirect;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_mageworx_seoxtemplates_template_brand_on_delete',
                    ['name' => $name, 'status' => 'fail']
                );
                $this->messageManager->addError($e->getMessage());
                $resultRedirect->setPath('mageworx_seoxtemplates/*/edit', ['template_id' => $id]);

                return $resultRedirect;
            }
        }
        $this->messageManager->addError(__('We can\'t find a brand page template to delete.'));
        $resultRedirect->setPath('mageworx_seoxtemplates/*/');

        return $resultRedirect;
    }
}
