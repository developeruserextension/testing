<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Model\Source;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Option\ArrayInterface;
use Zend_Db_Select;

class CustomForm implements ArrayInterface
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    public function __construct(ResourceConnection $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $connection = $this->resource->getConnection();
        $tableName = $this->resource->getTableName('am_customform_form');
        if ($connection->isTableExists($tableName)) {
            $select = $connection->select()
                ->from($tableName)
                ->reset(Zend_Db_Select::COLUMNS)
                ->columns(['value' => 'form_id', 'label' => 'title']);

            $options = $connection->fetchAll($select);
        }

        return $options;
    }
}
