<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Controller\Adminhtml;

use Amasty\ProductAttachment\Api\Data\IconInterface;
use Amasty\ProductAttachment\Api\Data\FileInterface;

class RegistryConstants
{
    /**#@+
     * Constants defined for dataPersistor
     */
    const ICON_DATA = 'iconData';
    const FILE_DATA = 'fileData';
    /**#@-*/

    /**#@+
     * Constants defined for custom fields
     */
    const ICON_FILE_KEY = IconInterface::IMAGE . 'file';
    const FILE_KEY = 'file';
    /**#@-*/

    /**#@+
     * Constants defined for form url ids
     */
    const FORM_ICON_ID = 'icon_id';
    const FORM_FILE_ID = 'file_id';
    /**#@-*/

    /**#@+
     * Constants defined for FileScopeDataProvider keys
     */
    const FILE = 'file';
    const FILES = 'files';
    const FILE_IDS = 'file_ids';
    const FILE_IDS_ORDER = 'file_ids_order';
    const FILES_LIMIT = 'files_limit';
    const STORE = 'store';
    const CATEGORY = 'category';
    const PRODUCT = 'product';
    const PRODUCT_CATEGORIES = 'product_categories';
    const EXTRA_URL_PARAMS = 'url_params';
    const INCLUDE_FILTER = 'include_filter';
    const CUSTOMER_GROUP = 'customer_group';
    const TO_DELETE = 'to_delete';
    const EXCLUDE_FILES = 'exclude_files';
    /**#@-*/

    const USE_DEFAULT_FIELDS = [
        FileInterface::FILENAME,
        FileInterface::LABEL,
        FileInterface::IS_VISIBLE,
        FileInterface::INCLUDE_IN_ORDER,
        FileInterface::CUSTOMER_GROUPS,
    ];

    const USE_DEFAULT_PREFIX = 'set_use_default_';

    const ICON_EXTENSIONS = ['jpg', 'png', 'jpeg', 'bmp'];
}
