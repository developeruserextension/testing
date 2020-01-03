<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoAll\Block\Adminhtml\Brand;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Framework\DataObject;
use MageWorx\SeoAll\Block\Adminhtml\Brand\BrandGrid\DataProvider;

class BrandGrid extends Extended
{
    /**
     *
     * @var \Magento\Framework\Object
     */
    protected $object;

    /**
     * @var DataProvider
     */
    protected $dataProvider;

    /**
     * BrandGrid constructor.
     *
     * @param DataProvider $dataProvider
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param array $data
     */
    public function __construct(

        DataProvider $dataProvider,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);

        $this->dataProvider = $dataProvider;
        $this->object = new \Magento\Framework\DataObject;
    }
    /**
     * Block construction, prepare grid params
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setUseAjax(true);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param AbstractElement $element Form Element
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $uniqId = $this->mathRandom->getUniqueHash($element->getId());
        $sourceUrl = $this->getUrl(
            'mageworx_seoall/brand_widget/brandChooser',
            ['uniq_id' => $uniqId, 'use_massaction' => false]
        );

        $chooser = $this->getLayout()->createBlock(
            \Magento\Widget\Block\Adminhtml\Widget\Chooser::class
        )->setElement(
            $element
        )->setConfig(
            $this->getConfig()
        )->setFieldsetId(
            $this->getFieldsetId()
        )->setSourceUrl(
            $sourceUrl
        )->setUniqId(
            $uniqId
        );

        if ($element->getValue()) {

            $data = new DataObject();
            $data->setIds($element->getValue());
            $data->setBrandsData([]);
            $this->_eventManager->dispatch(
                'mageworx_brands_get_brands_data',
                ['object' => $data]
            );

            $brandHeaders = $data->getBrandsData();
            if (isset($brandHeaders[$element->getValue()]['header'])) {
                $chooser->setLabel($brandHeaders[$element->getValue()]['header']);
            }
        }

        $element->setData('after_element_html', $chooser->toHtml());

        return $element;
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $this->_eventManager->dispatch(
            'mageworx_seoall_brand_transfer_collection',
            ['object' => $this->object]
        );

        if (isset($this->object['brands'])) {
            $this->setCollection($this->object['brands']);

            $attrIds = [];
            foreach ($this->getCollection() as $item) {
                $attrIds[] = $item->getAttributeId();
            }

            $this->dataProvider->prepareAttributes($attrIds);
        }

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'brand_id',
            [
                'header' => __('Brand ID'),
                'sortable' => true,
                'index' => 'brand_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'name' => 'status',
                'index' => 'status',
                'header_css_class' => 'col-sku',
                'column_css_class' => 'col-sku',
                'type' => 'options',
                'options' => ['1' => __('Enabled'), '0' => __('Disabled')]
            ]
        );

        $this->addColumn(
            'attribute_id',
            [
                'header' => __('Attribute'),
                'name' => 'attribute_id',
                'index' => 'attribute_id',
                'renderer' => \MageWorx\SeoAll\Block\Adminhtml\Brand\BrandGrid\Attribute::class,
                'header_css_class' => 'col-product',
                'column_css_class' => 'col-product'
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

        return parent::_prepareColumns();
    }

    /**
     * Adds additional parameter to URL for loading only products grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'mageworx_seoall/brand_widget/brandChooser',
            [
                'brand_grid' => true,
                '_current' => true,
                'uniq_id' => $this->getId(),
                'use_massaction' => $this->getUseMassaction()
            ]
        );
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        $chooserJsObject = $this->getId();

        return '
                function (grid, event) {
                    var trElement = Event.findElement(event, "tr");
                    var lpId = trElement.down("td").innerHTML;
                    var lpName = trElement.down("td").next().next().innerHTML;
                    var optionLabel = lpName;
                    var optionValue = lpId.replace(/^\s+|\s+$/g,"");
                    ' .
            $chooserJsObject .
            '.setElementValue(optionValue);
                    ' .
            $chooserJsObject .
            '.setElementLabel(optionLabel);
                    ' .
            $chooserJsObject .
            '.close();
                }
            ';
    }
}