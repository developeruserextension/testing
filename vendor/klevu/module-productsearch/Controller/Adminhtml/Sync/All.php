<?php
namespace Klevu\Search\Controller\Adminhtml\Sync;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Backend\Model\Session;
use Klevu\Search\Helper\Config;
use Klevu\Search\Model\Product\Sync;
use Klevu\Search\Helper\Data;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\App\ObjectManager;
use Klevu\Search\Model\Product\MagentoProductActionsInterface as MagentoProductActions;
class All extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeModelStoreManagerInterface;
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendModelSession;
    /**
     * @var \Klevu\Search\Helper\Config
     */
    protected $_searchHelperConfig;
    /**
     * @var \Klevu\Search\Model\Product\Sync
     */
    protected $_modelProductSync;
    /**
     * @var \Klevu\Search\Helper\Data
     */
    protected $_searchHelperData;
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_frameworkEventManagerInterface;
	/**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_frameworkAppRequestInterface;
	/**
     * @var \Klevu\Search\Model\Product\MagentoProductActionsInterface
     */
    protected $_magentoProductActions;
	
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeModelStoreManagerInterface,
        \Klevu\Search\Helper\Config $searchHelperConfig,
        \Klevu\Search\Model\Product\Sync $modelProductSync,
        \Klevu\Search\Helper\Data $searchHelperData,
		\Klevu\Search\Model\Product\MagentoProductActionsInterface $magentoProductActions
    ) {
        $this->_storeModelStoreManagerInterface = $storeModelStoreManagerInterface;
        $this->_backendModelSession = $context->getSession();
        $this->_searchHelperConfig = $searchHelperConfig;
        $this->_modelProductSync = $modelProductSync;
        $this->_searchHelperData = $searchHelperData;
        $this->_frameworkEventManagerInterface = $context->getEventManager();
		$this->_magentoProductActions = $magentoProductActions;
        parent::__construct($context);
    }
    public function execute()
    {
        $store = $this->getRequest()->getParam("store");
        if ($store !== null) {
            try {
                $store = $this->_storeModelStoreManagerInterface->getStore($store);
            } catch (\Magento\Framework\Model\Store\Exception $e) {
                $this->_backendModelSession->addErrorMessage(__("Selected store could not be found!"));
                $this->_redirect($this->_redirect->getRefererUrl());
            }
        }
        
        if ($this->_searchHelperConfig->isProductSyncEnabled()) {
            if ($this->_searchHelperConfig->getSyncOptionsFlag() == "2") {
                if ($store) {
					if($this->_searchHelperConfig->isExternalCronEnabled()) {
						$this->_magentoProductActions
						->markAllProductsForUpdate($store);
					} else {
						$this->_magentoProductActions
						->markAllProductsForUpdate($store);
					}
                    
                    $this->_searchHelperData->log(\Zend\Log\Logger::INFO, sprintf(
                        "Product Sync scheduled to re-sync ALL products in %s (%s).",
                        $store->getWebsite()->getName(),
                        $store->getName()
                    ));
                    $this->messageManager->addSuccessMessage(sprintf(
                        "Klevu Search Product Sync scheduled to be run on the next cron run for ALL products in %s (%s).",
                        $store->getWebsite()->getName(),
                        $store->getName()
                    ));
                } else {
                    $this->_magentoProductActions->markAllProductsForUpdate();
                    $this->_searchHelperData->log(\Zend\Log\Logger::INFO, "Product Sync scheduled to re-sync ALL products.");
                    $this->messageManager->addSuccessMessage(__("Klevu Search Sync scheduled to be run on the next cron run for ALL products."));
                }
            } else {
                $this->syncWithoutCron();
            }
        } else {
            $this->messageManager->addErrorMessage(__("Klevu Search Product Sync is disabled."));
        }
        
        $this->_frameworkEventManagerInterface->dispatch('sync_all_external_data', [
            'store' => $store
        ]);
        $this->_storeModelStoreManagerInterface->setCurrentStore(0);
        return $this->_redirect($this->_redirect->getRefererUrl());
    }
    
    protected function _isAllowed()
    {
         return true;
    }
    
    public function syncWithoutCron()
    {
        try {
			$store = $this->getRequest()->getParam("store");
			$onestore = $this->_storeModelStoreManagerInterface->getStore($store);
			if($store != null) {
				//Sync Data
				if(is_object($onestore)) {
					
						$this->_modelProductSync->reset();
						if (!$this->_modelProductSync->setupSession($onestore)) {
							return;
						}
						$this->_modelProductSync->syncData($onestore);
						$this->_modelProductSync->runCategory($onestore);
				}
			} else {
				$this->_modelProductSync->run();
			}
            /* Use event For other content sync */
            $this->_frameworkEventManagerInterface->dispatch('content_data_to_sync', []);
            \Magento\Framework\App\ObjectManager::getInstance()->get('Klevu\Search\Model\Session')->unsFirstSync();
            $this->messageManager->addSuccessMessage(__("Data updates have been sent to Klevu"));
        } catch (\Magento\Framework\Model\Store\Exception $e) {
            $this->_psrLogLoggerInterface->error($e);
        }
        return $this->_redirect($this->_redirect->getRefererUrl());
    }
}