<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Template\Brand\Source;

use MageWorx\SeoXTemplates\Model\Template\Brand as BrandTemplate;
use MageWorx\SeoAll\Model\Source;

/**
 * Used in creating options for config value selection
 *
 */
class AssignType extends Source
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => BrandTemplate::ASSIGN_ALL_ITEMS,
                'label' => __('All Brands')
            ],
            [
                'value' => BrandTemplate::ASSIGN_INDIVIDUAL_ITEMS,
                'label' => __('Specific Brands')
            ],
        ];
    }
}
