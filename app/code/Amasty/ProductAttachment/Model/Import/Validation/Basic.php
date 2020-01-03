<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Import\Validation;

use Amasty\Base\Model\Import\Validation\Validator;
use Amasty\ProductAttachment\Api\Data\FileInterface;
use Amasty\ProductAttachment\Model\Import\ImportFile;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;

class Basic extends Validator implements \Amasty\Base\Model\Import\Validation\ValidatorInterface
{
    const ERROR_COL_IMPORT_FILE_ID = 'importFileIdEmpty';
    const ERROR_COL_IMPORT_ID = 'importIdEmpty';
    const ERROR_COL_FILENAME = 'filenameEmpty';
    const ERROR_WRONG_CSV_FILE = 'wrongCsvFile';
    const ERROR_COL_LABEL = 'labelEmpty';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::ERROR_COL_IMPORT_FILE_ID => '<b>Error!</b> Field Import File Id can not be empty',
        self::ERROR_COL_IMPORT_ID => '<b>Error!</b> Field Import Id can not be empty',
        self::ERROR_WRONG_CSV_FILE => '<b>Error!</b> Wrong Csv File',
        self::ERROR_COL_FILENAME => '<b>Error!</b> Field Filename can not be empty for store_id 0',
        self::ERROR_COL_LABEL => '<b>Error!</b> Field Label can not be empty for store_id 0',
    ];

    /**
     * @var \Amasty\ProductAttachment\Model\Import\Repository
     */
    private $importRepository;

    /**
     * @var bool
     */
    private $importChecked = false;

    public function __construct(
        \Magento\Framework\DataObject $validationData,
        \Amasty\ProductAttachment\Model\Import\Repository $importRepository
    ) {
        parent::__construct($validationData);
        $this->importRepository = $importRepository;
    }

    /**
     * @inheritdoc
     */
    public function validateRow(array $rowData, $behavior)
    {
        $this->errors = [];

        if (empty($rowData[ImportFile::IMPORT_FILE_ID])) {
            $this->errors[self::ERROR_COL_IMPORT_FILE_ID] = ProcessingError::ERROR_LEVEL_CRITICAL;
        }

        if (empty($rowData[ImportFile::IMPORT_ID])) {
            $this->errors[self::ERROR_COL_IMPORT_ID] = ProcessingError::ERROR_LEVEL_CRITICAL;
        } elseif (!$this->importChecked) {
            try {
                $this->importRepository->getById((int)$rowData[ImportFile::IMPORT_ID]);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                $this->errors[self::ERROR_WRONG_CSV_FILE] = ProcessingError::ERROR_LEVEL_CRITICAL;
            }
            $this->importChecked = true;
        }

        if ((empty($rowData['store_id'])) && empty($rowData[FileInterface::FILENAME])) {
            $this->errors[self::ERROR_COL_FILENAME] = ProcessingError::ERROR_LEVEL_CRITICAL;
        }

        if ((empty($rowData['store_id'])) && empty($rowData[FileInterface::LABEL])) {
            $this->errors[self::ERROR_COL_LABEL] = ProcessingError::ERROR_LEVEL_CRITICAL;
        }

        return parent::validateResult();
    }
}
