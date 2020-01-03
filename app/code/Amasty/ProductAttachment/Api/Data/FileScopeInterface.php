<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Api\Data;

interface FileScopeInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const FILE_STORE_ID = 'file_store_id';
    const FILE_STORE_CATEGORY_ID = 'file_store_category_id';
    const FILE_STORE_PRODUCT_ID = 'file_store_product_id';
    const FILE_STORE_CATEGORY_PRODUCT_ID = 'file_store_category_product_id';
    const FILE_ID = 'file_id';
    const STORE_ID = 'store_id';
    const PRODUCT_ID = 'product_id';
    const CATEGORY_ID = 'category_id';
    const FILENAME = 'filename';
    const LABEL = 'label';
    const IS_VISIBLE = 'is_visible';
    const INCLUDE_IN_ORDER = 'include_in_order';
    const CUSTOMER_GROUPS = 'customer_groups';
    const POSITION = 'position';
    /**#@-*/
}
