<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachmentApi
 */


namespace Amasty\ProductAttachmentApi\Api\Data;

interface BackendFileInterface extends \Amasty\ProductAttachment\Api\Data\FileInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ATTACHMENT_PRODUCTS = 'attachment_products';
    const ATTACHMENT_CATEGORIES = 'attachment_categories';
    const TMP_FILE = 'tmp_file';
    /**#@-*/

    /**
     * @return string[]
     */
    public function getAttachmentProducts();

    /**
     * @param string[] $products
     *
     * @return \Amasty\ProductAttachmentApi\Api\Data\BackendFileInterface
     */
    public function setAttachmentProducts($products);

    /**
     * @return string[]
     */
    public function getAttachmentCategories();

    /**
     * @param string[] $categories
     *
     * @return \Amasty\ProductAttachmentApi\Api\Data\BackendFileInterface
     */
    public function setAttachmentCategories($categories);

    /**
     * @return string
     */
    public function getTmpFile();

    /**
     * @param string $tmpFile
     *
     * @return \Amasty\ProductAttachmentApi\Api\Data\BackendFileInterface
     */
    public function setTmpFile($tmpFile);
}
