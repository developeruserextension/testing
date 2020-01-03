<?php

namespace Infoplus\Connect\Model;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderPlaceAfterObserver implements ObserverInterface
{
	protected $_logger;
  	protected $_jsonHelper;
  	protected $_curlAdapter;
	protected $_storeManager;
  	protected $_scopeConfig;

  	CONST INFOPLUS_URL = 'infoplus_connect/general/infoplus_url';


	public function __construct
	(
		\Magento\Framework\HTTP\Adapter\Curl $curlAdapter,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
     	\Magento\Framework\Json\Helper\Data $jsonHelper,
     	\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Psr\Log\LoggerInterface $logger
	)
	{
		$this->_logger = $logger;
		$this->_jsonHelper = $jsonHelper;
     	$this->_curlAdapter = $curlAdapter;
     	$this->_storeManager = $storeManager;
     	$this->_scopeConfig = $scopeConfig;
	}

   public function execute(\Magento\Framework\Event\Observer $observer)
   {  
   	$event = "order-created";
    	$url = $this->_scopeConfig->getValue(self::INFOPLUS_URL);
    	$url .= "/infoplus-wms/api/magento2/orders";

		/////////////////////////////////////
	   // get the order from the observer //
		/////////////////////////////////////
      $order = $observer->getEvent()->getOrder();

      $data = array("event" => $event, "increment_id" => $order->getIncrementId(), "store_url" => $this->_storeManager->getStore()->getBaseUrl());
      $bodyJson = $this->_jsonHelper->jsonEncode($data);

      $this->_logger->debug("Sending webhook data " . $bodyJson . " to " . $url);

		$headers = ["Content-Type: application/json"];
      $this->_curlAdapter->write('POST', $url, '1.1', $headers, $bodyJson);
      $this->_logger->info($this->_curlAdapter->read());
      $this->_curlAdapter->close();
   }
}
