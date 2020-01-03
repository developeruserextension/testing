<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachmentApi
 */


namespace Amasty\ProductAttachmentApi\Api\Data;

interface FileContentInterface
{
    const BASE64_ENCODED_DATA = 'base64_encoded_data';
    const NAME = 'name_with_extension';
    const TMP_NAME = 'temporary_name';

    /**
     * @return string
     */
    public function getBase64EncodedData();

    /**
     * @param string $base64EncodedData
     *
     * @return FileContentInterface
     */
    public function setBase64EncodedData($base64EncodedData);

    /**
     * @param string $nameWithExtension
     *
     * @return FileContentInterface
     */
    public function setNameWithExtension($nameWithExtension);

    /**
     * @return string
     */
    public function getNameWithExtension();

    /**
     * @return string
     */
    public function getTemporaryName();
}
