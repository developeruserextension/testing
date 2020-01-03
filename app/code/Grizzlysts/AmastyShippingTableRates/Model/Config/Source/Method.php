<?php

namespace Grizzlysts\AmastyShippingTableRates\Model\Config\Source;

class Method implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Amasty\ShippingTableRates\Model\ResourceModel\Method\CollectionFactory
     */
    private $methodCollectionFactory;

    public function __construct(
        \Amasty\ShippingTableRates\Model\ResourceModel\Method\CollectionFactory $methodCollectionFactory
    )
    {
        $this->methodCollectionFactory = $methodCollectionFactory;
    }

    public function toOptionArray()
    {
        $methodCollection = $this->methodCollectionFactory->create();
        $methodCollection->addFieldToFilter('is_active', 1);
        $options = [];
        foreach ($methodCollection as $method)
        {
            $options[] = [
                'value' => $method->getId(),
                'label' => $method->getName()
            ];
        }

        return $options;
    }
}