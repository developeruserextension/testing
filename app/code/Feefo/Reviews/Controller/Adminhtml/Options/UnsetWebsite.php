<?php

namespace Feefo\Reviews\Controller\Adminhtml\Options;

use Feefo\Reviews\Api\Feefo\StorageInterface;
use Magento\Backend\App\Action as BackendAction;
use Magento\Backend\App\Action\Context as ActionContext;
use Magento\Backend\Model\View\Result\Redirect;

/**
 * Class UnsetWebsite
 */
class UnsetWebsite extends BackendAction
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * Index constructor
     *
     * @param ActionContext $context
     * @param StorageInterface $storage
     */
    public function __construct(
        ActionContext $context,
        StorageInterface $storage
    ) {
        $this->storage = $storage;
        parent::__construct($context);
    }

    /**
     * Show choose website, registration flow, configuration pages.
     *
     * @return $this
     */
    public function execute()
    {
        $this->storage->unsetWebsite();

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('feefo/options/index');
    }
}