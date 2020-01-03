<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Category;

use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Amasty\ProductAttachment\Model\File\FileScope\SaveFileScopeInterface;

class Save implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var SaveFileScopeInterface
     */
    private $saveFileScope;

    public function __construct(
        SaveFileScopeInterface $saveFileScope
    ) {
        $this->saveFileScope = $saveFileScope;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $files = $observer->getRequest()->getParam('attachments');
        $params = [
            RegistryConstants::FILES => !empty($files['files']) ? $files['files'] : false,
            RegistryConstants::CATEGORY => $observer->getCategory()->getId(),
            RegistryConstants::STORE => (int)$observer->getRequest()->getParam('store_id')
        ];
        if (!empty($files['delete'])) {
            $params[RegistryConstants::TO_DELETE] = $files['delete'];
        }
        $this->saveFileScope->execute(
            $params,
            'category'
        );
    }
}
