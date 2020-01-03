<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Block\Adminhtml\Widget;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Amasty\ProductAttachment\Api\Data\FileInterface;

class Chooser extends \Magento\Backend\Block\Widget
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $collectionFactory;

    public function __construct(
        \Amasty\ProductAttachment\Model\File\ResourceModel\CollectionFactory $collectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @param $string
     *
     * @return bool
     */
    public function isJson($string)
    {
        @json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
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
        /** @var FileRows $filesRows */
        $filesRows = $this->getLayout()->createBlock(FileRows::class)->setUniqId($uniqId);
        $filesRows->setFieldsetId($this->getFieldsetId());

        /** @var \Amasty\ProductAttachment\Block\Adminhtml\Widget\Button $chooser */
        $chooser = $this->getLayout()->createBlock(
            \Amasty\ProductAttachment\Block\Adminhtml\Widget\Button::class
        )->setElement(
            $element
        )->setConfig(
            $this->getConfig()
        )->setFieldsetId(
            $this->getFieldsetId()
        )->setUniqId(
            $uniqId
        )->setSourceUrl(
            $this->getUrl('amfile/file_widget/chooser', ['uniq_id' => $uniqId, 'use_massaction' => true])
        )->setLabel(
            ' '
        );

        $fileIds = [];
        $hasValue = false;
        if (!empty($element->getValue())) {
            $value = str_replace('|', '"', $element->getValue());
            if ($this->isJson($value) && $files = json_decode($value, true)) {
                $hasValue = true;
            }
        }
        if ($hasValue) {
            /** @var \Amasty\ProductAttachment\Model\File\ResourceModel\Collection $collection */
            $collection = $this->collectionFactory->create()->addFileData();
            $collection->addFieldToFilter('main_table.' . FileInterface::FILE_ID, array_keys($files));
            $filesOutput = [];
            foreach ($collection->getData() as $file) {
                $fileIds[] = $file[FileInterface::FILE_ID];
                $file['order'] = isset($files[(int)$file[FileInterface::FILE_ID]])
                    ? $files[(int)$file[FileInterface::FILE_ID]]
                    : 0;
                $filesOutput[$file[FileInterface::FILE_ID]] = $file;
            }
            usort($filesOutput, function ($file1, $file2) {
                if ($file1['order'] > $file2['order']) {
                    return 1;
                } elseif ($file1['order'] < $file2['order']) {
                    return -1;
                }

                return 0;
            });
            $filesRows->setFiles($filesOutput);
        }

        $element->setData('after_element_html', $filesRows->toHtml() . $chooser->toHtml());
        return $element;
    }
}
