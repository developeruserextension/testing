<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Model\Source;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Option\ArrayInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Catalog\Model\Product;

class StockStatus implements ArrayInterface
{
    /**
     * @var array|null
     */
    private $options = null;

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    public function __construct(AttributeRepositoryInterface $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!isset($this->options)) {
            $this->options = [];
            try {
                $attribute = $this->attributeRepository->get(Product::ENTITY, 'custom_stock_status');
                $this->options = $attribute->getSource()->getAllOptions(false);
            } catch (NoSuchEntityException $exception) {
                $this->options = [];
            }
            array_unshift($this->options, [
                'label' => __('NONE'),
                'value' => ''
            ]);
        }

        return $this->options;
    }
}
