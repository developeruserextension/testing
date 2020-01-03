<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Checkout
 */


namespace Amasty\Checkout\Controller\Index;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\Registry;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Result\LayoutFactory as ResultLayoutFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Amasty\Checkout\Helper\Onepage;
use Magento\Checkout\Helper\Data;
use Amasty\Checkout\Model\Config;
use Magento\Framework\View\Result\Page;
use Magento\Framework\Controller\ResultInterface;
use Magento\Checkout\Model\Session as CheckoutSession;

/**
 * Class Index
 */
class Index extends \Magento\Checkout\Controller\Index\Index
{
    /**
     * @var Onepage
     */
    protected $onepageHelper;

    /**
     * @var Data
     */
    protected $checkoutHelper;

    /**
     * @var Config
     */
    private $amCheckoutConfig;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    public function __construct(
        Context $context,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement,
        Registry $coreRegistry,
        InlineInterface $translateInline,
        Validator $formKeyValidator,
        ScopeConfigInterface $scopeConfig,
        LayoutFactory $layoutFactory,
        CartRepositoryInterface $quoteRepository,
        PageFactory $resultPageFactory,
        ResultLayoutFactory $resultLayoutFactory,
        RawFactory $resultRawFactory,
        JsonFactory $resultJsonFactory,
        Onepage $onepageHelper,
        Data $checkoutHelper,
        Config $amCheckoutConfig,
        CheckoutSession $checkoutSession
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $customerRepository,
            $accountManagement,
            $coreRegistry,
            $translateInline,
            $formKeyValidator,
            $scopeConfig,
            $layoutFactory,
            $quoteRepository,
            $resultPageFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $resultJsonFactory
        );

        $this->onepageHelper = $onepageHelper;
        $this->checkoutHelper = $checkoutHelper;
        $this->amCheckoutConfig = $amCheckoutConfig;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Checkout page
     *
     * @return ResultInterface|Page
     */
    public function execute()
    {
        if (!$this->amCheckoutConfig->isEnabled()) {
            return parent::execute();
        }

        if (!$this->checkoutHelper->canOnepageCheckout()) {
            $this->messageManager->addErrorMessage(__('One-page checkout is turned off.'));

            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        if (!$this->_customerSession->isLoggedIn() && !$this->checkoutHelper->isAllowedGuestCheckout($quote)) {
            $this->messageManager->addErrorMessage(__('Guest checkout is disabled.'));

            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        $this->_customerSession->regenerateId();
        $this->checkoutSession->setCartWasUpdated(false);
        $this->getOnepage()->initCheckout();

        $resultPage = $this->prepareResultPage();

        /** @var \Magento\Checkout\Block\Onepage $checkoutBlock */
        $checkoutBlock = $resultPage->getLayout()->getBlock('checkout.root');

        $checkoutBlock
            ->setTemplate('Amasty_Checkout::onepage.phtml')
            ->setData('amcheckout_helper', $this->onepageHelper);

        $resultPage->getConfig()->getTitle()->set(__('Checkout'));

        return $resultPage;
    }

    /**
     * @return Page
     */
    private function prepareResultPage()
    {
        $resultPage = $this->resultPageFactory->create();

        if ($font = $this->amCheckoutConfig->getCustomFont()) {
            $resultPage->getConfig()->addRemotePageAsset(
                'https://fonts.googleapis.com/css?family=' . urlencode($font),
                'css'
            );
        }

        $resultPage->getLayout()->getUpdate()->addHandle('amasty_checkout');

        if ($this->amCheckoutConfig->getHeaderFooter()) {
            $resultPage->getLayout()->getUpdate()->addHandle('amasty_checkout_headerfooter');
        }

        return $resultPage;
    }
}
