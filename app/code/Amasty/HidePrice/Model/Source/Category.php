<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Model\Source;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class Category implements \Magento\Framework\Option\ArrayInterface
{
    protected $options = [];
    /**
     * @var CollectionFactory
     */
    private $categoryCollectionFactory;

    public function __construct(
        CollectionFactory $categoryCollectionFactory
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    public function toOptionArray()
    {
        if (!$this->options) {
            $collection = $this->categoryCollectionFactory->create();
            $collection->addAttributeToSelect('name');
            $this->options[] = [
                'label' => __('NONE'),
                'value' => 0
            ];

            foreach ($collection as $item) {
                $this->options[] = [
                    'label' => $item->getName(),
                    'value' => $item->getId()
                ];
            }
        }

        return $this->options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */

    public function toArray()
    {
        $optionArray = $this->toOptionArray();
        $labels =  array_column($optionArray, 'label');
        $values =  array_column($optionArray, 'value');
        return array_combine($values, $labels);
    }
}
