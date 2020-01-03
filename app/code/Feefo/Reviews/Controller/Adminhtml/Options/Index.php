<?php

namespace Feefo\Reviews\Controller\Adminhtml\Options;

use Feefo\Reviews\Api\Feefo\Data\StoreUrlGroupDataInterface;
use Feefo\Reviews\Api\Feefo\StorageInterface;
use Feefo\Reviews\Api\Feefo\StoreUrlGroupInterface;
use Magento\Backend\App\Action as BackendAction;
use Magento\Backend\App\Action\Context as ActionContext;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Url\DecoderInterface as UrlDecoder;

/**
 * Class Index
 */
class Index extends BackendAction
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var StoreUrlGroupInterface
     */
    protected $storeUrlGroup;

    /**
     * @var UrlDecoder
     */
    protected $urlDecoder;

    /**
     * Index constructor
     *
     * @param ActionContext $context
     * @param StorageInterface $storage
     * @param StoreUrlGroupInterface $storeUrlGroup
     * @param UrlDecoder $urlDecoder
     */
    public function __construct(
        ActionContext $context,
        StorageInterface $storage,
        StoreUrlGroupInterface $storeUrlGroup,
        UrlDecoder $urlDecoder
    ) {
        $this->storage = $storage;
        $this->storeUrlGroup = $storeUrlGroup;
        $this->urlDecoder = $urlDecoder;
        parent::__construct($context);
    }

    /**
     * Show choose website, registration flow, configuration pages.
     *
     * @return $this
     */
    public function execute()
    {
        $websiteUrl = $this->getRequest()->getParam('website_url', null);
        $websiteUrl = $this->urlDecoder->decode($websiteUrl);

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($websiteUrl) {
            /** @var StoreUrlGroupDataInterface $urlGroup */
            $urlGroup = $this->storeUrlGroup->getGroupByUrl($websiteUrl);

            if (!$urlGroup) {
                $this->messageManager->addErrorMessage(__('The "%1" store can not be set.', $websiteUrl));
            } else {
                $storeIds = $urlGroup->getStoreIds();
                $this->storage->setWebsiteUrl($websiteUrl, $storeIds);
                $this->storage->setStoreIds($storeIds);
            }
            return $resultRedirect->setPath('feefo/*/');
        }

        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Feefo Ratings & Reviews Configurations'));
        $this->_view->renderLayout();
    }
}
