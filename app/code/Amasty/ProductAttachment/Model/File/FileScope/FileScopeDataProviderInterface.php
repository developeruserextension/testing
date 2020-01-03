<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\File\FileScope;

interface FileScopeDataProviderInterface
{
    /**
     * @param array $params
     * @param string $dataProviderName
     *
     * @return mixed
     */
    public function execute($params, $dataProviderName);
}
