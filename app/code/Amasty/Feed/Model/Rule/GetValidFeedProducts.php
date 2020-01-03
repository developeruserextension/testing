<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Model\Rule;

use Amasty\Feed\Model\Config;
use Amasty\Feed\Model\Rule\RuleFactory;
use Magento\Catalog\Model\Config as CatalogConfig;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Rule\Model\Condition\Sql\Builder;

class GetValidFeedProducts
{
    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var array
     */
    private $productIds = [];

    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * Catalog config
     *
     * @var CatalogConfig
     */
    protected $catalogConfig;

    /**
     * @var Builder
     */
    protected $sqlBuilder;

    public function __construct(
        RuleFactory $ruleFactory,
        CollectionFactory $productCollectionFactory,
        Config $config,
        Builder $sqlBuilder,
        CatalogConfig $catalogConfig
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->ruleFactory = $ruleFactory;
        $this->config = $config;
        $this->sqlBuilder = $sqlBuilder;
        $this->catalogConfig = $catalogConfig;
    }

    /**
     * @param \Amasty\Feed\Model\Feed $model
     * @param array $ids
     *
     * @return array
     */
    public function execute(\Amasty\Feed\Model\Feed $model, array $ids = [])
    {
        $rule = $this->ruleFactory->create();
        $rule->setConditionsSerialized($model->getConditionsSerialized());
        $rule->setStoreId($model->getStoreId());
        $model->setRule($rule);
        $itemsPerPage = $this->config->getItemsPerPage();
        $page = 0;

        $validProducts = [];

        do {
            $result = $this->getValidProducts($model, ++$page, $itemsPerPage, $ids);
            $validProducts = array_merge($validProducts, $result['productsId']);
        } while (!$result['isLastPage']);

        return $validProducts;
    }

    public function getValidProducts(\Amasty\Feed\Model\Feed $model, $page, $itemsPerPage, array $ids = [])
    {
        $isLastPage = false;

        /** @var $productCollection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $productCollection = $this->prepareCollection($model, $page, $itemsPerPage, $ids);
        if ($productCollection->getCurPage() >= $productCollection->getLastPageNumber()) {
            $isLastPage = true;
        }

        $this->productIds = [];

        $products = $productCollection->getItems();

        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        foreach ($products as $product) {
            if ($this->validateProduct($model, $product)) {
                $this->productIds[] = [
                    'feed_id' => $model->getEntityId(),
                    'valid_product_id' => $product->getId()
                ];
            }
        }

        return ['isLastPage' => $isLastPage, 'productsId' => $this->productIds];
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $model
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     *
     * @return bool
     */
    public function validateProduct(
        \Magento\Framework\Model\AbstractModel $model,
        \Magento\Catalog\Api\Data\ProductInterface $product
    ) {
        $product->setStoreId($model->getStoreId());

        return $model->getRule()->getConditions()->validate($product);
    }

    protected function _addProductAttributesAndPrices(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
    ) {
        return $collection
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->addUrlRewrite();
    }

    /**
     * @param \Amasty\Feed\Model\Feed $model
     * @param int $page
     * @param int $itemsPerPage
     * @param array $ids
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    private function prepareCollection(\Amasty\Feed\Model\Feed $model, $page, $itemsPerPage, $ids = [])
    {
        /** @var $productCollection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addStoreFilter($model->getStoreId());

        if ($ids) {
            $productCollection->addAttributeToFilter('entity_id', ['in' => $ids]);
        }

        // DBEST-1250
        if ($model->getExcludeDisabled()) {
            $productCollection->addAttributeToFilter('status', ['eq' => Status::STATUS_ENABLED]);
        }
        if ($model->getExcludeNotVisible()) {
            $productCollection->addAttributeToFilter('visibility', ['neq' => Visibility::VISIBILITY_NOT_VISIBLE]);
        }
        if ($model->getExcludeOutOfStock()) {
            $productCollection->getSelect()->joinInner(
                ['s' => $productCollection->getTable('cataloginventory_stock_item')],
                $productCollection->getSelect()->getConnection()->quoteInto(
                    's.product_id = e.entity_id AND s.is_in_stock = ?',
                    1,
                    \Zend_Db::INT_TYPE
                ),
                'is_in_stock'
            );
        }

        $productCollection->setPage($page, $itemsPerPage);
        //TODO ???????????//
        $model->getRule()->getConditions()->collectValidatedAttributes($productCollection);

        return $productCollection;
    }
}
