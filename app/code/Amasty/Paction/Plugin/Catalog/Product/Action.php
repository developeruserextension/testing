<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */

namespace Amasty\Paction\Plugin\Catalog\Product;

use Amasty\Paction\Block\Adminhtml\Product\Edit\Action\Attribute\Tab\TierPrice as TierPriceBlock;
use Magento\Catalog\Api\Data\ProductTierPriceExtensionFactory;
use Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Config\Source\ProductPriceOptionsInterface;
use Magento\Catalog\Model\Product\Action as ProductAction;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Pricing\Price\TierPrice;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;

class Action
{
    /**
     * @var \Amasty\Paction\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    private $collection;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $adapter;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ProductTierPriceInterfaceFactory
     */
    private $productTierPriceInterfaceFactory;

    /**
     * @var ProductTierPriceExtensionFactory
     */
    private $productTierPriceExtensionFactory;

    public function __construct(
        \Amasty\Paction\Helper\Data $helper,
        CollectionFactory $collectionFactory,
        ResourceConnection $resourceConnection,
        RequestInterface $request,
        ProductRepositoryInterface $productRepository,
        ProductTierPriceInterfaceFactory $productTierPriceInterfaceFactory,
        ProductTierPriceExtensionFactory $productTierPriceExtensionFactory
    ) {
        $this->helper = $helper;
        $this->collection = $collectionFactory->create();
        $this->adapter = $resourceConnection->getConnection();
        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->productTierPriceInterfaceFactory = $productTierPriceInterfaceFactory;
        $this->productTierPriceExtensionFactory = $productTierPriceExtensionFactory;
    }

    /**
     * @param ProductAction $subject
     * @param array $productIds
     * @param array $attrData
     * @param int $storeId
     *
     * @return array
     */
    public function beforeUpdateAttributes(ProductAction $subject, $productIds, $attrData, $storeId)
    {
        $isNeedDeletePrices = $this->request->getParam(TierPriceBlock::TIER_PRICE_CHANGE_CHECKBOX_NAME);

        if ((array_key_exists(TierPrice::PRICE_CODE, $attrData) && $attrData[TierPrice::PRICE_CODE])
            || $isNeedDeletePrices
        ) {
            foreach ($productIds as $productId) {
                $product = $this->productRepository->getById($productId);
                $product->setMediaGalleryEntries($product->getMediaGalleryEntries());
                $product->setTierPrices($this->prepareTierPrices($isNeedDeletePrices ? [] : $attrData));
                $this->productRepository->save($product);
            }

            unset($attrData[TierPrice::PRICE_CODE]);
        }

        return [$productIds, $attrData, $storeId];
    }

    /**
     * @param array $tierPriceDataArray
     *
     * @return array
     */
    private function prepareTierPrices(array $tierPriceDataArray)
    {
        $result = [];

        if ($tierPriceDataArray
            && array_key_exists(TierPrice::PRICE_CODE, $tierPriceDataArray)
            && $tierPriceDataArray[TierPrice::PRICE_CODE]
        ) {
            foreach ($tierPriceDataArray[TierPrice::PRICE_CODE] as $item) {
                if (!$item['price_qty']) {
                    continue;
                }

                $isPercentValue = $item['value_type'] == ProductPriceOptionsInterface::VALUE_PERCENT;
                $tierPriceExtensionAttribute = $this->productTierPriceExtensionFactory->create()
                    ->setWebsiteId($item['website_id']);

                if ($isPercentValue) {
                    $tierPriceExtensionAttribute->setPercentageValue($item['price']);
                }

                $result[] = $this->productTierPriceInterfaceFactory
                    ->create()
                    ->setCustomerGroupId($item['cust_group'])
                    ->setQty($item['price_qty'])
                    ->setValue(!$isPercentValue ? $item['price'] : '')
                    ->setExtensionAttributes($tierPriceExtensionAttribute);
            }
        }

        return $result;
    }

    /**
     * Update product has weight status
     *
     * @param ProductAction $object
     * @param ProductAction $result
     *
     * @return ProductAction
     */
    public function afterUpdateAttributes(ProductAction $object, ProductAction $result)
    {
        $entityIdName = $this->helper->getEntityNameDependOnEdition();
        if (array_key_exists(ProductInterface::WEIGHT, $object->getAttributesData())) {
            $productIds = implode(',', $object->getProductIds());
            $this->collection->addFilter($entityIdName, $productIds);

            foreach ($this->collection as $product) {
                if ($product->getTypeId() == Type::TYPE_VIRTUAL) {
                    $this->adapter->insertOnDuplicate(
                        $this->collection->getMainTable(),
                        [
                            $entityIdName => $product->getEntityId(),
                            'type_id'   => Type::TYPE_SIMPLE
                        ],
                        [$entityIdName, 'type_id']
                    );
                }
            }
        }

        return $result;
    }
}
