<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Items\Type;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderItemInterface;
use \Magento\Sales\Block\Adminhtml\Items\AbstractItems;
use Magento\Sales\Model\Order as OrderInstance;
use Magento\Sales\Model\Order\Item as OrderItem;

/**
 * Class AbstractType
 */
class AbstractType extends AbstractItems
{
    const ITEM_TYPE_ORDER = 'order';
    const ITEM_TYPE_QUOTE = 'quote';

    /**
     * @var null|OrderInstance
     */
    protected $order = null;

    /**
     * @var null|OrderItem
     */
    protected $orderItem = null;

    /**
     * @var \Magento\Sales\Helper\Admin
     */
    protected $adminHelper;

    /**
     * @var \MageWorx\OrderEditor\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\OrderEditor\Api\TaxManagerInterface
     */
    protected $taxManager;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogHelper;

    /**
     * @var \MageWorx\OrderEditor\Model\Edit\Thumbnail
     */
    protected $thumbnailModel;

    /**
     * AbstractType constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \Magento\CatalogInventory\Model\ResourceModel\Stock\Item $itemResource
     * @param \MageWorx\OrderEditor\Helper\Data $helperData
     * @param \MageWorx\OrderEditor\Api\TaxManagerInterface $taxManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\Item $itemResource,
        \MageWorx\OrderEditor\Helper\Data $helperData,
        \MageWorx\OrderEditor\Api\TaxManagerInterface $taxManager,
        \Magento\Catalog\Helper\Data $catalogHelper,
        \MageWorx\OrderEditor\Model\Edit\Thumbnail $thumbnailModel,
        array $data = []
    ) {
        $this->adminHelper = $adminHelper;
        $this->helperData  = $helperData;
        $this->taxManager  = $taxManager;
        $this->catalogHelper = $catalogHelper;
        $this->thumbnailModel = $thumbnailModel;

        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $data);
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $item
     * @return \Magento\Catalog\Helper\Image|null
     */
    public function getImageHelper($item)
    {
        return $this->thumbnailModel->getImgByItem($item);
    }

    /**
     * @param OrderItem $orderItem
     * @return $this
     */
    public function setOrderItem($orderItem): self
    {
        $this->orderItem = $orderItem;

        return $this;
    }

    /**
     * @return OrderItem
     */
    public function getOrderItem()
    {
        return $this->orderItem;
    }

    /**
     * @param OrderInstance $order
     * @return $this
     */
    public function setOrder(OrderInstance $order): self
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return OrderInstance
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return OrderItem
     */
    public function getPriceDataObject()
    {
        return $this->getOrderItem();
    }

    /**
     * @param string $priceType
     * @return string
     */
    public function getPriceHtml(string $priceType): string
    {
        $basePrice = $this->getOrderItem()->getData('base_' . $priceType);
        $price     = $this->getOrderItem()->getData($priceType);

        return $this->adminHelper->displayPrices(
            $this->getOrder(),
            $basePrice,
            $price,
            false,
            '<br/>'
        );
    }

    /**
     * @param string $priceType
     * @return string
     */
    public function getPrice(string $priceType): string
    {
        $price = $this->getOrderItem()->getData($priceType);

        return $this->helperData->roundAndFormatPrice($price);
    }

    /**
     * @param string $percentType
     * @return string
     */
    public function getPercent(string $percentType): string
    {
        $percent = $this->getOrderItem()->getData($percentType);

        return number_format($percent, 2, '.', '');
    }

    /**
     * @param string $percentType
     * @return string
     */
    public function getPercentHtml(string $percentType): string
    {
        return $this->getPercent($percentType) . "%";
    }

    /**
     * @return string
     */
    public function getItemTotalHtml(): string
    {
        $basePrice = $this->getBaseItemTotal();
        $price     = $this->getItemTotal();

        return $this->adminHelper->displayPrices(
            $this->getOrder(),
            $basePrice,
            $price,
            false,
            '<br/>'
        );
    }

    /**
     * @return string
     */
    public function getBaseItemTotal(): string
    {
        $orderItem = $this->getOrderItem();

        $total = $orderItem->getBaseRowTotal()
            + $orderItem->getBaseTaxAmount()
            + $orderItem->getBaseWeeeTaxAppliedRowAmount()
            + $orderItem->getBaseDiscountTaxCompensationAmount()
            - $orderItem->getBaseDiscountAmount();

        return $this->helperData->roundAndFormatPrice($total);
    }

    /**
     * @return string
     */
    public function getItemTotal(): string
    {
        $orderItem = $this->getOrderItem();

        $total = $orderItem->getRowTotal()
            + $orderItem->getTaxAmount()
            + $orderItem->getWeeeTaxAppliedRowAmount()
            + $orderItem->getDiscountTaxCompensationAmount()
            - $orderItem->getDiscountAmount();

        return $this->helperData->roundAndFormatPrice($total);
    }

    /**
     * @param OrderItem|null $orderItem
     * @return float|int
     */
    public function getItemQty(OrderItem $orderItem = null): float
    {
        if ($orderItem == null) {
            $orderItem = $this->getOrderItem();
        }

        $itemQty = $orderItem->getQtyOrdered()
            - $orderItem->getQtyRefunded()
            - $orderItem->getQtyCanceled();

        return $itemQty < 0 ? 0.0 : (float)$itemQty;
    }

    /**
     * @param OrderItemInterface $item
     * @return bool
     */
    public function canShowPriceInfo(OrderItemInterface $item): bool
    {
        return true;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getConfigureButtonHtml(): string
    {
        $product = $this->getOrderItem()->getProduct();
        if (!$product) {
            return '';
        }

        $options = ['label' => __('Configure')];
        if ($product->canConfigure()) {
            $id               = $this->getPrefixId() . $this->getOrderItem()->getId();
            $options['class'] = sprintf("configure-order-item item-id-%s", $id);

            return $this->getLayout()
                        ->createBlock(\Magento\Backend\Block\Widget\Button::class)
                        ->setData($options)
                        ->setDataAttribute(['order-item-id' => $id])
                        ->toHtml();
        }

        return '';
    }

    /**
     * @return bool
     */
    public function getDefaultBackToStock(): bool
    {
        return $this->helperData->getReturnToStock();
    }

    /**
     * @return string
     */
    public function getOrderItemHtmlId(): string
    {
        return $this->getPrefixId() . $this->getOrderItem()->getItemId();
    }

    /**
     * @return string
     */
    public function getParentItemHtmlId(): string
    {
        $parentItem = $this->getOrderItem()->getParentItem();
        $parentId   = !empty($parentItem) ? $parentItem->getItemId() : 0;

        return $this->getPrefixId() . $parentId;
    }

    /**
     * @return bool
     */
    public function hasOrderItemParent(): bool
    {
        $parentItem = $this->getOrderItem()->getParentItem();

        return !empty($parentItem);
    }

    /**
     * @return string
     */
    public function getPrefixId(): string
    {
        return '';
    }

    /**
     * @return string
     */
    public function getEditedItemType(): string
    {
        return static::ITEM_TYPE_ORDER;
    }

    /**
     * @return bool
     */
    public function getCanDeleteItem(): bool
    {
        $item = $this->getOrderItem();
        if ($item->getQtyRefunded() == $item->getQtyOrdered()) {
            return false;
        }

        return true;
    }

    /**
     * Get tax rate codes (classes) for the active item
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getItemTaxRateCodes(): array
    {
        return $this->taxManager->getOrderItemTaxClasses($this->getOrderItem());
    }

    /**
     * Return all available tax rate codes (whole Magento)
     *
     * @return array
     */
    public function getTaxRatesOptions(): array
    {
        return $this->taxManager->getAllAvailableTaxRateCodes();
    }

    /**
     * Returns html of the <option> tag for the tax-rates select/multiselect tag
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function renderTaxRatesOptions(): string
    {
        $options     = $this->getTaxRatesOptions();
        $values      = $this->getItemTaxRateCodes();
        $optionsHtml = '';

        foreach ($options as $option) {
            $selected    = in_array($option['label'], $values) ? 'selected="selected"' : '';
            $optionsHtml .= '<option 
                value="' . $option['label'] . '" ' .
                'data-percent="' . round($option['percent'], 2) . '"' .
                'data-rate-id="' . $option['id'] . '"' .
                ' ' . $selected . '>' . $option['label'] . '</option>';
        }

        return $optionsHtml;
    }

    /**
     * Get tax rates applied to the order item
     *
     * @return \Magento\Tax\Model\Sales\Order\Tax[]
     * @throws NoSuchEntityException
     */
    public function getItemActiveRates(): array
    {
        $orderItem = $this->getOrderItem();
        $appliedRates = $this->taxManager->getOrderItemTaxDetails($orderItem);

        return $appliedRates;
    }

    /**
     * @return \Magento\Catalog\Helper\Data
     */
    public function getCatalogHelper(): \Magento\Catalog\Helper\Data
    {
        return $this->catalogHelper;
    }
}
