<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Block\Adminhtml\Buttons\File;

use Amasty\ProductAttachment\Block\Adminhtml\Buttons\GenericButton;
use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        if (!$this->getFileId()) {
            return [];
        }
        $alertMessage = __('Are you sure you want to do this?');
        $onClick = sprintf('deleteConfirm("%s", "%s")', $alertMessage, $this->getDeleteUrl());

        $data = [
            'label' => __('Delete File'),
            'class' => 'delete',
            'id' => 'file-edit-delete-button',
            'on_click' => $onClick,
            'sort_order' => 20,
        ];

        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', [RegistryConstants::FORM_FILE_ID => $this->getFileId()]);
    }

    /**
     * @return null|int
     */
    public function getFileId()
    {
        return (int)$this->request->getParam(RegistryConstants::FORM_FILE_ID);
    }
}
