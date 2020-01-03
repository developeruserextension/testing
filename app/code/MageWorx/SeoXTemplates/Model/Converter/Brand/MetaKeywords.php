<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Converter\Brand;

use MageWorx\SeoXTemplates\Model\Converter\Brand as ConverterBrand;

class MetaKeywords extends ConverterBrand
{
    /**
     *
     * @param string $convertValue
     * @return string
     */
    protected function _render($convertValue)
    {
        $convertValue = parent::_render($convertValue);
        $convertValue = strip_tags($convertValue);

        return trim($convertValue);
    }
}