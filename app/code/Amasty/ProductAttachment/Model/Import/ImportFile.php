<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Import;

use Amasty\ProductAttachment\Api\Data\FileInterface;
use Magento\Framework\Model\AbstractModel;

class ImportFile extends AbstractModel
{
    /**#@+
     * Constants defined for keys of data array
     */
    const IMPORT_FILE_ID = 'import_file_id';
    const IMPORT_ID = 'import_id';
    /**#@-*/

    public function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\ProductAttachment\Model\Import\ResourceModel\ImportFile::class);
        $this->setIdFieldName(self::IMPORT_FILE_ID);
    }

    /**
     * @param int $importFileId
     *
     * @return ImportFile
     */
    public function setImportFileId($importFileId)
    {
        return $this->setData(self::IMPORT_FILE_ID, (int)$importFileId);
    }

    /**
     * @return int
     */
    public function getImportFileId()
    {
        return (int)$this->_getData(self::IMPORT_FILE_ID);
    }

    /**
     * @param int $importId
     *
     * @return ImportFile
     */
    public function setImportId($importId)
    {
        return $this->setData(self::IMPORT_ID, (int)$importId);
    }

    /**
     * @return int
     */
    public function getImportId()
    {
        return (int)$this->_getData(self::IMPORT_ID);
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->_getData(FileInterface::FILE_PATH);
    }

    /**
     * @param string $filePath
     *
     * @return ImportFile
     */
    public function setFilePath($filePath)
    {
        return $this->setData(FileInterface::FILE_PATH, $filePath);
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->_getData(FileInterface::FILENAME);
    }

    /**
     * @param string $fileName
     *
     * @return ImportFile
     */
    public function setFileName($fileName)
    {
        return $this->setData(FileInterface::FILENAME, $fileName);
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->_getData(FileInterface::LABEL);
    }

    /**
     * @param string $label
     *
     * @return ImportFile
     */
    public function setLabel($label)
    {
        return $this->setData(FileInterface::LABEL, $label);
    }

    /**
     * @return array
     */
    public function getCustomerGroups()
    {
        $customerGroups = $this->_getData(FileInterface::CUSTOMER_GROUPS);
        if ($customerGroups !== null && $customerGroups !== "" && !is_array($customerGroups)) {
            $customerGroups = explode(',', $customerGroups);
        }
        return $customerGroups;
    }

    /**
     * @param $customerGroups
     *
     * @return ImportFile
     */
    public function setCustomerGroups($customerGroups)
    {
        return $this->setData(FileInterface::CUSTOMER_GROUPS, $customerGroups);
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return (bool)$this->_getData(FileInterface::IS_VISIBLE);
    }

    /**
     * @param bool $isVisible
     *
     * @return ImportFile
     */
    public function setIsVisible($isVisible)
    {
        return $this->setData(FileInterface::IS_VISIBLE, (bool)$isVisible);
    }

    /**
     * @return bool
     */
    public function isIncludeInOrder()
    {
        return (bool)$this->_getData(FileInterface::INCLUDE_IN_ORDER);
    }

    /**
     * @param bool $isIncludeInOrder
     *
     * @return ImportFile
     */
    public function setIsIncludeInOrder($isIncludeInOrder)
    {
        return $this->setData(FileInterface::INCLUDE_IN_ORDER, (bool)$isIncludeInOrder);
    }
}
