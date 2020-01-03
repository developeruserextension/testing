<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model;

class ConfigProvider extends \Amasty\Base\Model\ConfigProviderAbstract
{
    protected $pathPrefix = 'amfile/';

    /**#@+
     * Constants defined for xpath of system configuration
     */
    const XPATH_ENABLED = 'general/enabled';
    const ADD_CATEGORIES_FILES_TO_PRODUCTS = 'general/add_categories_files';
    const EXCLUDE_INCLUDE_IN_ORDER_FILES = 'general/exclude_include_in_order_files';
    const URL_TYPE = 'general/url_type';
    const DETECT_MIME_TYPE = 'additional/detect_mime';
    const BLOCK_TITLE = 'product_tab/block_label';
    const BLOCK_ENABLED = 'product_tab/block_enabled';
    const BLOCK_SORT_ORDER = 'product_tab/block_sort_order';
    const BLOCK_CUSTOMER_GROUPS = 'product_tab/customer_group';
    const SHOW_ICON = 'product_tab/block_fileicon';
    const SHOW_FILESIZE = 'product_tab/block_filesize';
    const SHOW_IN_ORDER_VIEW = 'order_view/show_attachments';
    const ORDER_VIEW_LABEL = 'order_view/label';
    const ORDER_VIEW_ORDER_STATUS = 'order_view/order_status';
    const ORDER_VIEW_SHOW_FILESIZE = 'order_view/filesize';
    const ORDER_VIEW_SHOW_ICON = 'order_view/fileicon';
    const ORDER_VIEW_ATTACHMENTS_FILTER = 'order_view/include_attachments_filter';
    const SHOW_IN_ORDER_EMAIL = 'order_email/show_attachments';
    const ORDER_EMAIL_LABEL = 'order_email/label';
    const ORDER_EMAIL_ATTACHMENTS_FILTER = 'order_email/include_attachments_filter';
    const ORDER_EMAIL_ORDER_STATUS = 'order_email/order_status';
    const BLOCK_LOCATION = 'block/block_location';
    /**#@-*/

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isSetFlag(self::XPATH_ENABLED);
    }

    /**
     * @return string
     */
    public function getBlockTitle()
    {
        return $this->getValue(self::BLOCK_TITLE);
    }

    /**
     * @return int
     */
    public function getUrlType()
    {
        return (int)$this->getValue(self::URL_TYPE);
    }

    /**
     * @return string
     */
    public function getBlockCustomerGroups()
    {
        return $this->getValue(self::BLOCK_CUSTOMER_GROUPS);
    }

    /**
     * @return bool
     */
    public function detectMimeType()
    {
        return $this->isSetFlag(self::DETECT_MIME_TYPE);
    }

    /**
     * @return bool
     */
    public function addCategoriesFilesToProducts()
    {
        return $this->isSetFlag(self::ADD_CATEGORIES_FILES_TO_PRODUCTS);
    }

    /**
     * @return bool
     */
    public function isBlockEnabled()
    {
        return $this->isSetFlag(self::BLOCK_ENABLED);
    }
    /**
     * @return string
     */
    public function getBlockSortOrder()
    {
        return (int)$this->getValue(self::BLOCK_SORT_ORDER);
    }

    /**
     * @return bool
     */
    public function isShowIcon()
    {
        return $this->isSetFlag(self::SHOW_ICON);
    }

    /**
     * @return bool
     */
    public function isShowFilesize()
    {
        return $this->isSetFlag(self::SHOW_FILESIZE);
    }

    /**
     * @return bool
     */
    public function isShowInOrderView()
    {
        return $this->isSetFlag(self::SHOW_IN_ORDER_VIEW);
    }

    /**
     * @return string
     */
    public function getLabelInOrderView()
    {
        return $this->getValue(self::ORDER_VIEW_LABEL);
    }

    /**
     * @return array
     */
    public function getViewOrderStatuses()
    {
        $orderStatuses = $this->getValue(self::ORDER_VIEW_ORDER_STATUS);
        if (empty($orderStatuses)) {
            return [];
        }

        return array_map('trim', explode(',', $orderStatuses));
    }

    /**
     * @return bool
     */
    public function isShowIconInOrderView()
    {
        return $this->isSetFlag(self::ORDER_VIEW_SHOW_ICON);
    }

    /**
     * @return bool
     */
    public function isShowFilesizeInOrderView()
    {
        return $this->isSetFlag(self::ORDER_VIEW_SHOW_FILESIZE);
    }

    /**
     * @return int
     */
    public function getViewAttachmentsFilter()
    {
        return (int)$this->getValue(self::ORDER_VIEW_ATTACHMENTS_FILTER);
    }

    /**
     * @return bool
     */
    public function isShowInOrderEmail()
    {
        return $this->isSetFlag(self::SHOW_IN_ORDER_EMAIL);
    }

    /**
     * @return string
     */
    public function getLabelInOrderEmail()
    {
        return $this->getValue(self::ORDER_EMAIL_LABEL);
    }

    /**
     * @return int
     */
    public function getEmailAttachmentsFilter()
    {
        return (int)$this->getValue(self::ORDER_EMAIL_ATTACHMENTS_FILTER);
    }

    /**
     * @return array
     */
    public function getEmailOrderStatuses()
    {
        $orderStatuses = $this->getValue(self::ORDER_EMAIL_ORDER_STATUS);
        if (empty($orderStatuses)) {
            return [];
        }

        return array_map('trim', explode(',', $orderStatuses));
    }

    /**
     * @return string
     */
    public function getBlockLocation()
    {
        return $this->getValue(self::BLOCK_LOCATION);
    }

    /**
     * @return bool
     */
    public function excludeIncludeInOrderFiles()
    {
        return !$this->isSetFlag(self::EXCLUDE_INCLUDE_IN_ORDER_FILES);
    }
}
