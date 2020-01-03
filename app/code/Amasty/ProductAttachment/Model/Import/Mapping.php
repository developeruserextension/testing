<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Import;

use Amasty\Base\Model\Import\Mapping\Mapping as MappingBase;
use Amasty\ProductAttachment\Api\Data\FileInterface;

class Mapping extends MappingBase implements \Amasty\Base\Model\Import\Mapping\MappingInterface
{
    /**
     * @var array
     */
    protected $mappings = [
        ImportFile::IMPORT_FILE_ID,
        ImportFile::IMPORT_ID,
        'store_id',
        FileInterface::FILENAME,
        FileInterface::LABEL,
        FileInterface::CUSTOMER_GROUPS,
        FileInterface::IS_VISIBLE,
        FileInterface::INCLUDE_IN_ORDER,
        FileInterface::PRODUCTS,
        FileInterface::CATEGORIES,
        'product_skus'
    ];

    /**
     * @var string
     */
    protected $masterAttributeCode = ImportFile::IMPORT_FILE_ID;
}
