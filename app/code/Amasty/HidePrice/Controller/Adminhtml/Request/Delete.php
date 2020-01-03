<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Controller\Adminhtml\Request;


class Delete extends \Amasty\HidePrice\Controller\Adminhtml\Request
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Amasty\HidePrice\Model\RequestRepository $requestRepository,
        \Magento\Framework\Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context, $requestRepository, $coreRegistry);
        $this->logger = $logger;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->requestRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('Request was deleted.'));
                $this->_redirect('amasty_hideprice/*/');

                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Can\'t delete item right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
                $this->_redirect('amasty_hideprice/*/edit', ['id' => $id]);

                return;
            }
        }
        $this->messageManager->addErrorMessage(__('Can\'t find a item to delete.'));
        $this->_redirect('amasty_hideprice/*/');
    }
}
