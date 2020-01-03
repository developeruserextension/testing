<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\DataProvider;

use Magento\Framework\App\ResourceConnection;
use MageWorx\SeoXTemplates\Model\ConverterBrandFactory;
use MageWorx\SeoXTemplates\Helper\Store as HelperStore;
use Magento\Store\Model\StoreManagerInterface;

class Brand extends \MageWorx\SeoXTemplates\Model\DataProvider
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     *
     * @var int
     */
    protected $_defaultStore;

    /**
     * Store ID for obtaining and preparing data
     *
     * @var int
     */
    protected $_storeId;

    /**
     * @var HelperStore
     */
    protected $helperStore;

    /**
     *
     * @var array
     */
    protected $_attributeCodes = [];

    /**
     *
     * @var \Magento\Framework\Data\Collection
     */
    protected $_collection;

    /**
     * @var ConverterBrandFactory
     */
    protected $converterBrandFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Brand constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param ResourceConnection $resource
     * @param ConverterBrandFactory $converterBrandFactory
     * @param HelperStore $helperStore
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ResourceConnection $resource,
        ConverterBrandFactory $converterBrandFactory,
        HelperStore $helperStore
    ) {
        parent::__construct($resource);
        $this->converterBrandFactory = $converterBrandFactory;
        $this->helperStore                 = $helperStore;
        $this->storeManager                = $storeManager;
    }

    /**
     * @param \Magento\Framework\Data\Collection $collection
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $template
     * @param null $customStoreId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getData($collection, $template, $customStoreId = null)
    {
        $data = [];

        $targetPropertyList = $template->getAttributeCodesByType();
        $targetProperty     = $targetPropertyList[0];
        $storeId    = $this->getStoreId($template, $customStoreId);
        /** @var \MageWorx\SeoXTemplates\Model\Template\Brand $brand */
        foreach ($collection as $brand) {
            $converter      = $this->converterBrandFactory->create($targetProperty);
            $brand->setStoreId($storeId);
            $attributeValue = $converter->convert($brand, $template->getCode());
            $data[$brand->getId()] = [
                'brand_id'  => $brand->getId(),
                'title'           => $brand->getOptionLabel(),
                'store_id'        => $storeId,
                'store_name'      => $this->storeManager->getStore($storeId)->getName(),
                'target_property' => $targetProperty,
                'old_value'       => $brand->getStoreValue($targetProperty, $storeId, true),
                'value'           => $attributeValue
            ];
        }

        return $data;
    }

    /**
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $template
     * @param int|null $customStoreId
     * @return int|null
     */
    protected function getStoreId($template, $customStoreId = null)
    {
        if ($template->getUseForDefaultValue()) {
            return $template->getStoreId();
        }

        if ($customStoreId) {
            return $customStoreId;
        }

        if ($template->getIsSingleStoreMode()) {
            return $this->helperStore->getCurrentStoreId();
        }

        return $template->getStoreId();
    }
}
