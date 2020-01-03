<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Import\DataProvider;

use Amasty\ProductAttachment\Model\Import\Import;
use Amasty\ProductAttachment\Model\Import\Import as ImportModel;
use Amasty\ProductAttachment\Model\Import\ResourceModel\ImportCollectionFactory;

class Stores extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    public function __construct(
        ImportCollectionFactory $importCollectionFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $importCollectionFactory->create();
    }

    public function getData()
    {
        $data = parent::getData();
        $data['items'][0][ImportModel::STORE_IDS] = explode(',', $data['items'][0][ImportModel::STORE_IDS]);
        $data[$data['items'][0][ImportModel::IMPORT_ID]] = $data['items'][0];

        return $data;
    }
}
