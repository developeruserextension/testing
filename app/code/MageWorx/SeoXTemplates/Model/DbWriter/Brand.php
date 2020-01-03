<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Model\DbWriter;

use Magento\Framework\App\ResourceConnection;
use MageWorx\SeoXTemplates\Model\DataProviderBrandFactory;
use MageWorx\SeoAll\Helper\LinkFieldResolver;

class Brand extends \MageWorx\SeoXTemplates\Model\DbWriter
{
    /**
     * @var DataProviderBrandFactory
     */
    protected $dataProviderBrandFactory;

    /**
     * @var \MageWorx\SeoAll\Helper\LinkFieldResolver
     */
    protected $linkFieldResolver;

    /**
     * Brand constructor.
     *
     * @param ResourceConnection $resource
     * @param DataProviderBrandFactory $dataProviderBrandFactory
     * @param LinkFieldResolver $linkFieldResolver
     */
    public function __construct(
        ResourceConnection $resource,
        DataProviderBrandFactory $dataProviderBrandFactory,
        LinkFieldResolver $linkFieldResolver
    ) {
        parent::__construct($resource);
        $this->dataProviderBrandFactory = $dataProviderBrandFactory;
        $this->linkFieldResolver = $linkFieldResolver;
    }

    /**
     * @param \Magento\Framework\Data\Collection $collection
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $template
     * @param null $customStoreId
     * @return array|bool
     * @throws \Exception
     */
    public function write($collection, $template, $customStoreId = null)
    {
        if (!$collection) {
            return false;
        }

        $dataProvider = $this->dataProviderBrandFactory->create($template->getTypeId());
        $data         = $dataProvider->getData($collection, $template, $customStoreId);
        foreach ($collection as $brand) {
            if (empty($data[$brand->getId()])) {
                continue;
            }

            $filterData = $data[$brand->getId()];

            if (!$filterData['value']) {
                continue;
            }
            $brand->setStoreId($filterData['store_id']);
            $brand->setData($filterData['target_property'], $filterData['value']);
            $brand->setStoreValue($filterData['target_property'], $filterData['value'], $filterData['store_id']);
            $brand->save();
        }

        return true;
    }
}
