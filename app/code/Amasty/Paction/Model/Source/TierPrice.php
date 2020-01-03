<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */


namespace Amasty\Paction\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * TierPrice types mode source.
 */
class TierPrice implements OptionSourceInterface
{
    /**#@+
     * Values
     */
    const VALUE_FIXED = 'fixed';
    const VALUE_PERCENT = 'percent';
    /**#@-*/

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::VALUE_FIXED, 'label' => __('Fixed')],
            ['value' => self::VALUE_PERCENT, 'label' => __('Discount')],
        ];
    }
}
