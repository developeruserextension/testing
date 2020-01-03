<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\File\FileScope\DataProviders\Frontend;

use Amasty\ProductAttachment\Api\Data\FileInterface;
use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Amasty\ProductAttachment\Model\File\ResourceModel\CollectionFactory;

class FileIds implements \Amasty\ProductAttachment\Model\File\FileScope\DataProviders\FileScopeDataInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var File
     */
    private $fileDataProvider;

    public function __construct(
        CollectionFactory $collectionFactory,
        File $fileDataProvider
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->fileDataProvider = $fileDataProvider;
    }

    /**
     * @inheritdoc
     */
    public function execute($params)
    {
        $fileIds = $params[RegistryConstants::FILE_IDS];
        $storeId = $params[RegistryConstants::STORE];

        /** @var \Amasty\ProductAttachment\Model\File\ResourceModel\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('main_table.' . FileInterface::FILE_ID, $fileIds);

        /** @var \Amasty\ProductAttachment\Api\Data\FileInterface[] $files */
        if ($files = $collection->getItems()) {
            foreach ($files as &$file) {
                $fileParams = [RegistryConstants::FILE => $file, RegistryConstants::STORE => $storeId];
                if (isset($params[RegistryConstants::EXTRA_URL_PARAMS])) {
                    $fileParams[RegistryConstants::EXTRA_URL_PARAMS] = $params[RegistryConstants::EXTRA_URL_PARAMS];
                }
                if (isset($params[RegistryConstants::INCLUDE_FILTER])) {
                    $fileParams[RegistryConstants::INCLUDE_FILTER] = $params[RegistryConstants::INCLUDE_FILTER];
                }
                $file = $this->fileDataProvider->execute(
                    $fileParams
                );
            }
            $files = array_filter(
                $files,
                function ($value) {
                    return $value !== false;
                }
            );

            if (!empty($params[RegistryConstants::FILE_IDS_ORDER])) {
                $order = $params[RegistryConstants::FILE_IDS_ORDER];
                usort($files, function ($file1, $file2) use ($order) {
                    $file1Order = isset($order[$file1->getFileId()]) ? $order[$file1->getFileId()] : 0;
                    $file2Order = isset($order[$file2->getFileId()]) ? $order[$file2->getFileId()] : 0;
                    if ($file1Order > $file2Order) {
                        return 1;
                    } elseif ($file1Order < $file2Order) {
                        return -1;
                    }

                    return 0;
                });
            }

            return $files;
        }

        return false;
    }
}
