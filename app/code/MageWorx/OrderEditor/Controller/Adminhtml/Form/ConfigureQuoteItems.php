<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Controller\Adminhtml\Form;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Catalog\Helper\Product\Composite as CompositeProductHelper;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\Layout;
use Magento\Framework\View\Result\PageFactory;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Model\ResourceModel\Quote\Item\Option\CollectionFactory as QuoteItemOptionCollectionFactory;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use MageWorx\OrderEditor\Model\Quote\Item;
use Magento\Sales\Controller\Adminhtml\Order\Create;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Quote\Api\Data\CartItemInterface;

/**
 * Class ConfigureQuoteItems
 */
class ConfigureQuoteItems extends Create
{
    /**
     * @var CompositeProductHelper
     */
    protected $productCompositeHelper;

    /**
     * @var OrderItemRepositoryInterface
     */
    protected $orderItemRepository;

    /**
     * @var CartItemRepositoryInterface
     */
    protected $cartItemRepository;

    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var QuoteItemOptionCollectionFactory
     */
    protected $quoteItemOptionCollectionFactory;

    /**
     * ConfigureQuoteItems constructor.
     *
     * @param Action\Context $context
     * @param ProductHelper $productHelper
     * @param Escaper $escaper
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param CompositeProductHelper $productCompositeHelper
     */
    public function __construct(
        Action\Context $context,
        ProductHelper $productHelper,
        Escaper $escaper,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        CompositeProductHelper $productCompositeHelper,
        OrderItemRepositoryInterface $orderItemRepository,
        CartItemRepositoryInterface $cartItemRepository,
        DataObjectFactory $dataObjectFactory,
        QuoteItemOptionCollectionFactory $quoteItemOptionCollectionFactory
    ) {
        $this->productCompositeHelper           = $productCompositeHelper;
        $this->orderItemRepository              = $orderItemRepository;
        $this->cartItemRepository               = $cartItemRepository;
        $this->dataObjectFactory                = $dataObjectFactory;
        $this->quoteItemOptionCollectionFactory = $quoteItemOptionCollectionFactory;
        parent::__construct(
            $context,
            $productHelper,
            $escaper,
            $resultPageFactory,
            $resultForwardFactory
        );
    }

    /**
     * @return Layout
     */
    public function execute(): Layout
    {
        // Prepare data
        $configureResult = $this->dataObjectFactory->create();
        try {
            $quoteItem   = $this->getQuoteItem();
            $quoteItemId = $quoteItem->getItemId();

            $configureResult->setOk(true);

            $optionsCollection = $this->quoteItemOptionCollectionFactory->create();
            $options           = $optionsCollection
                ->addItemFilter([$quoteItemId])
                ->getOptionsByItem($quoteItem);
            $quoteItem->setOptions($options);

            $configureResult->setBuyRequest($quoteItem->getBuyRequest());
            $configureResult->setCurrentStoreId($quoteItem->getStoreId());
            $configureResult->setProductId($quoteItem->getProductId());
        } catch (\Exception $e) {
            $configureResult->setError(true);
            $configureResult->setMessage($e->getMessage());
        }

        return $this->productCompositeHelper
            ->renderConfigureResult($configureResult);
    }

    /**
     * @return CartItemInterface
     * @throws LocalizedException
     */
    protected function getQuoteItem(): CartItemInterface
    {
        $orderItemId = (int)$this->getRequest()->getParam('id');
        if (!$orderItemId) {
            throw new LocalizedException(__('Order item id is not received.'));
        }

        $prefixIdLength = strlen(Item::PREFIX_ID);
        if (substr($orderItemId, 0, $prefixIdLength) == Item::PREFIX_ID) {
            $quoteItemId = substr(
                $orderItemId,
                $prefixIdLength,
                strlen($orderItemId)
            );
        } else {
            $orderItem   = $this->loadOrderItem($orderItemId);
            $quoteId     = (int)$orderItem->getOrder()->getQuoteId();
            $quoteItemId = (int)$orderItem->getQuoteItemId();
        }

        return $this->loadQuoteItem($quoteId, $quoteItemId);
    }

    /**
     * @param int $quoteId
     * @param int $quoteItemId
     * @return CartItemInterface
     * @throws NoSuchEntityException
     */
    protected function loadQuoteItem(
        int $quoteId,
        int $quoteItemId
    ): CartItemInterface {
        $quoteItems = $this->cartItemRepository->getList($quoteId);
        foreach ($quoteItems as $quoteItem) {
            if ($quoteItem->getItemId() == $quoteItemId) {
                return $quoteItem;
            }
        }

        throw new NoSuchEntityException(__('Quote item is not loaded.'));
    }

    /**
     * @param int $orderItemId
     * @return OrderItemInterface|\Magento\Sales\Model\Order\Item
     * @throws NoSuchEntityException
     */
    protected function loadOrderItem(int $orderItemId): OrderItemInterface
    {
        /** @var \Magento\Sales\Model\Order\Item $orderItem */
        $orderItem = $this->orderItemRepository->get($orderItemId);

        if (!$orderItem->getId()) {
            throw new NoSuchEntityException(__('Order item is not loaded.'));
        }

        return $orderItem;
    }
}
