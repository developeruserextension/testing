<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Controller\Adminhtml\Import;

use Amasty\ProductAttachment\Api\Data\FileInterface;
use Amasty\ProductAttachment\Controller\Adminhtml\Import;
use Amasty\ProductAttachment\Model\Import\ImportFile;
use Amasty\ProductAttachment\Model\Import\Repository;
use Magento\Backend\App\Action;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Filesystem\Driver\File as CsvFile;

class Generate extends Import
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var CsvFile
     */
    private $file;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    public function __construct(
        Repository $repository,
        CsvFile $file,
        FileFactory $fileFactory,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->repository = $repository;
        $this->file = $file;
        $this->fileFactory = $fileFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        if ($importId = $this->getRequest()->getParam('import_id')) {
            if ($import = $this->repository->getById((int)$importId)) {
                $importFiles = $this->repository->getImportFilesByImportId($import->getImportId());
                $storeIds = [];
                if (empty($import->getStoreIds())) {
                    $storeIds[] = 0;
                } else {
                    $storeIds = $import->getStoreIds();
                }

                if (!in_array(0, $storeIds)) {
                    $storeIds = array_merge([0], $storeIds);
                }
                $result = [
                    [
                        ImportFile::IMPORT_FILE_ID,
                        ImportFile::IMPORT_ID,
                        'store_id',
                        FileInterface::FILENAME,
                        FileInterface::LABEL,
                        FileInterface::CUSTOMER_GROUPS,
                        FileInterface::IS_VISIBLE,
                        FileInterface::INCLUDE_IN_ORDER,
                        FileInterface::PRODUCTS,
                        FileInterface::CATEGORIES,
                        'product_skus'
                    ]
                ];
                foreach ($importFiles as $importFile) {
                    foreach ($storeIds as $storeId) {
                        if ($storeId == 0) {
                            $result[] = [
                                ImportFile::IMPORT_FILE_ID      => $importFile->getImportFileId(),
                                ImportFile::IMPORT_ID           => $importFile->getImportId(),
                                'store_id'                      => $storeId,
                                FileInterface::FILENAME         => $importFile->getFileName(),
                                FileInterface::LABEL            => $importFile->getLabel(),
                                FileInterface::CUSTOMER_GROUPS  => $importFile->getData(FileInterface::CUSTOMER_GROUPS),
                                FileInterface::IS_VISIBLE       => (int)$importFile->isVisible(),
                                FileInterface::INCLUDE_IN_ORDER => (int)$importFile->isIncludeInOrder(),
                                FileInterface::PRODUCTS         => '',
                                FileInterface::CATEGORIES       => '',
                                'product_skus' => ''
                            ];
                        } else {
                            $result[] = [
                                ImportFile::IMPORT_FILE_ID      => $importFile->getImportFileId(),
                                ImportFile::IMPORT_ID           => $importFile->getImportId(),
                                'store_id'                      => $storeId,
                                FileInterface::FILENAME         => '',
                                FileInterface::LABEL            => '',
                                FileInterface::CUSTOMER_GROUPS  => '',
                                FileInterface::IS_VISIBLE       => '',
                                FileInterface::INCLUDE_IN_ORDER => '',
                                FileInterface::PRODUCTS         => '',
                                FileInterface::CATEGORIES       => '',
                                'product_skus' => ''
                            ];
                        }
                    }
                }
                $resource = fopen('php://memory', 'a+');
                foreach ($result as $row) {
                    $this->file->filePutCsv($resource, $row);
                }

                fseek($resource, 0);
                $csvContent = stream_get_contents($resource);

                $this->fileFactory->create(
                    'amfile_import_' . $importId . '.csv',
                    null,
                    \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                    'application/octet-stream',
                    strlen($csvContent)
                );
                /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
                $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);
                $resultRaw->setContents($csvContent);

                return $resultRaw;
            }
        }

        return $this->_redirect('*/*/');
    }
}
