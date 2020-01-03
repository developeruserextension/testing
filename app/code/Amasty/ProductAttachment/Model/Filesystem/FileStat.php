<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Filesystem;

class FileStat
{
    /**
     * @var string
     */
    private $fileName;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    public function __construct(
        $fileName = '',
        \Magento\Framework\Filesystem $filesystem
    ) {
        //TODO check if file exists
        $this->fileName = $fileName;
        $this->filesystem = $filesystem;
        $this->collectFileStat();
    }

    private function collectFileStat()
    {
        
    }
}
