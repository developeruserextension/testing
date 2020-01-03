<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Block;

use Amasty\StoreCredit\Api\Data\HistoryInterface;
use Amasty\StoreCredit\Model\History\ResourceModel\CollectionFactory;
use Amasty\StoreCredit\Model\StoreCredit\StoreCreditRepository;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class Dashboard extends Template
{
    /**
     * @var string
     */
    protected $_template = 'dashboard.phtml';

    /**
     * @var \Amasty\StoreCredit\Model\History\ResourceModel\Collection
     */
    private $collection;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var StoreCreditRepository
     */
    private $storeCreditRepository;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        Registry $registry,
        StoreCreditRepository $storeCreditRepository,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->collectionFactory = $collectionFactory;
        $this->registry = $registry;
        $this->storeCreditRepository = $storeCreditRepository;
        $this->priceCurrency = $priceCurrency;
    }

    public function getCustomerBalance()
    {
        return $this->priceCurrency->convertAndFormat(
            $this->storeCreditRepository->getByCustomerId($this->getCustomerId())->getStoreCredit(),
            false,
            2,
            null,
            $this->_storeManager->getStore()->getCurrentCurrency()
        );
    }

    public function getCustomerId()
    {
        return $this->registry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * Return Pager html for all pages
     *
     * @return string
     */
    public function getPagerHtml()
    {
        $pagerBlock = $this->getChildBlock('amasty_store_credit_pager');

        if ($pagerBlock instanceof \Magento\Framework\DataObject) {

            $pagerBlock->setUseContainer(
                false
            )->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setLimit(
                $this->getLimit()
            )->setCollection(
                $this->getCollection()
            );

            return $pagerBlock->toHtml();
        }

        return '';
    }

    public function getCollection()
    {
        if (!$this->collection) {
            $this->collection = $this->collectionFactory->create();
            $this->collection->setDashboarFilters($this->getCustomerId());
            $this->collection->setOrder('main_table.' . HistoryInterface::CUSTOMER_HISTORY_ID, 'DESC');
            if ($this->getLimit()) {
                $curPage = (int)$this->getRequest()->getParam('p', 1);
                $this->collection->setCurPage($curPage);
                $this->collection->setPageSize($this->getLimit());
            }
        }

        return $this->collection;
    }

    public function getLimit()
    {
        return (int)$this->getRequest()->getParam('limit', 10);
    }
}
