<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Helper;

use Magento\Framework\App\ResourceConnection;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Url $urlBulder,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\App\Helper\Context $context,
        ResourceConnection $resource
    ) {
        parent::__construct($context);
        $this->_objectManager = $objectManager;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->urlBulder = $urlBulder;
        $this->productMetadata = $productMetadata;
        $this->connection = $resource->getConnection();
        $this->resource = $resource;
    }

    /**
     * Get module setting value.
     * @return string
     */
    public function getModuleConfig($path, $store = 0) {
        return $this->_scopeConfig->getValue(
            'amasty_paction/' . $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get command data array.
     * @return array
     */
    public function getActionDataByName($type) {
        $className = 'Amasty\Paction\Model\Command\\'  . ucfirst($type);
        if (class_exists($className)) {
            $command = $this->_objectManager->create($className);
            $result = $command->getCreationData();
            $result['url'] = $this->urlBulder->getUrl('amasty_paction/massaction/index');
        }
        else {
            /* initialization for delimiter lines*/
            $result = [
                'confirm_title' => '',
                'confirm_message' => '',
                'type' => $type,
                'label' => '------------',
                'url' => '',
                'fieldLabel'      => ''
            ];
        }

        return $result;
    }

    public function getEntityNameDependOnEdition(){
        $edition = $this->productMetadata->getEdition();
        if ($edition == 'Community') {
            return 'entity_id';
        }

        return 'row_id';
    }

    public function convertEntityIdToRowIdIfNeed($ids)
    {
        if ($this->productMetadata->getEdition() == 'Community') {
            return $ids;
        }

        $tableName = $this->resource->getTableName('catalog_product_entity');
        $select = $this->connection->select()
            ->from($tableName, ['row_id'])
            ->where('entity_id IN (?)', $ids);
        $result = $this->connection->fetchCol($select);

        return $result;
    }

}
