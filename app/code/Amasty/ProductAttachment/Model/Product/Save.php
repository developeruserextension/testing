<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Product;

use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Amasty\ProductAttachment\Model\ConfigProvider;
use Amasty\ProductAttachment\Model\File\FileScope\SaveFileScopeInterface;

class Save implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var SaveFileScopeInterface
     */
    private $saveFileScope;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        SaveFileScopeInterface $saveFileScope,
        ConfigProvider $configProvider
    ) {
        $this->saveFileScope = $saveFileScope;
        $this->configProvider = $configProvider;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $files = $observer->getController()->getRequest()->getParam('attachments');
        $params = [
            RegistryConstants::FILES => !empty($files['files']) ? $files['files'] : false,
            RegistryConstants::PRODUCT => $observer->getProduct()->getId(),
            RegistryConstants::STORE => (int)$observer->getController()->getRequest()->getParam('store')
        ];
        if (!empty($files['delete'])) {
            $params[RegistryConstants::TO_DELETE] = $files['delete'];
        }
        $this->saveFileScope->execute($params, 'product');
        if ($this->configProvider->addCategoriesFilesToProducts()) {
            $files = $observer->getController()->getRequest()->getParam('categories_attachments');
            $params = [
                RegistryConstants::FILES => !empty($files['categories_files']) ? $files['categories_files'] : false,
                RegistryConstants::PRODUCT => $observer->getProduct()->getId(),
                RegistryConstants::STORE => (int)$observer->getController()->getRequest()->getParam('store')
            ];
            $this->saveFileScope->execute($params, 'productCategories');
        }
    }
}
