<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Items\Search;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Backend\Model\Session\Quote as SessionQuote;
use Magento\Catalog\Model\Config as CatalogConfig;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Config as SalesConfig;
use Magento\Store\Model\Store;
use MageWorx\OrderEditor\Model\Order;
use MageWorx\OrderEditor\Api\OrderRepositoryInterface;

/**
 * Class Grid
 */
class Grid extends \Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid
{
    /**
     * @var Order
     */
    protected $order;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * Grid constructor.
     *
     * @param Context $context
     * @param BackendHelper $backendHelper
     * @param ProductFactory $productFactory
     * @param CatalogConfig $catalogConfig
     * @param SessionQuote $sessionQuote
     * @param SalesConfig $salesConfig
     * @param OrderRepositoryInterface $orderRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        ProductFactory $productFactory,
        CatalogConfig $catalogConfig,
        SessionQuote $sessionQuote,
        SalesConfig $salesConfig,
        OrderRepositoryInterface $orderRepository,
        array $data = []
    ) {
        $this->orderRepository = $orderRepository;
        parent::__construct(
            $context,
            $backendHelper,
            $productFactory,
            $catalogConfig,
            $sessionQuote,
            $salesConfig,
            $data
        );
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl(): string
    {
        return $this->getUrl(
            'sales/order_create/loadBlock',
            ['block' => 'search_grid', '_current' => true, 'collapse' => null]
        );
    }

    /**
     * Get store
     *
     * @return Store
     */
    public function getStore(): Store
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if (empty($orderId)) {
            return parent::getStore();
        }

        try {
            if ($this->order === null) {
                $this->order = $this->orderRepository->getById($orderId);
            }

            return $this->order->getStore();
        } catch (NoSuchEntityException $exception) {
            return parent::getStore();
        }
    }
}
