<?php

namespace Feefo\Reviews\Controller\Adminhtml\System;

use Feefo\Reviews\Service\UninstallPluginService;
use Magento\Backend\App\Action as BackendAction;
use Magento\Backend\App\Action\Context as ActionContext;
use Magento\Backend\Model\View\Result\Redirect;

/**
 * Class UninstallPlugin
 */
class UninstallPlugin extends BackendAction
{
    /**
     * Uninstall Plugin Service
     *
     * @var UninstallPluginService
     */
    protected $uninstallPluginService;

    /**
     * Index constructor
     *
     * @param ActionContext $context
     * @param UninstallPluginService $uninstallPluginService
     */
    public function __construct(
        ActionContext $context,
        UninstallPluginService $uninstallPluginService
    ) {
        $this->uninstallPluginService = $uninstallPluginService;
        parent::__construct($context);
    }

    /**
     * Uninstall Plugin Action
     *
     * @return Redirect
     */
    public function execute()
    {
        try {
            $result = $this->uninstallPluginService->execute(true);

            if ($result) {
                $this->messageManager->addSuccessMessage(__('E-commerce plugin uninstalled successfully.'));
            } else {
                $this->messageManager->addWarningMessage(__('E-commerce plugin might be already uninstalled or service is unavailable. Local data is successfully cleared.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong. E-commerce plugin could not be uninstalled.'));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setRefererOrBaseUrl();
    }
}