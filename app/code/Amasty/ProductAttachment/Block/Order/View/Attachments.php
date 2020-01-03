<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Block\Order\View;

class Attachments extends \Amasty\ProductAttachment\Block\Order\AbstractAttachments
{
    public function toHtml()
    {
        if (!$this->configProvider->isShowInOrderView()) {
            return '';
        }

        return parent::toHtml();
    }

    public function getBlockTitle()
    {
        return $this->configProvider->getLabelInOrderView();
    }

    /**
     * @return bool
     */
    public function isShowIcon()
    {
        return $this->configProvider->isShowIconInOrderView();
    }

    /**
     * @return bool
     */
    public function isShowFilesize()
    {
        return $this->configProvider->isShowFilesizeInOrderView();
    }

    /**
     * @inheritdoc
     */
    public function getAttachmentsFilter()
    {
        return $this->configProvider->getViewAttachmentsFilter();
    }

    /**
     * @inheritdoc
     */
    public function getOrderStatuses()
    {
        return $this->configProvider->getViewOrderStatuses();
    }
}
