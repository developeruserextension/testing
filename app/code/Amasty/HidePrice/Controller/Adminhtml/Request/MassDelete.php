<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Controller\Adminhtml\Request;

class MassDelete extends \Amasty\HidePrice\Controller\Adminhtml\Request
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \Amasty\HidePrice\Model\ResourceModel\Request\CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Amasty\HidePrice\Model\RequestRepository $requestRepository,
        \Magento\Framework\Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger,
        \Amasty\HidePrice\Model\ResourceModel\Request\CollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $requestRepository, $coreRegistry);
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $requestIds = $this->getRequest()->getParam('request_ids');
        if (!is_array($requestIds)) {
            $this->messageManager->addErrorMessage(__('Please select items.'));
        } else {
            try {
                /** @var \Amasty\HidePrice\Model\ResourceModel\Request\Collection $collection */
                $collection = $this->collectionFactory->create();
                $collection->deleteByIds($requestIds);

                $this->messageManager->addSuccessMessage(__('Get a Quote requests are deleted.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Can\'t delete items right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
            }
        }
        $this->_redirect('amasty_hideprice/*/');
    }
}
