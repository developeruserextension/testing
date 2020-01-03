<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Import;

use Magento\Framework\Model\AbstractModel;

class Import extends AbstractModel
{
    /**#@+
     * Constants defined for keys of data array
     */
    const IMPORT_ID = 'import_id';
    const STORE_IDS = 'store_ids';
    const CREATED_AT = 'created_at';
    /**#@-*/

    public function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\ProductAttachment\Model\Import\ResourceModel\Import::class);
        $this->setIdFieldName(self::IMPORT_ID);
    }

    /**
     * @param int $importId
     *
     * @return Import
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
     * @param string|array $storeIds
     *
     * @return Import
     */
    public function setStoreIds($storeIds)
    {
        if (is_array($storeIds)) {
            return $this->setData(self::STORE_IDS, implode(',', $storeIds));
        }

        return $this->setData(self::STORE_IDS, $storeIds);
    }

    /**
     * @return array
     */
    public function getStoreIds()
    {
        $storeIds = $this->_getData(self::STORE_IDS);
        if (!is_array($storeIds)) {
            $storeIds = explode(',', $storeIds);
        }

        return $storeIds;
    }

    /**
     * @return int
     */
    public function getCreatedAt()
    {
        return $this->_getData(self::CREATED_AT);
    }
}
