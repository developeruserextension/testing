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
class Type extends Source
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
                'value' => BrandTemplate::TYPE_BRAND_PAGE_TITLE,
                'label' => __('Brand Page Title')
            ],
            [
                'value' => BrandTemplate::TYPE_BRAND_META_TITLE,
                'label' => __('Brand Meta Title')
            ],
            [
                'value' => BrandTemplate::TYPE_BRAND_META_DESCRIPTION,
                'label' => __('Brand Meta Description')
            ],
            [
                'value' => BrandTemplate::TYPE_BRAND_META_KEYWORDS,
                'label' => __('Brand Meta Keywords')
            ]
        ];
    }
}
