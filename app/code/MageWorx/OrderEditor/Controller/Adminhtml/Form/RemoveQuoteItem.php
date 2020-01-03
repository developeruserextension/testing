<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Controller\Adminhtml\Form;

use Magento\Framework\Controller\Result\Json as ResultJson;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\CartItemInterface;
use MageWorx\OrderEditor\Model\Quote\Item;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use MageWorx\OrderEditor\Api\QuoteItemRepositoryInterface as OrderEditorQuoteItemRepository;

/**
 * Class RemoveQuoteItem
 */
class RemoveQuoteItem extends Action
{
    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var OrderEditorQuoteItemRepository
     */
    protected $quoteItemRepository;

    /**
     * RemoveQuoteItem constructor.
     *
     * @param Context $context
     * @param DataObjectFactory $dataObjectFactory
     * @param OrderEditorQuoteItemRepository $quoteItemRepository
     */
    public function __construct(
        Context $context,
        DataObjectFactory $dataObjectFactory,
        OrderEditorQuoteItemRepository $quoteItemRepository
    ) {
        parent::__construct($context);

        $this->dataObjectFactory   = $dataObjectFactory;
        $this->quoteItemRepository = $quoteItemRepository;
    }

    /**
     * @return ResultJson
     */
    public function execute(): ResultJson
    {
        try {
            $response = [
                'result' => $this->prepareResultHtml(),
                'status' => true
            ];
        } catch (\Exception $e) {
            $response = [
                'error'  => $e->getMessage(),
                'status' => false
            ];
        }

        $updateResult = $this->dataObjectFactory->create(['data' => $response]);
        /** @var ResultJson $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        return $resultJson->setData($updateResult);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function prepareResultHtml(): string
    {
        $quoteItem = $this->quoteItemRepository->getById($this->getQuoteItemId());

        $quoteItems = $quoteItem->getChildren();
        /** @var CartItemInterface $item */
        foreach ($quoteItems as $item) {
            $this->quoteItemRepository->delete($item);
        }

        $this->quoteItemRepository->delete($quoteItem);

        return 'true';
    }

    /**
     * Get real quote item id without prefix
     *
     * @return int
     * @throws LocalizedException
     */
    protected function getQuoteItemId(): int
    {
        $quoteItemId = $this->getRequest()->getParam('id', 0);
        if (!$quoteItemId) {
            throw new LocalizedException(
                __('Quote item id is not received.')
            );
        }

        $prefixIdLength = strlen(Item::PREFIX_ID);
        if (substr($quoteItemId, 0, $prefixIdLength) == Item::PREFIX_ID) {
            $quoteItemId = substr($quoteItemId, $prefixIdLength, strlen($quoteItemId));
        }

        return (int)$quoteItemId;
    }
}
