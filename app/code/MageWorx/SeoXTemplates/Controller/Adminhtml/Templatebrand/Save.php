<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templatebrand;

use Magento\Framework\Registry;
use MageWorx\SeoXTemplates\Controller\Adminhtml\Templatebrand as TemplateController;
use MageWorx\SeoXTemplates\Model\Template\BrandFactory as TemplateBrandFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\Helper\Js as JsHelper;
use Magento\Backend\App\Action\Context;

class Save extends TemplateController
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

    /**
     *
     * @param DateTime $date
     * @param JsHelper $jsHelper
     * @param Registry $registry
     * @param TemplateBrandFactory $templateBrandFactory
     * @param Context $context
     */
    public function __construct(
        DateTime $date,
        JsHelper $jsHelper,
        Registry $registry,
        TemplateBrandFactory $templateBrandFactory,
        Context $context
    ) {
        $this->date     = $date;
        $this->jsHelper = $jsHelper;
        parent::__construct($registry, $templateBrandFactory, $context);
    }

    /**
     * Save the brand page template
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('template');

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data = $this->filterData($data);

            if (!empty($this->getRequest()->getParam('template')['template_id'])) {
                $template = $this->initTemplateBrand($this->getRequest()->getParam('template')['template_id']);
            } else {
                $template = $this->initTemplateBrand();
            }

            $template->setData($data);
            $brands = $this->getRequest()->getPost('brands', -1);
            if ($brands != -1) {
                $template->setBrandsData($this->jsHelper->decodeGridSerializedInput($brands));
            } else {
                $template->setBrandsData($template->getOrigData('brands_data'));
            }

            $this->_eventManager->dispatch(
                'mageworx_seoxtemplates_template_brand_prepare_save',
                [
                    'template' => $template,
                    'request'  => $this->getRequest()
                ]
            );

            try {
                $template->setDateModified($this->date->gmtDate());
                $template->save();
                $this->_getSession()->setMageworxSeoXTemplatesTemplateBrandData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'mageworx_seoxtemplates/*/edit',
                        [
                            'template_id' => $template->getId(),
                            '_current'    => true
                        ]
                    );

                    return $resultRedirect;
                }
                $this->messageManager->addSuccessMessage(__('The "%1" template has been saved.', $template->getName()));
                $resultRedirect->setPath('mageworx_seoxtemplates/*/');

                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the template.'));
            }

            $this->_getSession()->setMageworxSeoXTemplatesTemplateBrandData($data);
            $resultRedirect->setPath(
                'mageworx_seoxtemplates/*/edit',
                [
                    'template_id' => $template->getId(),
                    '_current'    => true
                ]
            );

            return $resultRedirect;
        }
        $resultRedirect->setPath('mageworx_seoxtemplates/*/');

        return $resultRedirect;
    }
}
