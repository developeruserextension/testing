<?php

namespace Infoplus\Connect\Plugin\Widget;
 
class Context
{
   protected $_logger;
  	protected $_scopeConfig;
  	protected $_orderRepository;
	protected $_storeManager;

  	CONST INFOPLUS_URL = 'infoplus_connect/general/infoplus_url';
 
   public function __construct(
      \Psr\Log\LoggerInterface $logger,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
     	\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
      \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
   )
   {
      $this->_logger = $logger;
     	$this->_storeManager = $storeManager;
     	$this->_scopeConfig = $scopeConfig;
      $this->_orderRepository = $orderRepository;
   }


   public function afterGetButtonList(\Magento\Backend\Block\Widget\Context $subject, $buttonList)
   {
      if($subject->getRequest()->getFullActionName() == 'sales_order_view')
      {
         $orderId = $subject->getRequest()->getParam("order_id");
         $order = $this->_orderRepository->get($orderId);
         $incrementId = $order->getIncrementId();
         $this->_logger->info("IncrementId: " . $incrementId);

         $replaceURL = $this->_scopeConfig->getValue(self::INFOPLUS_URL);
         $replaceURL .= "/infoplus-wms/api/magento/replace";
         $replaceURL .= "?order_id=" . $incrementId;
         $replaceURL .= "&store_url=" . $this->_storeManager->getStore()->getBaseUrl();

         $resendURL = $this->_scopeConfig->getValue(self::INFOPLUS_URL);
         $resendURL .= "/infoplus-wms/api/magento/repost";
         $resendURL .= "?order_id=" . $incrementId;
         $resendURL .= "&store_url=" . $this->_storeManager->getStore()->getBaseUrl();

         $viewURL = $this->_scopeConfig->getValue(self::INFOPLUS_URL);
         $viewURL .= "/infoplus-wms/order/req/query/";
         $viewURL .= "{%22criteriaFields%22:[{%22fieldName%22:%22customerOrderNo%22,%22values%22:[%22" . $incrementId . "%22],%22operator%22:%22EQUALS%22},{%22fieldName%22:%22reqLoadProgramId%22,%22operator%22:%22IN%22,%22values%22:[{%22id%22:32,%22text%22:%22Magento 2 (32)%22,%22idValue%22:4}],%22isAdvancedPVS%22:false}],%22orderByFields%22:[{%22fieldName%22:%22reqNo%22,%22direction%22:%22DESC%22}],%22startAt%22:null,%22limit%22:null}";

         $resendMessage = "Are you sure you want to send order " . $incrementId . " to Infoplus? NOTE: If this order already exists in Infoplus, it will NOT be replaced!";
         $replaceMessage = "Are you sure you want to replace order " . $incrementId . " in Infoplus? NOTE: If an order already exists, it will be replaced!";

         $buttonList->add
         (
            'resend_to_infoplus',
            [
               'label' => __('Send To Infoplus'),
               'onclick' => "if(confirm('{$resendMessage}')){window.open('{$resendURL}','_blank','width=350,height=250');}",
               'class' => 'ship'
            ]
         );

         $buttonList->add
         (
            'replace_in_infoplus',
            [
               'label' => __('Replace In Infoplus'),
               'onclick' => "if(confirm('{$replaceMessage}')){window.open('{$replaceURL}','_blank','width=350,height=250');}",
               'class' => 'ship'
            ]
         );

         $buttonList->add
         (
            'view_in_infoplus',
            [
               'label' => __('View In Infoplus'),
               'onclick' => "window.open('${viewURL}')",
               'class' => 'ship'
            ]
         );
      }

      return $buttonList;
   }
}
