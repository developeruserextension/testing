<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Checkout
 */


namespace Amasty\Checkout\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\View\Layout\BuilderFactory as LayoutBuilderFactory;
use Magento\Framework\View\LayoutFactory;
use Magento\Quote\Model\Quote;
use Magento\Downloadable\Model\Product\Type as DownloadableType;
use Magento\Framework\Registry;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Quote\Model\Quote\Item as QuoteItem;

/**
 * Class Item
 */
class Item extends AbstractHelper
{
    /**
     * @var LayoutInterface
     */
    protected $layout = null;

    /**
     * @var LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var LayoutBuilderFactory
     */
    protected $layoutBuilderFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        Context $context,
        LayoutFactory $layoutFactory,
        LayoutBuilderFactory $layoutBuilderFactory,
        Registry $registry,
        ObjectManagerInterface $objectManager
    ) {
        parent::__construct($context);
        $this->layoutFactory = $layoutFactory;
        $this->layoutBuilderFactory = $layoutBuilderFactory;
        $this->registry = $registry;
        $this->objectManager = $objectManager;
    }

    /**
     * @param Quote $quote
     * @param QuoteItem|int $item
     *
     * @return array
     */
    public function getItemOptionsConfig(Quote $quote, $item)
    {
        /** @var \Magento\Catalog\Block\Product\View\Options $optionsBlock */
        $optionsBlock = $this->getLayout()->getBlock('amcheckout.options.prototype');

        $quoteItem = is_object($item) ? $item : $quote->getItemById($item);

        $additionalConfig = [];

        $product = $quoteItem->getProduct();

        $product->setPreconfiguredValues(
            $product->processBuyRequest($quoteItem->getBuyRequest())
        );

        // Fix issue in vendor/magento/module-tax/Observer/GetPriceConfigurationObserver.php
        $oldRegistryProduct = $this->registry->registry('current_product');

        if ($oldRegistryProduct) {
            $this->registry->unregister('current_product');
        }

        $this->registry->register('current_product', $product);

        if ($quoteItem->getData('product_type') == 'configurable') {
            $additionalConfig['configurableAttributes'] = $this->getConfigurableAttributesConfig($quoteItem, $product);
        }

        if ($quoteItem->getProduct()->getOptions()) {
            $optionsBlock->setProduct($product);

            $customOptionsConfig = [
                'template' => $optionsBlock->toHtml(),
                'optionConfig' => $optionsBlock->getJsonConfig()
            ];

            $additionalConfig['customOptions'] = $customOptionsConfig;
        }

        if ($quoteItem->getProductType() == DownloadableType::TYPE_DOWNLOADABLE) {
            $additionalConfig['customOptions'] = $this->getDownloadableCustomOptionsConfig($quoteItem);
        }

        if ($quoteItem->getProductType() == \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
            $additionalConfig['customOptions'] = $this->getBundleCustomOptionsConfig($quoteItem);
        }

        if ($quoteItem->getProductType() == 'giftcard') {
            $additionalConfig['customOptions'] = $this->getGiftCardCustomOptionsConfig($quoteItem);
        }

        $this->registry->unregister('current_product');
        if ($oldRegistryProduct) {
            $this->registry->register('current_product', $oldRegistryProduct);
        }

        return $additionalConfig;
    }

    /**
     * @param Quote $quote
     * @param QuoteItem|int $item
     * @return mixed|string
     */
    public function getItemCustomStockStatus(Quote $quote, $item)
    {
        $stockStatus = '';
        $quoteItem = is_object($item) ? $item : $quote->getItemById($item);
        $product = $quoteItem->getProduct();

        /** @var \Amasty\Stockstatus\Helper\Data $stockStatusHelper */
        $stockStatusHelper = $this->objectManager->create(\Amasty\Stockstatus\Helper\Data::class);

        if ($stockStatusHelper) {
            $stockStatus = $stockStatusHelper->getProductStockStatus($product, $quoteItem);
        }

        return $stockStatus;
    }

    /**
     * @return LayoutInterface
     */
    protected function getLayout()
    {
        if ($this->layout === null) {
            $layout = $this->layoutFactory->create();

            $this->layoutBuilderFactory->create(
                LayoutBuilderFactory::TYPE_LAYOUT,
                ['layout' => $layout]
            );
            $layout->getUpdate()->addHandle(['default', 'amasty_checkout_prototypes']);

            /** @var \Magento\Framework\View\Element\AbstractBlock $block */
            foreach ($layout->getAllBlocks() as $block) {
                $block->setData('area', 'frontend');
            }

            $this->layout = $layout;
        }

        return $this->layout;
    }

    /**
     * @param QuoteItem $quoteItem
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return array
     */
    private function getConfigurableAttributesConfig(QuoteItem $quoteItem, $product)
    {
        $buyRequest = $quoteItem->getBuyRequest();

        /** @var \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $configurableAttributesBlock */
        $configurableAttributesBlock = $this->getLayout()->getBlock('amcheckout.super.prototype');

        $configurableAttributesBlock->unsetData('allow_products');
        $configurableAttributesBlock->addData([
            'product' => $product,
            'quote_item' => $quoteItem
        ]);

        $configurableAttributesConfig = [
            'selectedAttributes' => $buyRequest['super_attribute'],
            'template' => $configurableAttributesBlock->toHtml(),
            'spConfig' => $configurableAttributesBlock->getJsonConfig(),
        ];

        return $configurableAttributesConfig;
    }

    /**
     * @param QuoteItem $quoteItem
     *
     * @return array
     */
    private function getDownloadableCustomOptionsConfig(QuoteItem $quoteItem)
    {
        /** @var \Magento\Downloadable\Block\Checkout\Cart\Item\Renderer $downloadableBlock */
        $downloadableBlock = $this->getLayout()->getBlock('amcheckout.downloadable.prototype');
        $downloadableBlock->setItem($quoteItem);

        $customOptionsConfig = [
            'template' => $downloadableBlock->toHtml(),
            'optionConfig' => null
        ];

        return $customOptionsConfig;
    }

    /**
     * @param QuoteItem $quoteItem
     *
     * @return array
     */
    private function getBundleCustomOptionsConfig(QuoteItem $quoteItem)
    {
        /** @var \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle $bundleBlock */
        $bundleBlock = $this->getLayout()->getBlock('amcheckout.bundle.prototype');
        $bundleBlock->setProduct($quoteItem->getProduct());
        $bundleBlock->setItem($quoteItem);
        $bundleBlock->getOptions(true);

        $customOptionsConfig = [
            'template' => $bundleBlock->toHtml(),
            'optionConfig' => $bundleBlock->getJsonConfig()
        ];

        return $customOptionsConfig;
    }

    /**
     * @param QuoteItem $quoteItem
     *
     * @return array
     */
    private function getGiftCardCustomOptionsConfig(QuoteItem $quoteItem)
    {
        if (!$giftCardBlock = $this->getLayout()->getBlock('amcheckout.giftcard.prototype')) {
            $giftCardBlock = $this->getLayout()->createBlock(
                \Magento\GiftCard\Block\Catalog\Product\View\Type\Giftcard::class,
                'amcheckout.giftcard.prototype'
            );
        }

        $giftCardBlock->setTemplate('Amasty_Checkout::product/view/type/options/giftcard.phtml');
        $giftCardBlock->setItem($quoteItem);
        $giftCardBlock->setProduct($quoteItem->getProduct());

        $customOptionsConfig = [
            'template' => $giftCardBlock->toHtml(),
            'optionConfig' => null
        ];

        return $customOptionsConfig;
    }
}
