<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\File\FileScope\SaveProcessors;

interface FileScopeSaveProcessorInterface
{
    /**
     * @param array $params
     *
     * @return array
     */
    public function execute($params);
}
