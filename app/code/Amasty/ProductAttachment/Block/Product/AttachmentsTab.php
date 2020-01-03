<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Block\Product;

use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Amasty\ProductAttachment\Model\ConfigProvider;
use Amasty\ProductAttachment\Model\File\FileScope\FileScopeDataProvider;
use Magento\Customer\Model\Session;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class AttachmentsTab extends Template
{
    protected $_template = 'Amasty_ProductAttachment::attachments.phtml';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var FileScopeDataProvider
     */
    private $fileScopeDataProvider;

    /**
     * @var Session
     */
    private $customerSession;

    public function __construct(
        ConfigProvider $configProvider,
        FileScopeDataProvider $fileScopeDataProvider,
        Session $customerSession,
        Registry $registry,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configProvider = $configProvider;
        $this->registry = $registry;
        $this->fileScopeDataProvider = $fileScopeDataProvider;
        $this->customerSession = $customerSession;
        $this->setData('sort_order', $this->configProvider->getBlockSortOrder());
    }

    /**
     * @inheritdoc
     */
    public function toHtml()
    {
        if (!$this->configProvider->isEnabled() || !$this->configProvider->isBlockEnabled()) {
            return '';
        }
        if ($this->configProvider->getBlockCustomerGroups() !== null) {
            if (!in_array(
                $this->customerSession->getCustomerGroupId(),
                explode(',', $this->configProvider->getBlockCustomerGroups())
            )) {
                return '';
            }
        }

        $this->setTitle($this->configProvider->getBlockTitle());

        return parent::toHtml();
    }

    /**
     * @return bool
     */
    public function getBlockTitle()
    {
        return false;
    }

    /**
     * @return \Amasty\ProductAttachment\Api\Data\FileInterface[]|bool
     */
    public function getAttachments()
    {
        if ($product = $this->registry->registry('current_product')) {
            return $this->fileScopeDataProvider->execute(
                [
                    RegistryConstants::PRODUCT => $product->getId(),
                    RegistryConstants::STORE => $this->_storeManager->getStore()->getId(),
                    RegistryConstants::EXTRA_URL_PARAMS => [
                        'product' => (int)$product->getId()
                    ]
                ],
                'frontendProduct'
            );
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isShowIcon()
    {
        return $this->configProvider->isShowIcon();
    }

    /**
     * @return bool
     */
    public function isShowFilesize()
    {
        return $this->configProvider->isShowFilesize();
    }

    /**
     * @return string
     */
    public function getWidgetType()
    {
        return 'tab';
    }
}
