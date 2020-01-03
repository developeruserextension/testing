<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Controller\Adminhtml\Request;

use Amasty\HidePrice\Model\Source\Status;

class Send extends \Amasty\HidePrice\Controller\Adminhtml\Request
{
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;
    /**
     * @var \Amasty\HidePrice\Helper\Data
     */
    private $helper;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Amasty\HidePrice\Model\RequestRepository $requestRepository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Amasty\HidePrice\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context, $requestRepository, $coreRegistry);
        $this->transportBuilder = $transportBuilder;
        $this->helper = $helper;
        $this->storeManager = $storeManager;
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('request_id');

        $message = $this->getRequest()->getParam('email_text');
        if (!\Zend_Validate::is(trim($message), 'NotEmpty')) {
            $this->messageManager->addErrorMessage(__('Please enter a Email Text.'));
            $this->_redirect('amasty_hideprice/edit/*');
            return;
        }

        if ($id) {
            try {
                $model = $this->requestRepository->get($id);

                $emailTo = $model->getEmail();
                $sender = $this->helper->getModuleConfig('general/sender');
                $template = $this->helper->getModuleConfig('general/template');
                if ($this->sendEmail($model, $sender, $emailTo, $template, $message)) {
                    $model->setStatus(Status::ANSWERED);
                    $model->setMessageText($message);
                    $this->requestRepository->save($model);
                    $this->messageManager->addSuccessMessage(__('Email Answer was sent.'));
                }
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This request no longer exists.'));
            }
        } else {
            $this->messageManager->addErrorMessage(__('Please select request id.'));
        }

        $this->_redirect('amasty_hideprice/*/');
    }

    private function sendEmail(\Amasty\HidePrice\Model\Request $model, $sender, $emailTo, $template, $message)
    {
        try {
            $store = $this->storeManager->getStore($model->getStoreId());
            $data =  [
                'website_name'  => $store->getWebsite()->getName(),
                'group_name'    => $store->getGroup()->getName(),
                'store_name'    => $store->getName(),
                'request'       => $model,
                'message'       => $message,
                'customer_name' => $model->getName()
            ];

            $transport = $this->transportBuilder->setTemplateIdentifier(
                $template
            )->setTemplateOptions(
                ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $store->getId()]
            )->setTemplateVars(
                $data
            )->setFrom(
                $sender
            )->addTo(
                $emailTo,
                $model->getName()
            )->getTransport();

            $transport->sendMessage();
            return true;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return false;
        }
    }
}
