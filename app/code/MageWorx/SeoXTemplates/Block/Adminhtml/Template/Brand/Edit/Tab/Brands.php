<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Block\Adminhtml\Template\Brand\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Extended as ExtendedGrid;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status as Status;
use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as DataHelper;


class Brands extends ExtendedGrid implements TabInterface
{
    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $status;

    /**
     * @var  \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Brands constructor.
     *
     * @param Status $status
     * @param Registry $registry
     * @param Context $context
     * @param DataHelper $backendHelper
     * @param array $data
     */
    public function __construct(
        Status $status,
        Registry $registry,
        Context $context,
        DataHelper $backendHelper,
        array $data = []
    ) {
        $this->status = $status;
        $this->registry = $registry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('brand_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        if ($this->getBrandTemplate()->getId()) {
            $this->setDefaultFilter(['in_brands' => 1]);
        }
    }

    /**
     * @return bool
     */
    public function isAjax()
    {
        return $this->_request->isXmlHttpRequest() || $this->_request->getParam('isAjax');
    }

    /**
     * Prepare the collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->getBrandTemplate()->getBrands();

        $excludeBrandsIds = $this->getBrandTemplate()->getBrandIdsAssignedForAnalogTemplate();
        if (!empty($excludeBrandsIds)) {
            $collection->getSelect()->where('main_table.brand_id NOT IN (?)', $excludeBrandsIds);
        }

        $this->setCollection($collection);
        parent::_prepareCollection();

        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_brands',
            [
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'in_brands',
                'values'            => $this->_getSelectedBrands(),
                'align'             => 'center',
                'index'             => 'brand_id'
            ]
        );
        $this->addColumn(
            'brand_id',
            [
                'header'           => __('Brand ID'),
                'sortable'         => true,
                'index'            => 'brand_id',
                'type'             => 'number',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'option_id',
            [
                'header' => __('Brand'),
                'name' => 'option_id',
                'index' => 'option_id',
                'renderer' => \MageWorx\SeoAll\Block\Adminhtml\Brand\BrandGrid\AttributeValue::class,
                'header_css_class' => 'col-product',
                'column_css_class' => 'col-product'
            ]
        );

        $this->addColumn(
            'status',
            [
                'header'           => __('Status'),
                'index'            => 'status',
                'type'             => 'options',
                'options'          => $this->status->getOptionArray(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            ]
        );

        return $this;
    }

    /**
     * Retrieve selected brands
     *
     * @return array
     */
    protected function _getSelectedBrands()
    {
        $selected = $this->getBrandTemplate()->getBrandsData();
        return $selected;
    }

    /**
     * Retrieve selected brands
     *
     * @return array
     */
    public function getSelectedBrands()
    {
        $selected = $this->getBrandTemplate()->getBrandsData();

        if (!is_array($selected)) {
            $selected = [];
        }
        return $selected;
    }
    /**
     * @param \Magento\Framework\Object $item
     * @return string
     */
    public function getRowUrl($item)
    {
        return '#';
    }
    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            '*/*/brandsGrid',
            [
                'template_id' => $this->getBrandTemplate()->getId(),
                'store_id'    => $this->getBrandTemplate()->getStoreId(),
                'type_id'     => $this->getBrandTemplate()->getTypeId()
            ]
        );
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_brands') {
            $brandsIds = $this->_getSelectedBrands();
            if (empty($brandsIds)) {
                $brandsIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('brand_id', ['in' => $brandsIds]);
            } else {
                if ($brandsIds) {
                    $this->getCollection()->addFieldToFilter('brand_id', ['nin' => $brandsIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return __('Brands');
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return !($this->getBrandTemplate()->isAssignForIndividualItems($this->getBrandTemplate()->getAssignType()));
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('mageworx_seoxtemplates/templatebrand/brands', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }

    /**
     * Retrieve store id from request
     *
     * @return int
     */
    protected function getTemplateStoreId()
    {
        $templateParams = $this->getRequest()->getParam('template');

        if ($templateParams && array_key_exists('store_id', $templateParams) && $templateParams['store_id'] !== '') {
            return $templateParams['store_id'];
        }

        return $this->getBrandTemplate()->getStoreId();
    }

    /**
     * Retrieve type id from request
     *
     * @return int
     */
    protected function getTemplateTypeId()
    {
        $templateParams = $this->getRequest()->getParam('template');

        if ($templateParams && array_key_exists('type_id', $templateParams) && $templateParams['type_id'] !== '') {
            return $templateParams['type_id'];
        }

        return $this->getBrandTemplate()->getTypeId();
    }

    /**
     *
     * @return \MageWorx\SeoXTemplates\Model\Template
     */
    protected function getBrandTemplate()
    {
        return $this->registry->registry('mageworx_seoxtemplates_template');
    }
}
