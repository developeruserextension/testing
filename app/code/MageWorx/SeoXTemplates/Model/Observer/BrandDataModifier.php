<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Observer;

use MageWorx\SeoXTemplates\Model\DynamicRenderer\Brand as Renderer;

/**
 * Observer class for brand page template apply proccess
 */

class BrandDataModifier implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageWorx\SeoXTemplates\Model\DynamicRenderer\Brand
     */
    protected $dynamicRenderer;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var  \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * BrandDataModifier constructor.
     * @param Renderer $dynamicRenderer
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Renderer $dynamicRenderer,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Registry $registry
    ) {
        $this->dynamicRenderer = $dynamicRenderer;
        $this->request = $request;
        $this->registry = $registry;
    }

    /**
     * Modify Brand data and meta head
     * Event: layout_generate_blocks_after
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ('mageworx_brands_brand_view' == $this->request->getFullActionName()) {

            $brand = $this->registry->registry('current_brand');
            if (is_object($brand)) {
                $this->dynamicRenderer->modifyBrandTitle($brand);
                $this->dynamicRenderer->modifyBrandMetaDescription($brand);
                $this->dynamicRenderer->modifyBrandMetaKeywords($brand);
            }
        }
    }
}
