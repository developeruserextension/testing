<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Model;

use Exception;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\OrderAddressRepositoryInterface;
use Magento\Sales\Model\AbstractModel;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Sales\Model\Order\Address as OrderAddressModel;

/**
 * Class Address
 */
class Address extends AbstractModel
{
    /**
     * @var $address OrderAddressInterface|OrderAddressModel
     */
    protected $address;

    /**
     * @var $oldAddress OrderAddressInterface|OrderAddressModel
     */
    protected $oldAddress;

    /**
     * @var RegionFactory
     */
    protected $regionFactory;

    /**
     * @var OrderAddressRepositoryInterface
     */
    protected $orderAddressRepository;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param RegionFactory $regionFactory
     * @param \Magento\Customer\Model\Address $customerAddress
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        RegionFactory $regionFactory,
        OrderAddressRepositoryInterface $orderAddressRepository,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );

        $this->regionFactory = $regionFactory;
        $this->orderAddressRepository = $orderAddressRepository;
    }

    /**
     * @param string[] $addressData
     * @return void
     * @throws LocalizedException
     */
    public function updateAddress(array &$addressData)
    {
        if ($this->address === null) {
            throw new LocalizedException(__('Address must be loaded before.'));
        }

        $addressData = $this->prepareRegion($addressData);
        $this->oldAddress = $this->address->getData();

        $this->address->addData($addressData);
        $this->orderAddressRepository->save($this->address);

        $this->_eventManager->dispatch(
            'admin_sales_order_address_update',
            ['order_id' => $this->address->getParentId()]
        );
    }

    /**
     * @param string[] &$addressData
     * @return string[]
     */
    protected function prepareRegion(array &$addressData): array
    {
        if (!empty($addressData['region_id'])
            && empty($addressData['region'])
        ) {
            $addressData['region'] = $this->regionFactory->create()
                ->load($addressData['region_id'])
                ->getName();
        }

        return $addressData;
    }

    /**
     * @param int $addressId
     * @return OrderAddressInterface|OrderAddressModel
     */
    public function loadAddress(int $addressId): OrderAddressInterface
    {
        /**
         * @var OrderAddressInterface|OrderAddressModel $address
         */
        $this->address = $this->orderAddressRepository->get($addressId);

        return $this->address;
    }
}
