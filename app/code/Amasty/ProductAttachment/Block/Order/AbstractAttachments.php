<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Block\Order;

use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Amasty\ProductAttachment\Model\ConfigProvider;
use Amasty\ProductAttachment\Model\File\FileScope\FileScopeDataProvider;
use Magento\Framework\View\Element\Template;

abstract class AbstractAttachments extends Template
{
    /**
     * @var int
     */
    protected $productId;

    /**
     * @var int
     */
    protected $orderId;

    /**
     * @var int
     */
    protected $storeId;

    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * @var FileScopeDataProvider
     */
    protected $fileScopeDataProvider;

    public function __construct(
        ConfigProvider $configProvider,
        FileScopeDataProvider $fileScopeDataProvider,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configProvider = $configProvider;
        $this->fileScopeDataProvider = $fileScopeDataProvider;
    }

    /**
     * @inheritdoc
     */
    public function toHtml()
    {
        $this->productId = $this->getParentBlock()->getItem()->getProductId();
        $this->orderId = $this->getParentBlock()->getItem()->getOrderId();
        $this->storeId = $this->getParentBlock()->getItem()->getOrder()->getStoreId();
        $statusPass = empty($this->getOrderStatuses()) || in_array(
            $this->getParentBlock()->getItem()->getOrder()->getStatus(),
            $this->getOrderStatuses()
        );

        if (!$this->configProvider->isEnabled() || !$this->productId || !$statusPass) {
            return '';
        }

        return parent::toHtml();
    }

    /**
     * @return \Amasty\ProductAttachment\Api\Data\FileInterface[]|bool
     */
    public function getAttachments()
    {
        return $this->fileScopeDataProvider->execute(
            [
                RegistryConstants::PRODUCT => $this->productId,
                RegistryConstants::STORE => $this->storeId,
                RegistryConstants::EXTRA_URL_PARAMS => [
                    'order' => $this->orderId,
                    'product' => $this->productId
                ],
                RegistryConstants::INCLUDE_FILTER => $this->getAttachmentsFilter()
            ],
            'frontendProduct'
        );
    }

    /**
     * @return int
     */
    abstract public function getAttachmentsFilter();

    /**
     * @return array
     */
    abstract public function getOrderStatuses();
}
