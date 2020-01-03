<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\File\DataProvider;

use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Amasty\ProductAttachment\Utils\Price;

class FormProductDetails
{
    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var Image
     */
    private $imageHelper;

    /**
     * @var Price
     */
    private $priceModifier;

    public function __construct(
        ProductCollectionFactory $productCollectionFactory,
        Image $imageHelper,
        Price $priceModifier
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->imageHelper = $imageHelper;
        $this->priceModifier = $priceModifier;
    }

    /**
     * @param array $fileData
     */
    public function addProductDetails(&$fileData)
    {
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addIdFilter($fileData['product_ids'])
            ->addAttributeToSelect(['status', 'thumbnail', 'name', 'price'], 'left');

        $fileData['fileproducts']['products'] = [];
        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        foreach ($productCollection->getItems() as $product) {
            $fileData['fileproducts']['products'][] = $this->fillProductData($product);
        }
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     *
     * @return array
     */
    private function fillProductData(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        return [
            'entity_id' => $product->getId(),
            'thumbnail' => $this->imageHelper->init($product, 'product_listing_thumbnail')->getUrl(),
            'name' => $product->getName(),
            'status' => $product->getStatus(),
            'type_id' => $product->getTypeId(),
            'sku' => $product->getSku(),
            'price' => $product->getPrice() ? $this->priceModifier->toDefaultCurrency($product->getPrice()) : ''
        ];
    }
}
