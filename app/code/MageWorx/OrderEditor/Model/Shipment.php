<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Model;

use Exception;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Sales\Api\OrderRepositoryInterface as OriginalOrderRepository;
use Magento\Sales\Model\Order\Shipment\CommentFactory as ShipmentCommentFactory;
use Magento\Sales\Model\Order\Shipment\Item as ShipmentItem;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Comment\CollectionFactory as ShipmentCommentCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Item\CollectionFactory as ShipmentItemCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory as ShipmentTrackCollectionFactory;
use MageWorx\OrderEditor\Api\OrderItemRepositoryInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

/**
 * Class Shipment
 */
class Shipment extends \Magento\Sales\Model\Order\Shipment
{
    /**
     * @var OrderItemRepositoryInterface
     */
    protected $oeOrderItemRepository;

    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;

    /**
     * Shipment constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param ShipmentItemCollectionFactory $shipmentItemCollectionFactory
     * @param ShipmentTrackCollectionFactory $trackCollectionFactory
     * @param ShipmentCommentFactory $commentFactory
     * @param ShipmentCommentCollectionFactory $commentCollectionFactory
     * @param OriginalOrderRepository $orderRepository
     * @param OrderItemRepositoryInterface $oeOrderItemRepository
     * @param MessageManagerInterface $messageManager
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        ShipmentItemCollectionFactory $shipmentItemCollectionFactory,
        ShipmentTrackCollectionFactory $trackCollectionFactory,
        ShipmentCommentFactory $commentFactory,
        ShipmentCommentCollectionFactory $commentCollectionFactory,
        OriginalOrderRepository $orderRepository,
        OrderItemRepositoryInterface $oeOrderItemRepository,
        MessageManagerInterface $messageManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->oeOrderItemRepository = $oeOrderItemRepository;
        $this->messageManager        = $messageManager;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $shipmentItemCollectionFactory,
            $trackCollectionFactory,
            $commentFactory,
            $commentCollectionFactory,
            $orderRepository,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\MageWorx\OrderEditor\Model\ResourceModel\Shipment::class);
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    public function beforeDelete(): self
    {
        $this->_commentCollectionFactory->create()
                                        ->setShipmentFilter($this->getId())
                                        ->walk('delete');

        $this->_shipmentItemCollectionFactory->create()
                                             ->setShipmentFilter($this->getId())
                                             ->walk('delete');

        $this->_trackCollectionFactory->create()
                                      ->setShipmentFilter($this->getId())
                                      ->walk('delete');

        $this->deleteFromGrid();

        parent::beforeDelete();

        return $this;
    }

    /**
     * Delete From Grid
     *
     * @return void
     */
    protected function deleteFromGrid()
    {
        $id = (int)$this->getId();
        if (!empty($id)) {
            $resource   = $this->getResource();
            $connection = $resource->getConnection();
            $gridTable  = $resource->getTable('sales_shipment_grid');
            $connection->delete($gridTable, ['entity_id = ?' => $id]);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function cancel()
    {
        $this->cancelItems();
        $this->changeOrderStatusAfterDeleteShipment();
    }

    /**
     * @return void
     */
    protected function cancelItems()
    {
        $shipmentItems = $this->getItemsCollection();

        /**
         * @var ShipmentItem $shipmentItem
         */
        foreach ($shipmentItems as $shipmentItem) {
            $orderItems = $this->getOrder()->getItems();
            foreach ($orderItems as $orderItem) {
                if ($orderItem->getProductId() != $shipmentItem->getProductId()) {
                    continue;
                }

                $qty = $orderItem->getQtyShipped() - $shipmentItem->getQty();
                $orderItem->setQtyShipped($qty);

                try {
                    $this->oeOrderItemRepository->save($orderItem);
                } catch (LocalizedException $exception) {
                    $this->messageManager->addErrorMessage(
                        __(
                            'Something goes wrong while cancelling shipments. Original error message: %1',
                            $exception->getMessage()
                        )
                    );
                }
            }
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function changeOrderStatusAfterDeleteShipment()
    {
        $order = $this->getOrder();

        $state = ($order->hasInvoices())
            ? \Magento\Sales\Model\Order::STATE_PROCESSING
            : \Magento\Sales\Model\Order::STATE_NEW;

        $order->setData('state', $state);
        $order->setStatus($order->getConfig()->getStateDefaultStatus($state));

        $this->orderRepository->save($order);
    }
}
