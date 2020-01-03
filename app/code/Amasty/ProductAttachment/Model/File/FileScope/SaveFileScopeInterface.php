<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\File\FileScope;

interface SaveFileScopeInterface
{
    /**
     * @param array $params
     * @param string $saveProcessorName
     *
     * @return void
     */
    public function execute($params, $saveProcessorName);
}
