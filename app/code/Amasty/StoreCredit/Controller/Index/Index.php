<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Controller\Index;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Registry;

class Index extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Session
     */
    private $customerSession;

    public function __construct(Session $customerSession, Registry $registry, Context $context)
    {
        parent::__construct($context);
        $this->registry = $registry;
        $this->customerSession = $customerSession;
    }

    /**
     * @return $this|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($customerId = $this->customerSession->getCustomerId()) {
            $this->registry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customerId);

            return $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        } else {
            return $this->_redirect('customer/account/login');
        }
    }
}
