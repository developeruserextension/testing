<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\File\DataProvider;

use Amasty\ProductAttachment\Model\File\ResourceModel\CollectionFactory;
use Amasty\ProductAttachment\Api\Data\FileInterface;
use Amasty\ProductAttachment\Model\Icon\GetIconForFile;

class Listing extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Amasty\ProductAttachment\Model\File\ResourceModel\Collection
     */
    protected $collection;

    /**
     * @var GetIconForFile
     */
    private $getIconForFile;

    public function __construct(
        CollectionFactory $collectionFactory,
        GetIconForFile $getIconForFile,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->collection->addFileData();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->getIconForFile = $getIconForFile;
    }

    public function getData()
    {
        $data = parent::getData();
        if (!empty($data['items'])) {
            foreach ($data['items'] as &$item) {
                if (!empty($item[FileInterface::EXTENSION])) {
                    $item['icon_src'] = $this->getIconForFile->byFileExtension(
                        $item[FileInterface::EXTENSION]
                    );
                }
                $item[FileInterface::SIZE] = $this->getReadableFileSize((int)$item[FileInterface::SIZE]);
            }
        }

        return $data;
    }

    /**
     * @param int $bytes
     *
     * @return string
     */
    public function getReadableFileSize($bytes = 0)
    {
        $size   = ['B', 'kB', 'MB', 'GB', 'TB'];
        $factor = (int)floor((strlen($bytes) - 1) / 3);
        if (isset($size[$factor])) {
            $bytes = sprintf("%.2f", $bytes / pow(1024, $factor)) . ' ' . $size[$factor];
        }

        return $bytes;
    }
}
