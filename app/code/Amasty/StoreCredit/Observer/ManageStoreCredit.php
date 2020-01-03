<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Observer;

use Amasty\StoreCredit\Api\Data\StoreCreditInterface;
use Amasty\StoreCredit\Model\ConfigProvider;
use Amasty\StoreCredit\Model\History\MessageProcessor;

class ManageStoreCredit implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Amasty\StoreCredit\Api\ManageCustomerStoreCreditInterface
     */
    private $manageCustomerStoreCredit;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    private $authorization;

    /**
     * @var \Amasty\StoreCredit\Model\StoreCredit\StoreCreditRepository
     */
    private $storeCreditRepository;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        \Amasty\StoreCredit\Model\ConfigProvider $configProvider,
        \Amasty\StoreCredit\Api\ManageCustomerStoreCreditInterface $manageCustomerStoreCredit,
        \Magento\Framework\AuthorizationInterface $authorization,
        \Amasty\StoreCredit\Model\StoreCredit\StoreCreditRepository $storeCreditRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->manageCustomerStoreCredit = $manageCustomerStoreCredit;
        $this->authorization = $authorization;
        $this->storeCreditRepository = $storeCreditRepository;
        $this->configProvider = $configProvider;
        $this->priceCurrency = $priceCurrency;
        $this->storeManager = $storeManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->configProvider->isEnabled()
            && $this->authorization->isAllowed('Amasty_StoreCredit::customer')
        ) {
            $request = $observer->getData('request');
            /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
            $customer = $observer->getData('customer');
            if ($addOrSubtract = $request->getParam(StoreCreditInterface::ADD_OR_SUBTRACT)) {
                $addOrSubtract = $this->priceCurrency->round(
                    $addOrSubtract / $this->storeManager->getStore()->getBaseCurrency()->getRate(
                        $this->storeManager->getStore()->getCurrentCurrencyCode()
                    )
                );
                $currentAmount = $this->storeCreditRepository->getByCustomerId($customer->getId())->getStoreCredit();
                if (($currentAmount + $addOrSubtract) < 0) {
                    $addOrSubtract = -$currentAmount;
                }
                $this->manageCustomerStoreCredit->addOrSubtractStoreCredit(
                    $customer->getId(),
                    $addOrSubtract,
                    (
                        $addOrSubtract < 0
                            ? MessageProcessor::ADMIN_BALANCE_CHANGE_MINUS
                            : MessageProcessor::ADMIN_BALANCE_CHANGE_PLUS
                    ),
                    [],
                    null,
                    $request->getParam(StoreCreditInterface::ADMIN_COMMENT, '')
                );
            }
        }
    }
}
