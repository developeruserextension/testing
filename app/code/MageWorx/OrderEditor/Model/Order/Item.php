<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Model\Order;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\CatalogInventory\Api\StockManagementInterface;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable
    as ConfigurableProductType;
use Magento\Downloadable\Api\Data\LinkInterface as DownloadableLinkInterface;
use Magento\Downloadable\Model\Link as DownloadableLinkModel;
use Magento\Downloadable\Model\Link\Purchased\ItemFactory as PurchasedItemFactory;
use Magento\Downloadable\Model\Link\PurchasedFactory;
use Magento\Downloadable\Model\Product\Type as DownloadableProductType;
use Magento\Downloadable\Model\ResourceModel\Link\Purchased\CollectionFactory
    as LinkPurchasedCollectionFactory;
use Magento\Downloadable\Model\ResourceModel\Link\Purchased\Item\CollectionFactory
    as LinkPurchasedItemCollectionFactory;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\Copy as DataObjectCopy;
use Magento\Framework\DB\TransactionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Sales\Model\Order as OriginalOrder;
use Magento\Sales\Model\Order\Item as OriginalOrderItem;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\OrderEditor\Api\TaxManagerInterface;
use MageWorx\OrderEditor\Helper\Data as Helper;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use MageWorx\OrderEditor\Model\Invoice as OrderEditorInvoice;
use MageWorx\OrderEditor\Model\Edit\QuoteFactory as OrderEditorQuoteFactory;
use MageWorx\OrderEditor\Api\OrderItemRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;

/**
 * Class Item
 */
class Item extends OriginalOrderItem
{
    /**
     * @var array
     */
    protected $newParams = [];

    /**
     * @var array
     */
    protected $changes = [];

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var float
     */
    protected $deltaForComparesOrders = 0.02;

    /**
     * @var mixed
     */
    protected $oldData;

    /**
     * @var PurchasedFactory
     */
    protected $purchasedFactory;

    /**
     * @var LinkPurchasedItemCollectionFactory
     */
    protected $linkPurchasedItemsCollectionFactory;

    /**
     * @var LinkPurchasedCollectionFactory
     */
    protected $linkPurchasedCollectionFactory;

    /**
     * @var DataObjectCopy
     */
    protected $objectCopyService;

    /**
     * @var DownloadableLinkModel
     */
    protected $downloadableLink;

    /**
     * @var float
     */
    protected $increasedQty;

    /**
     * @var float
     */
    protected $decreasedQty;

    /**
     * @var OrderEditorInvoice
     */
    protected $invoice;

    /**
     * @var TaxManagerInterface
     */
    protected $taxManager;

    /**
     * @var StockRegistryProviderInterface
     */
    protected $stockRegistryProvider;

    /**
     * @var StockManagementInterface
     */
    protected $stockManagement;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var CartItemRepositoryInterface
     */
    private $cartItemRepository;

    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @var OrderEditorQuoteFactory
     */
    private $orderEditorQuoteFactory;

    /**
     * @var OrderItemRepositoryInterface
     */
    private $oeOrderItemRepository;

    /**
     * @var PurchasedItemFactory
     */
    private $purchasedItemFactory;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;

    /**
     * @var StockItemRepositoryInterface
     */
    protected $stockItemRepository;

    /**
     * Item constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param OrderFactory $orderFactory
     * @param StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     * @param Helper $helper
     * @param StockManagementInterface $stockManagement
     * @param StockRegistryProviderInterface $stockRegistryProvider
     * @param PurchasedFactory $purchasedFactory
     * @param PurchasedItemFactory $purchasedItemFactory
     * @param LinkPurchasedCollectionFactory $linkPurchasedCollectionFactory
     * @param LinkPurchasedItemCollectionFactory $linkPurchasedItemsCollectionFactory
     * @param DataObjectCopy $objectCopyService
     * @param DownloadableLinkModel $downloadableLink
     * @param OrderEditorInvoice $invoice
     * @param TaxManagerInterface $taxManager
     * @param TransactionFactory $transactionFactory
     * @param CartItemRepositoryInterface $cartItemRepository
     * @param MessageManagerInterface $messageManager
     * @param OrderEditorQuoteFactory $orderEditorQuoteFactory
     * @param OrderItemRepositoryInterface $oeOrderItemRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        OrderFactory $orderFactory,
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        Helper $helper,
        StockManagementInterface $stockManagement,
        StockRegistryProviderInterface $stockRegistryProvider,
        StockItemRepositoryInterface $stockItemRepository,
        PurchasedFactory $purchasedFactory,
        PurchasedItemFactory $purchasedItemFactory,
        LinkPurchasedCollectionFactory $linkPurchasedCollectionFactory,
        LinkPurchasedItemCollectionFactory $linkPurchasedItemsCollectionFactory,
        DataObjectCopy $objectCopyService,
        DownloadableLinkModel $downloadableLink,
        OrderEditorInvoice $invoice,
        TaxManagerInterface $taxManager,
        TransactionFactory $transactionFactory,
        CartItemRepositoryInterface $cartItemRepository,
        MessageManagerInterface $messageManager,
        OrderEditorQuoteFactory $orderEditorQuoteFactory,
        OrderItemRepositoryInterface $oeOrderItemRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $orderFactory,
            $storeManager,
            $productRepository,
            $resource,
            $resourceCollection,
            $data
        );

        $this->purchasedFactory                    = $purchasedFactory;
        $this->purchasedItemFactory                = $purchasedItemFactory;
        $this->helper                              = $helper;
        $this->stockManagement                     = $stockManagement;
        $this->stockRegistryProvider               = $stockRegistryProvider;
        $this->stockItemRepository                 = $stockItemRepository;
        $this->linkPurchasedCollectionFactory      = $linkPurchasedCollectionFactory;
        $this->linkPurchasedItemsCollectionFactory = $linkPurchasedItemsCollectionFactory;
        $this->objectCopyService                   = $objectCopyService;
        $this->downloadableLink                    = $downloadableLink;
        $this->invoice                             = $invoice;
        $this->taxManager                          = $taxManager;
        $this->transactionFactory                  = $transactionFactory;
        $this->cartItemRepository                  = $cartItemRepository;
        $this->messageManager                      = $messageManager;
        $this->orderEditorQuoteFactory             = $orderEditorQuoteFactory;
        $this->oeOrderItemRepository               = $oeOrderItemRepository;
        $this->searchCriteriaBuilderFactory        = $searchCriteriaBuilderFactory;
    }

    /**
     * @param string[] $params
     * @param OriginalOrder $order
     * @return $this|OriginalOrderItem
     * @throws \Exception
     */
    public function editItem(array $params, OriginalOrder $order): OriginalOrderItem
    {
        $this->initParams($params, $order);

        if (isset($this->newParams['fact_qty'])
            && $this->newParams['fact_qty'] <= 0
        ) {
            return $this;
        }

        // remove item
        if ($this->isRemovedItem()) {
            $this->removeOrderItem();

            return $this;
        }

        // add new item
        if ($this->isNewItem()) {
            return $this->addNewOrderItem();
        }

        // edit item
        $this->editOrderItem();

        return $this;
    }

    /**
     * @param string[] $params
     * @param OriginalOrder $order
     * @return void
     */
    protected function initParams(array $params, OriginalOrder $order)
    {
        $this->newParams = $params;
        $this->setOrder($order);
    }

    /**
     * @return bool
     */
    protected function isRemovedItem(): bool
    {
        return isset($this->newParams['action'])
            && $this->newParams['action'] == 'remove';
    }

    /**
     * @return bool
     */
    public function isNewItem(): bool
    {
        return isset($this->newParams['item_type'])
            && $this->newParams['item_type'] == 'quote';
    }

    /**
     * @return void
     */
    protected function removeOrderItem()
    {
        try {
            $this->cancelInvoices();

            $this->removeDownloadablePurchasedLinks();
            $this->removeRelatedOrderItems();

            $this->reduceProductInStock(0);
            $this->removeQuoteItems($this);
            $this->oeOrderItemRepository->delete($this);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error while removing order items: %1', $e->getMessage()));

            return;
        }
    }

    /**
     * @return void
     */
    protected function removeRelatedOrderItems()
    {
        if ($this->getProductType() === ConfigurableProductType::TYPE_CODE) {
            $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
            $searchCriteriaBuilder->addFilter('parent_item_id', $this->getItemId());
            $searchCriteria = $searchCriteriaBuilder->create();
            $searchResult   = $this->oeOrderItemRepository->getList($searchCriteria);

            $simpleOrderItems = $searchResult->getItems();

            /**
             * @var Item $simpleOrderItem
             */
            foreach ($simpleOrderItems as $simpleOrderItem) {
                $this->reduceProductInStock(0, $simpleOrderItem);
                $this->removeQuoteItems($simpleOrderItem);
                try {
                    $this->oeOrderItemRepository->delete($simpleOrderItem);
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(
                        __('Error while removing related order items: %1', $e->getMessage())
                    );

                    continue;
                }
            }
        }
    }

    /**
     * @return void
     */
    protected function cancelInvoices()
    {
        $invoices = $this->invoice
            ->getCollection()
            ->addFieldToFilter('order_id', $this->getOrderId());

        /** @var $invoice OrderEditorInvoice */
        foreach ($invoices as $invoice) {
            try {
                $invoice->cancel();
                $this->transactionFactory->create()->addObject($invoice->getOrder())->save();
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Error while canceling invoices: %1', $e->getMessage()));

                continue;
            }
        }
    }

    /**
     * @return void
     * @var Item $orderItem
     */
    protected function removeQuoteItems($orderItem)
    {
        try {
            $this->cartItemRepository->deleteById(
                $this->getOrder()->getQuoteId(),
                $orderItem->getQuoteItemId()
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error while removing quote items: %1', $e->getMessage()));

            return;
        }
    }

    /**
     * @return void
     */
    protected function removeDownloadablePurchasedLinks()
    {
        if ($this->getProductType() === DownloadableProductType::TYPE_DOWNLOADABLE) {
            $purchased = $this->purchasedFactory->create()->load(
                $this->getId(),
                'order_item_id'
            );

            try {
                $purchased->delete();
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Error while removing downloadable product links: %1', $e->getMessage())
                );

                return;
            }
        }
    }

    /**
     * @return OriginalOrderItem
     * @throws LocalizedException
     */
    protected function addNewOrderItem(): OriginalOrderItem
    {
        if (!isset($this->newParams['item_id'])) {
            throw new LocalizedException(__('Item id is not defined'));
        }

        $quoteItemId = $this->newParams['item_id'];

        /** @var \MageWorx\OrderEditor\Model\Edit\Quote $converter */
        $converter = $this->orderEditorQuoteFactory->create();

        /** @var Item $orderItem */
        $orderItem = $converter->getUpdatedOrderItem($quoteItemId);

        $orderItem->setData('order_id', $this->getOrder()->getId());
        $orderItem->setData('store_id', $this->getOrder()->getStoreId());
        $qty = array_key_exists('fact_qty', $this->newParams) ? $this->newParams['fact_qty'] : 0;
        $orderItem->setData('qty_ordered', $qty);

        try {
            $this->oeOrderItemRepository->save($orderItem);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Error while adding (saving) new order item: %1', $e->getMessage())
            );
        }

        return $orderItem;
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function editOrderItem()
    {
        $this->saveOldData();
        $this->updateProductOptions();
        $this->updateQty();
        $this->updateItemData();
        $this->updateOrderTaxItemTable();

        $this->detectChangesInAmounts();
    }

    /**
     * @return void
     */
    protected function saveOldData()
    {
        $this->oldData = $this->getData();
    }

    /**
     * Compare old and new data in amounts.
     * Save the changes in the $this->changes property.
     *
     * @return void
     */
    protected function detectChangesInAmounts()
    {
        $map = [
            'row_total',
            'base_row_total',
            'tax_refunded',
            'base_tax_amount',
            'discount_amount',
            'base_discount_amount'
        ];

        $oldItem     = (clone $this)->setData($this->oldData);
        $oldRowTotal = $this->getOrderItemRowTotal($oldItem);
        $newRowTotal = $this->getOrderItemRowTotal($this);

        if (abs($oldRowTotal - $newRowTotal) >= $this->deltaForComparesOrders) {
            foreach ($map as $i) {
                if (isset($this->oldData[$i])) {
                    $this->changes[$i] = $this->oldData[$i] - $this->getData($i);
                }
            }
        }
    }

    /**
     * @return string[]
     */
    public function getChangesInAmounts()
    {
        return $this->changes;
    }

    /**
     * @return void
     */
    protected function updateProductOptions()
    {
        if (empty($this->newParams['product_options'])) {
            return;
        }

        $this->editDownloadItem();

        // options
        $productOptions = $this->newParams['product_options'];
        $this->setData('product_options', $productOptions);

        $oldSimpleSku = $this->getSku();
        $newSimpleSku = $this->updateSkuAfterUpdateOptions();

        $this->updateInventoryAfterUpdateOptions($oldSimpleSku, $newSimpleSku);

        try {
            $this->oeOrderItemRepository->save($this);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Error while saving order item: %1', $e->getMessage())
            );

            return;
        }
    }

    /**
     * @return string
     */
    protected function updateSkuAfterUpdateOptions(): string
    {
        $productOptions = $this->getData('product_options');
        $options        = $this->helper->decodeBuyRequestValue($productOptions);

        if (!empty($options['simple_sku'])) {
            $this->setSku($options['simple_sku']);
        } else {
            if (!empty($this->newParams['sku'])) {
                $this->setSku($this->newParams['sku']);
            }
        }

        if (!empty($options['simple_name'])) {
            $this->setName($options['simple_name']);
        }

        return $this->getSku();
    }

    /**
     * @param string $oldSimpleSku
     * @param string $newSimpleSku
     * @return void
     */
    protected function updateInventoryAfterUpdateOptions(
        string $oldSimpleSku,
        string $newSimpleSku
    ) {
        if ($oldSimpleSku === $newSimpleSku) {
            return;
        }

        // update product id for simple product
        try {
            $oldProductId = $this->productRepository->get($oldSimpleSku)->getId();
            $newProductId = $this->productRepository->get($newSimpleSku)->getId();

            if ($this->getProductType() === ConfigurableProductType::TYPE_CODE) {
                $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
                $searchCriteriaBuilder->addFilter('parent_item_id', $this->getItemId());
                $searchCriteria = $searchCriteriaBuilder->create();
                $searchResult   = $this->oeOrderItemRepository->getList($searchCriteria);

                $simpleOrderItems = $searchResult->getItems();
                if ($searchResult->getTotalCount() > 0) {
                    $simpleOrderItem = reset($simpleOrderItems);
                    $simpleOrderItem->getFirstItem()
                                    ->setProductId($newProductId)
                                    ->setSku($newSimpleSku)
                                    ->setName($this->getName())
                                    ->setQtyOrdered(0);
                    $this->oeOrderItemRepository->save($simpleOrderItem);
                }
            } elseif ($this->getProductType() === ProductType::TYPE_SIMPLE) {
                $this->setProductId($newProductId);
            }

            // prepare qty
            $qty = $this->getQtyOrdered() - $this->getQtyRefunded() - $this->getQtyCanceled();
            $qty = $qty < 0 ? 0 : $qty;

            // back to inventory OLD item
            $this->productToInventory($qty, $oldProductId);

            // get from inventory NEW item
            $this->setQtyOrdered(0);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Error while updating inventory after updating product options: %1', $e->getMessage())
            );

            return;
        }
    }

    /**
     * @return void
     */
    protected function editDownloadItem()
    {
        if ($this->getProductType() !== DownloadableProductType::TYPE_DOWNLOADABLE) {
            return;
        }

        $newLinks = $this->getOptionLinks($this->newParams['product_options']);
        $oldLinks = $this->getOptionLinks($this->getData('product_options'));

        $added = array_diff($newLinks, $oldLinks);
        foreach ($added as $linkId) {
            $this->addDownloadableLink($linkId);
        }

        $removed = array_diff($oldLinks, $newLinks);
        foreach ($removed as $linkId) {
            $this->removeDownloadableLink($linkId);
        }
    }

    /**
     * @TODO Check for errors with encoded value in the $productOptions , should be array-type
     *
     * @param string[] $productOptions
     * @return string[]
     */
    protected function getOptionLinks(array $productOptions): array
    {
        return isset($productOptions['links'])
            ? $productOptions['links']
            : [];
    }

    /**
     * @param int $linkId
     * @return void
     */
    protected function addDownloadableLink(int $linkId)
    {
        $linkPurchasedItem = $this->purchasedItemFactory->create();

        $linkPurchasedId = $this->getLinkPurchasedIdForOrderItem();

        $this->objectCopyService->copyFieldsetToTarget(
            'downloadable_sales_copy_link',
            'to_purchased',
            $linkId,
            $linkPurchasedItem
        );

        $hash     = microtime() . $linkPurchasedId . $this->getId() . $this->getProductId();
        $linkHash = strtr(base64_encode($hash), '+/=', '-_,');

        /**
         * @var $link DownloadableLinkModel
         */
        $link = $this->downloadableLink->getCollection()
                                       ->addTitleToResult()
                                       ->addFieldToFilter('main_table.link_id', $linkId)
                                       ->getFirstItem();

        $numberOfDownloads = $link->getNumberOfDownloads() * $this->getQtyOrdered();

        try {
            $linkPurchasedItem
                ->setPurchasedId($linkPurchasedId)
                ->setOrderItemId($this->getId())
                ->setLinkHash($linkHash)
                ->setNumberOfDownloadsBought($numberOfDownloads)
                ->setStatus(\Magento\Downloadable\Model\Link\Purchased\Item::LINK_STATUS_PENDING)
                ->setCreatedAt($this->getCreatedAt())
                ->setUpdatedAt($this->getUpdatedAt())
                ->setProductId($this->getProductId())
                ->setLinkId($link->getId())
                ->setIsShareable($link->getIsShareable())
                ->setLinkUrl($link->getLinkUrl())
                ->setLinkFile($link->getLinkFile())
                ->setLinkType($link->getLinkType())
                ->setLinkTitle($link->getDefaultTitle());
            $linkPurchasedItem->save();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Error while adding downloadable product link: %1', $e->getMessage())
            );

            return;
        }
    }

    /**
     * @return int
     */
    protected function getLinkPurchasedIdForOrderItem(): int
    {
        $collection = $this->linkPurchasedCollectionFactory
            ->create()
            ->addFieldToFilter('order_item_id', $this->getId());

        if ($collection->getSize() > 0) {
            return (int)$collection->getFirstItem()->getId();
        }

        return 0;
    }

    /**
     * @param int $linkId
     * @return void
     */
    protected function removeDownloadableLink(int $linkId)
    {
        $purchasedItems = $this->linkPurchasedItemsCollectionFactory
            ->create()
            ->addFieldToFilter('order_item_id', $this->getId())
            ->addFieldToFilter('link_id', $linkId);

        /** @var DownloadableLinkInterface|DownloadableLinkModel $link */
        foreach ($purchasedItems as $link) {
            try {
                $link->delete();
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Error while removing downloadable product link: %1', $e->getMessage())
                );

                continue;
            }
        }
    }

    /**
     * Remove taxes which not exist in $taxRates
     *
     * @param \MageWorx\OrderEditor\Model\Order\Tax\Item[] $taxItems
     * @param string[] $taxRates
     */
    protected function removeNonExistingTaxes(array $taxItems, array $taxRates = [])
    {
        /** @var \MageWorx\OrderEditor\Model\Order\Tax\Item $taxItem */
        foreach ($taxItems as $taxItem) {
            $rateCode = $taxItem->getData('code');
            if (!in_array($rateCode, $taxRates)) {
                try {
                    // Delete the tax item first, because it has reference for the tax table
                    $taxItem->delete();
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(
                        __('Error while deleting tax item: %1', $e->getMessage())
                    );

                    continue;
                }

                // Then Delete records from tax table, if exist
                $taxId = $taxItem->getTaxId();
                if ($taxId) {
                    $this->taxManager->deleteOrderTaxRecordByTaxId($taxId);
                }
            }
        }
    }

    /**
     * @param string $rateCode
     * @param \MageWorx\OrderEditor\Model\Order\Tax\Item[]  $taxItemsByCode
     * @return \MageWorx\OrderEditor\Model\Order\Tax\Item
     */
    protected function getOrderItemTaxItemByCode(
        string $rateCode,
        array $taxItemsByCode = []
    ): \MageWorx\OrderEditor\Model\Order\Tax\Item {
        if (isset($taxItemsByCode[$rateCode])) {
            $taxItem = $taxItemsByCode[$rateCode];
        } else {
            $taxItemsCollection = $this->taxManager->getOrderTaxItemsCollection();
            $taxItem = $taxItemsCollection->getNewEmptyItem();
        }

        return $taxItem;
    }

    /**
     * Get all tax items (tax code is key)
     *
     * @return \MageWorx\OrderEditor\Model\Order\Tax\Item[]
     */
    public function getTaxItems(): array
    {
        $orderItemTaxItems = $this->taxManager->getOrderItemTaxItems($this->getId());
        $orderItemTaxItemsByCode = [];

        foreach ($orderItemTaxItems as $item) {
            $orderItemTaxItemsByCode[$item->getData('code')] = $item;
        }

        return $orderItemTaxItemsByCode;
    }

    /**
     * Create/Delete/Update Tax Rates for each of Items
     *
     * @return void
     */
    protected function updateOrderTaxItemTable()
    {
        $taxRates          = $this->newParams['tax_rates'] ?? [];
        $overallTaxPercent = $this->newParams['tax_percent'] ?? 0;
        $overallTaxAmount  = $this->newParams['tax_amount'] ?? 0;

        /** @var \MageWorx\OrderEditor\Model\Order\Tax\Item[] $taxItemsByCode */
        $taxItemsByCode = $this->getTaxItems();

        $this->removeNonExistingTaxes($taxItemsByCode, $taxRates);

        foreach ($taxRates as $rateCode) {
            $taxItem = $this->getOrderItemTaxItemByCode($rateCode, $taxItemsByCode);

            $percent        = (float)$this->newParams['tax_applied_rates'][$rateCode];
            $amount         = $overallTaxPercent != 0 ? $percent / $overallTaxPercent * $overallTaxAmount : 0;
            $baseAmount     = $this->currencyConvertToBaseCurrency($amount);
            $baseRealAmount = $baseAmount;

            if ($taxItem->isObjectNew()) {
                // We should create new record in the `sales_order_tax` table before saving tax_item
                $tax = $this->taxManager->getOrderTaxCollection()->getNewEmptyItem();
                $tax->addData(
                    [
                        'order_id'         => $this->getOrderId(),
                        'code'             => $rateCode,
                        'title'            => $rateCode,
                        'percent'          => $percent,
                        'amount'           => $amount,
                        'priority'         => 0,
                        'position'         => 0,
                        'base_amount'      => $baseAmount,
                        'process'          => 0,
                        'base_real_amount' => $baseRealAmount
                    ]
                );
            } else {
                $tax = $this->taxManager->getOrderTaxItemByCodeAndOrderId($rateCode, $this->getOrderId());
                $tax->addData(
                    [
                        'percent'          => $percent,
                        'amount'           => $amount,
                        'base_amount'      => $baseAmount,
                        'base_real_amount' => $baseRealAmount
                    ]
                );
            }

            try {
                $this->taxManager->saveOrderTax($tax);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Error while saving tax: %1', $e->getMessage())
                );

                continue;
            }

            $taxId = $tax->getTaxId();

            $taxItem->addData(
                [
                    'tax_id'            => $taxId,
                    'item_id'           => $this->getItemId(),
                    'tax_percent'       => $percent,
                    'amount'            => $amount,
                    'base_amount'       => $baseAmount,
                    'real_amount'       => $amount,
                    'real_base_amount'  => $baseAmount,
                    'taxable_item_type' => 'product'
                ]
            );

            try {
                $this->taxManager->saveOrderTaxItem($taxItem);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Error while saving tax item: %1', $e->getMessage())
                );

                continue;
            }
        }
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function updateQty()
    {
        // qty ordered
        $oldQtyOrdered = $this->getQtyOrdered() - $this->getQtyRefunded();
        $newQty        = isset($this->newParams['fact_qty'])
            ? $this->newParams['fact_qty']
            : $oldQtyOrdered;

        if ($this->getProductType() === ConfigurableProductType::TYPE_CODE) {
            $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
            $searchCriteriaBuilder->addFilter('parent_item_id', $this->getItemId());
            $searchCriteria = $searchCriteriaBuilder->create();
            $searchResult   = $this->oeOrderItemRepository->getList($searchCriteria);

            $simpleOrderItems = $searchResult->getItems();
            if ($searchResult->getTotalCount() > 0) {
                /** @var Item $simpleOrderItem */
                $simpleOrderItem = reset($simpleOrderItems);
                $this->updateQtyProduct($newQty, $simpleOrderItem);
            }
        }
        $this->updateQtyProduct($newQty, $this);
    }

    /**
     * @param float $newQty
     * @param Item $item
     * @return float
     * @throws \Exception
     */
    protected function updateQtyProduct(float $newQty, Item $item): float
    {
        if ($item->getQtyOrdered() > $newQty) {
            /* product was removed */
            $this->decreasedQty = $this->reduceProductInStock($newQty, $item);
        } else {
            /* product was added */
            $this->increasedQty = $this->increaseProductInStock($newQty, $item);
        }

        $item->setQtyOrdered($newQty);
        $qty       = $newQty - $item->getQtyRefunded();
        $rowWeight = $item->getWeight() * $qty;
        $item->setRowWeight($rowWeight);
        try {
            $this->oeOrderItemRepository->save($item);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Error while updating product qty: %1', $e->getMessage())
            );

            throw $e;
        }

        return (float)$newQty;
    }

    /**
     * @param float $newQty
     * @param Item $item
     * @return float
     */
    protected function reduceProductInStock(float $newQty, Item $item = null): float
    {
        if ($item === null) {
            $item = $this;
        }

        $qty = $item->getQtyOrdered() - $newQty - $item->getQtyRefunded();

        if ($qty > 0 && $this->getAllowReturnToStock()) {
            $this->productToInventory($qty, $item->getProductId());
        }

        return (float)$qty;
    }

    /**
     * @param float $qty
     * @param int $productId
     */
    protected function productToInventory(float $qty, int $productId)
    {
        $this->stockManagement->backItemQty($productId, $qty, $this->getStore()->getWebsiteId());
    }

    /**
     * @return bool
     */
    protected function getAllowReturnToStock(): bool
    {
        if (isset($this->newParams['back_to_stock'])) {
            return $this->newParams['back_to_stock'] ? true : false;
        }

        return false;
    }

    /**
     * Increase Product In Stock
     *
     * @param float $newQty
     * @param Item $item
     * @return float
     */
    protected function increaseProductInStock(float $newQty, Item $item): float
    {
        $qty = $newQty - ($item->getQtyOrdered());
        $qty = $qty < 0 ? 0 : $qty;

        if ($qty != 0) {
            $this->productFromInventory($qty, $item->getProductId());
        }

        return (float)$qty;
    }

    /**
     * Remove Product From Inventory
     *
     * @param float $qty
     * @param int $productId
     * @return bool
     */
    protected function productFromInventory(float $qty, int $productId): bool
    {
        $websiteId = $this->getStore()->getWebsiteId();
        $stockItem = $this->stockRegistryProvider
            ->getStockItem($productId, $websiteId);

        $stockItem->setQty($stockItem->getQty() - $qty);

        try {
            $this->stockItemRepository->save($stockItem);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Error while removing product from inventory: %1', $e->getMessage())
            );

            return false;
        }

        return true;
    }

    /**
     * Update Item Data
     *
     * @return void
     * @throws \Exception
     */
    protected function updateItemData()
    {
        // description
        if (isset($this->newParams['description'])) {
            $this->setDescription($this->newParams['description']);
        }

        // tax amount
        if (isset($this->newParams['tax_amount'])) {
            $taxAmount     = (float)$this->newParams['tax_amount'];
            $baseTaxAmount = $this->currencyConvertToBaseCurrency($taxAmount);

            $this->setBaseTaxAmount($baseTaxAmount)
                 ->setTaxAmount($taxAmount)
                 ->setBaseTaxInvoiced(0)
                 ->setTaxInvoiced(0);
        }

        // discount tax compensation amount
        if (isset($this->newParams['discount_tax_compensation_amount'])) {
            $hiddenTax     = (float)$this->newParams['discount_tax_compensation_amount'];
            $baseHiddenTax = $this->currencyConvertToBaseCurrency($hiddenTax);

            $this->setBaseDiscountTaxCompensationAmount($baseHiddenTax)
                 ->setDiscountTaxCompensationAmount($hiddenTax);
        }

        // tax percent
        if (isset($this->newParams['tax_percent'])) {
            $this->setTaxPercent($this->newParams['tax_percent']);
        }

        // price
        if (isset($this->newParams['price'])) {
            $price     = (float)$this->newParams['price'];
            $basePrice = $this->currencyConvertToBaseCurrency($price);

            $this->setBasePrice($basePrice)
                 ->setPrice($price);
        }

        // Price includes tax
        if (isset($this->newParams['price_incl_tax'])) {
            $priceInclTax     = (float)$this->newParams['price_incl_tax'];
            $basePriceInclTax = $this->currencyConvertToBaseCurrency($priceInclTax);

            $this->setBasePriceInclTax($basePriceInclTax)
                 ->setPriceInclTax($priceInclTax);
        }

        // discount amount
        if (isset($this->newParams['discount_amount'])) {
            $discountAmount     = (float)$this->newParams['discount_amount'];
            $baseDiscountAmount = $this->currencyConvertToBaseCurrency($discountAmount);

            $this->setBaseDiscountAmount($baseDiscountAmount)
                 ->setDiscountAmount($discountAmount)
                 ->setBaseDiscountInvoiced(0)
                 ->setDiscountInvoiced(0);
        }

        // discount percent
        if (isset($this->newParams['discount_percent'])) {
            $this->setDiscountPercent($this->newParams['discount_percent']);
        }

        // subtotal (row total)
        if (isset($this->newParams['subtotal'])) {
            $currentSubtotal           = (float)$this->newParams['subtotal'];
            $baseCurrencySubtotal      = $this->currencyConvertToBaseCurrency($currentSubtotal);
            $roundBaseCurrencySubtotal = $this->helper->roundAndFormatPrice($baseCurrencySubtotal);

            $this->setBaseRowTotal($roundBaseCurrencySubtotal)
                 ->setRowTotal($currentSubtotal)
                 ->setBaseRowInvoiced(0)
                 ->setRowInvoiced(0);
        }

        // Subtotal includes tax
        if (isset($this->newParams['subtotal_incl_tax'])) {
            $subtotalInclTax                  = (float)$this->newParams['subtotal_incl_tax'];
            $baseCurrencySubtotalInclTax      = $this->currencyConvertToBaseCurrency($subtotalInclTax);
            $roundBaseCurrencySubtotalInclTax = $this->helper->roundAndFormatPrice($baseCurrencySubtotalInclTax);

            $this->setBaseRowTotalInclTax($roundBaseCurrencySubtotalInclTax)
                 ->setRowTotalInclTax($subtotalInclTax);
        }

        try {
            $this->oeOrderItemRepository->save($this);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Error while updating item data: %1', $e->getMessage())
            );

            throw $e;
        }
    }

    /**
     * Convert store amount to base amount
     *
     * @param float $amount
     * @return float
     */
    protected function currencyConvertToBaseCurrency($amount): float
    {
        $rate       = (float)$this->_order->getBaseToOrderRate();
        $rate       = $rate > 0 ? $rate : 1;
        $baseAmount = $amount / $rate;

        return (float)$baseAmount;
    }

    /**
     * Getter decreasedQty
     *
     * @return float
     */
    public function getDecreasedQty()
    {
        return $this->decreasedQty;
    }

    /**
     * Getter increasedQty
     *
     * @return float
     */
    public function getIncreasedQty()
    {
        return $this->increasedQty;
    }

    /**
     * Get Order Item Row Total
     *
     * @param Item|null $item
     * @return float
     */
    protected function getOrderItemRowTotal(Item $item = null): float
    {
        if ($item === null) {
            $item = $this;
        }

        return $item->getRowTotal()
            + $item->getTaxAmount()
            + $item->getWeeeTaxAppliedRowAmount()
            - $item->getDiscountAmount();
    }
}
