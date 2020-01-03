<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;
use Amasty\ProductAttachment\Api\IconRepositoryInterface;
use Amasty\ProductAttachment\Model\Icon\ResourceModel\CollectionFactory;
use Amasty\ProductAttachment\Api\Data\IconInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractIconMassAction extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Amasty_ProductAttachment::icon';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $iconCollectionFactory;

    /**
     * @var IconRepositoryInterface
     */
    protected $repository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        IconRepositoryInterface $repository,
        CollectionFactory $iconCollectionFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->repository = $repository;
        $this->iconCollectionFactory = $iconCollectionFactory;
        $this->logger = $logger;
    }

    /**
     * Execute action for icon
     *
     * @param IconInterface $icon
     */
    abstract protected function itemAction(IconInterface $icon);

    /**
     * Mass action execution
     */
    public function execute()
    {
        $this->filter->applySelectionOnTargetProvider(); // compatibility with Mass Actions on Magento 2.1.0
        /** @var \Amasty\ProductAttachment\Model\Icon\ResourceModel\Collection $collection */
        $collection = $this->filter->getCollection($this->iconCollectionFactory->create());

        $collectionSize = $collection->getSize();
        if ($collectionSize) {
            try {
                foreach ($collection->getItems() as $icon) {
                    $this->itemAction($icon);
                }
                $this->messageManager->addSuccessMessage($this->getSuccessMessage($collectionSize));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($this->getErrorMessage());
                $this->logger->critical($e);
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage()
    {
        return __('We can\'t change item right now. Please review the log and try again.');
    }

    /**
     * @param int $collectionSize
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        if ($collectionSize) {
            return __('A total of %1 record(s) have been changed.', $collectionSize);
        }
        return __('No records have been changed.');
    }
}
