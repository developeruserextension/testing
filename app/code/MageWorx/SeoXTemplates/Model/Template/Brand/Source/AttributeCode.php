<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Template\Brand\Source;

use MageWorx\SeoXTemplates\Model\Template\Brand as BrandTemplate;

/**
 * Used in creating options for config value selection
 *
 */
class AttributeCode
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toArray()
    {
        return [
            BrandTemplate::TYPE_BRAND_PAGE_TITLE       => ['page_title'],
            BrandTemplate::TYPE_BRAND_META_TITLE       => ['meta_title'],
            BrandTemplate::TYPE_BRAND_META_DESCRIPTION => ['meta_description'],
            BrandTemplate::TYPE_BRAND_META_KEYWORDS    => ['meta_keywords']
        ];
    }
}
