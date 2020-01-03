<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Block\Order\Email;

class Attachments extends \Amasty\ProductAttachment\Block\Order\AbstractAttachments
{
    public function toHtml()
    {
        if (!$this->configProvider->isShowInOrderEmail()) {
            return '';
        }

        return parent::toHtml();
    }

    public function getBlockTitle()
    {
        return $this->configProvider->getLabelInOrderEmail();
    }

    /**
     * @inheritdoc
     */
    public function getAttachmentsFilter()
    {
        return $this->configProvider->getEmailAttachmentsFilter();
    }

    /**
     * @inheritdoc
     */
    public function getOrderStatuses()
    {
        return $this->configProvider->getEmailOrderStatuses();
    }
}
