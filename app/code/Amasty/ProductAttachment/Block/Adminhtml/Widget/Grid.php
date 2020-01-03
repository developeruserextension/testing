<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Block\Adminhtml\Widget;

use Magento\Backend\Block\Widget\Grid\Extended;
use Amasty\ProductAttachment\Api\Data\FileInterface;

class Grid extends Extended
{
    /**
     * @var \Amasty\ProductAttachment\Model\File\ResourceModel\CollectionFactory
     */
    private $_collectionFactory;

    /**
     * @var array
     */
    private $selectedFiles;

    public function __construct(
        \Amasty\ProductAttachment\Model\File\ResourceModel\CollectionFactory $collectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->_collectionFactory = $collectionFactory;
    }

    public function toHtml()
    {
        return '
            <div class="page-main-actions">
                <div class="page-actions">
                    <div class="page-actions-buttons">
                        <button type="button" data-role="action" onclick="'. $this->getId() .'.close()">
                            <span>Cancel</span>
                        </button>
                        <button class="action-primary" type="button" data-role="action" id="addSelectedFiles">
                            <span>Add Selected Files</span>
                        </button>
                    </div>
                </div>
            </div>' . parent::toHtml();
    }

    /**
     * Block construction, prepare grid params
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('name');
        $this->setUseAjax(true);
    }

    /**
     * @inheritdoc
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_files') {
            $selected = $this->getSelectedFiles();
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.' . FileInterface::FILE_ID, ['in' => $selected]);
            } else {
                $this->getCollection()->addFieldToFilter('main_table.' . FileInterface::FILE_ID, ['nin' => $selected]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        /** @var $collection \Amasty\ProductAttachment\Model\File\ResourceModel\Collection **/
        $collection = $this->_collectionFactory->create();
        $collection->addFileData();
        if (!empty($this->getSelectedFiles())) {
            $collection->addFieldToFilter('main_table.' . FileInterface::FILE_ID, ['nin' => $this->getSelectedFiles()]);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @inheritdoc
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_files',
            [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_files',
                'inline_css' => 'checkbox entities',
                'field_name' => 'in_files',
                'values' => $this->getSelectedFiles(),
                'align' => 'center',
                'index' => FileInterface::FILE_ID,
                'use_index' => true
            ]
        );

        $this->addColumn(
            FileInterface::FILE_ID,
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => FileInterface::FILE_ID,
                'filter_index' => 'main_table.' . FileInterface::FILE_ID,
                'header_css_class' => 'col-' . FileInterface::FILE_ID,
                'column_css_class' => 'col-' . FileInterface::FILE_ID
            ]
        );
        $this->addColumn(
            FileInterface::FILENAME,
            [
                'header' => __('File Name'),
                'name' => 'filename',
                'index' => 'filename',
                'header_css_class' => 'col-' . FileInterface::FILENAME,
                'column_css_class' => 'col-' . FileInterface::FILENAME
            ]
        );
        $this->addColumn(
            FileInterface::LABEL,
            [
                'header' => __('File Label'),
                'name' => 'label',
                'index' => FileInterface::LABEL,
                'header_css_class' => 'col-' . FileInterface::LABEL,
                'column_css_class' => 'col-' . FileInterface::LABEL
            ]
        );

        return parent::_prepareColumns();
    }
    /**
     * @inheritdoc
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'amfile/file_widget/chooser',
            [
                '_current' => true,
                'uniq_id' => $this->getId(),
            ]
        );
    }

    /**
     * @param array $selectedFiles
     *
     * @return $this
     */
    public function setSelectedFiles($selectedFiles)
    {
        $this->selectedFiles = $selectedFiles;
        return $this;
    }

    /**
     * @return array
     */
    public function getSelectedFiles()
    {
        if ($jsonValue = $this->getRequest()->getParam('element_value')) {
            $jsonValue = str_replace('|', '"', $jsonValue);
            $files = json_decode($jsonValue, true);
            $filesOutput = [];
            foreach ($files as $fileId => $order) {
                $filesOutput[] = (int)$fileId;
            }

            $this->setSelectedFiles($filesOutput);
        }
        return $this->selectedFiles;
    }
}
