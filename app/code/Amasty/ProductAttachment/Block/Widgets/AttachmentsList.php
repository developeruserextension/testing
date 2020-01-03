<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Block\Widgets;

use Amasty\ProductAttachment\Api\Data\FileInterface;
use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Amasty\ProductAttachment\Model\File\FileScope\FileScopeDataProvider;
use Amasty\ProductAttachment\Model\SourceOptions\WidgetType;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Amasty\ProductAttachment\Model\ConfigProvider;

class AttachmentsList extends Template implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = "Amasty_ProductAttachment::attachments.phtml";

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var FileScopeDataProvider
     */
    private $fileScopeDataProvider;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        FileScopeDataProvider $fileScopeDataProvider,
        ConfigProvider $configProvider,
        Registry $registry,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configProvider = $configProvider;
        $this->fileScopeDataProvider = $fileScopeDataProvider;
        $this->registry = $registry;
    }

    /**
     * @inheritdoc
     */
    public function toHtml()
    {
        if (!$this->configProvider->isEnabled()) {
            return '';
        }

        return parent::toHtml();
    }

    /**
     * @return bool|string
     */
    public function getBlockTitle()
    {
        if ($this->hasData('block_title')) {
            return $this->getData('block_title');
        }

        return false;
    }

    /**
     * @return bool|FileInterface[]
     */
    public function getAttachments()
    {
        if ($this->hasData('widget_type')) {
            $dataProviderName = false;
            $params = [];
            switch ($this->getData('widget_type')) {
                case WidgetType::CURRENT_CATEGORY:
                    if ($category = $this->registry->registry('current_category')) {
                        $dataProviderName = 'frontendCategory';
                        $params = [
                            RegistryConstants::CATEGORY => (int)$category->getId(),
                            RegistryConstants::STORE => $this->_storeManager->getStore()->getId(),
                            RegistryConstants::EXTRA_URL_PARAMS => [
                                'category' => (int)$category->getId()
                            ]
                        ];
                    }
                    break;
                case WidgetType::SPECIFIC_CATEGORY:
                    if ($this->hasData('category')) {
                        $dataProviderName = 'frontendCategory';
                        $categoryId = (int)str_replace('category/', '', $this->getData('category'));
                        $params = [
                            RegistryConstants::CATEGORY => $categoryId,
                            RegistryConstants::STORE => $this->_storeManager->getStore()->getId(),
                            RegistryConstants::EXTRA_URL_PARAMS => [
                                'category' => $categoryId
                            ]
                        ];
                    }
                    break;
                case WidgetType::CURRENT_PRODUCT:
                    if ($product = $this->registry->registry('current_product')) {
                        $dataProviderName = 'frontendProduct';
                        $params = [
                            RegistryConstants::PRODUCT => (int)$product->getId(),
                            RegistryConstants::STORE => $this->_storeManager->getStore()->getId(),
                            RegistryConstants::EXTRA_URL_PARAMS => [
                                'product' => (int)$product->getId()
                            ]
                        ];
                    }
                    break;
                case WidgetType::SPECIFIC_PRODUCT:
                    if ($this->hasData('product')) {
                        $dataProviderName = 'frontendProduct';
                        $productId = (int)str_replace('product/', '', $this->getData('product'));
                        $params = [
                            RegistryConstants::PRODUCT => $productId,
                            RegistryConstants::STORE => $this->_storeManager->getStore()->getId(),
                            RegistryConstants::EXTRA_URL_PARAMS => [
                                'product' => $productId
                            ]
                        ];
                    }
                    break;
                case WidgetType::CUSTOM_FILES:
                    if ($this->hasData('files')) {
                        /** @codingStandardsIgnoreStart */
                        $items = json_decode(str_replace('|', '"', html_entity_decode($this->getData('files'))), true);
                        /** @codingStandardsIgnoreEnd */
                        if (!empty($items)) {
                            $dataProviderName = 'fileIds';
                            $params = [
                                RegistryConstants::FILE_IDS => array_keys($items),
                                RegistryConstants::FILE_IDS_ORDER => $items,
                                RegistryConstants::STORE => $this->_storeManager->getStore()->getId()
                            ];
                        }
                    }
                    break;
            }

            if ($dataProviderName) {
                return $this->fileScopeDataProvider->execute($params, $dataProviderName);
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isShowIcon()
    {
        return $this->hasData('show_icon') && $this->getData('show_icon');
    }

    /**
     * @return bool
     */
    public function isShowFilesize()
    {
        return $this->hasData('show_filesize') && $this->getData('show_filesize');
    }

    /**
     * @return int
     */
    public function getWidgetType()
    {
        return $this->getData('widget_type');
    }
}
