<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Model\Source;

use Amasty\HidePrice\Helper\Data as Helper;

class Group extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Customer\Model\Customer\Attribute\Source\Group
     */
    private $groupSource;

    public function __construct(\Magento\Customer\Model\Customer\Attribute\Source\Group $groupSource)
    {
        $this->groupSource = $groupSource;
    }

    public function toOptionArray()
    {
        return array_merge(
            [
                [
                    'value' => Helper::DISABLED_GROUP_KEY,
                    'label' => __('NONE')
                ],
                [
                    'value' => Helper::NOT_LOGGED_KEY,
                    'label' => __('NOT LOGGED IN')
                ]
            ],
            $this->groupSource->getAllOptions()
        );
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

    public function getAllOptions()
    {
        return $this->toOptionArray();
    }

    /**
     * @return array
     */
    public function getFlatColumns()
    {
        $columns = [];

        $columns[$this->getAttribute()->getAttributeCode()] = [
            'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'   => '255',
            'unsigned' => false,
            'nullable' => true,
            'default'  => null,
            'extra'    => null
        ];

        return $columns;
    }
}
