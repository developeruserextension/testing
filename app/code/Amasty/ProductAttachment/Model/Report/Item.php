<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Report;

use Magento\Framework\Model\AbstractModel;

class Item extends AbstractModel
{
    const ITEM_ID = 'item_id';
    const FILE_ID = 'file_id';
    const STORE_ID = 'store_id';
    const DOWNLOAD_SOURCE = 'download_source';
    const PRODUCT_ID = 'product_id';
    const CATEGORY_ID = 'category_id';
    const ORDER_ID = 'order_id';
    const CUSTOMER_ID = 'customer_id';
    const DOWNLOADED_AT = 'downloaded_at';

    public function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\ProductAttachment\Model\Report\ResourceModel\Item::class);
        $this->setIdFieldName(self::ITEM_ID);
    }

    /**
     * @param int $fileId
     *
     * @return $this
     */
    public function setFileId($fileId)
    {
        return $this->setData(self::FILE_ID, (int)$fileId);
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, (int)$storeId);
    }

    /**
     * @param int $sourceId
     *
     * @return $this
     */
    public function setDownloadSource($sourceId)
    {
        return $this->setData(self::DOWNLOAD_SOURCE, (int)$sourceId);
    }

    /**
     * @param int $productId
     *
     * @return $this
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, (int)$productId);
    }

    /**
     * @param int $categoryId
     *
     * @return $this
     */
    public function setCategoryId($categoryId)
    {
        return $this->setData(self::CATEGORY_ID, (int)$categoryId);
    }

    /**
     * @param int $orderId
     *
     * @return $this
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, (int)$orderId);
    }

    /**
     * @param int $customerId
     *
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, (int)$customerId);
    }
}
