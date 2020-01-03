<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\SourceOptions;

use Magento\Framework\Option\ArrayInterface;

class CustomerGroup implements ArrayInterface
{

    /**
     * @var \Magento\Customer\Model\Customer\Attribute\Source\Group
     */
    protected $groupSource;

    public function __construct(\Magento\Customer\Model\Customer\Attribute\Source\Group $groupSource)
    {
        $this->groupSource = $groupSource;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        foreach ($this->toArray() as $stepId => $label) {
            $optionArray[] = ['value' => $stepId, 'label' => $label];
        }
        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $options = $this->groupSource->getAllOptions();
        $result = [0  => __('NOT LOGGED IN')];

        /**
         * B2B Fix
         */
        if (!empty($options[0]) && is_array($options[0]['value'])) {
            $options = $options[0]['value'];
        }

        foreach ($options as $option) {
            $result[$option['value']] = $option['label'];
        }
        
        return $result;
    }
}
