<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\File\FileScope\DataProviders;

interface FileScopeDataInterface
{
    /**
     * @param array $params
     *
     * @return \Amasty\ProductAttachment\Api\Data\FileInterface|\Amasty\ProductAttachment\Api\Data\FileInterface[]|array
     */
    public function execute($params);
}
