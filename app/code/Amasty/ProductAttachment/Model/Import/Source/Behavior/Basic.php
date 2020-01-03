<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Import\Source\Behavior;

use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Source\Import\AbstractBehavior;

class Basic extends AbstractBehavior
{
    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            Import::BEHAVIOR_CUSTOM => __('Add'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return 'importamfilebasic';
    }
}
