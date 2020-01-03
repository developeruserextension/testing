<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Helper\Comment;

use MageWorx\SeoXTemplates\Model\Template\Brand as BrandTemplate;

/**
 * SEO XTemplates data helper
 */
class Brand extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @param BrandTemplate $type
     * Return comments for brand page template
     *
     * @return string
     * @throws \UnexpectedValueException
     */
    public function getComments($type)
    {
        $comment = '<br><small>' . $this->getStaticVariableHeader();
        switch ($type) {
            case BrandTemplate::TYPE_BRAND_META_TITLE:
            case BrandTemplate::TYPE_BRAND_META_DESCRIPTION:
            case BrandTemplate::TYPE_BRAND_META_KEYWORDS:
                $comment .= '<br><p>' . $this->getBrandComment();
                $comment .= '<br><p>' . $this->getWebsiteNameComment();
                $comment .= '<br><p>' . $this->getStoreNameComment();
                $comment .= '<br><p>' . $this->getStoreViewNameComment();
                $comment .= '<br><p>' . $this->getDynamicVariableHeader();
                $comment .= '<br><p>' . $this->getLnAllFiltersComment();
                $comment .= '<br><p>' . $this->getLnPersonalFiltersComment();
                $comment .= '<br><p>' . $this->getRandomizeComment();
                break;
            case BrandTemplate::TYPE_BRAND_PAGE_TITLE:
                $comment .= '<br><p>' . $this->getBrandComment();
                $comment .= '<br><p>' . $this->getWebsiteNameComment();
                $comment .= '<br><p>' . $this->getStoreNameComment();
                $comment .= '<br><p>' . $this->getStoreViewNameComment();
                $comment .= '<br><p>' . $this->getRandomizeComment();
                break;
            default:
                throw new \UnexpectedValueException(__('SEO XTemplates: Unknow Brand Page Template Type'));
        }
        return $comment.'</small>';
    }

    /**
     * Return Static Variable header
     *
     * @return string
     */
    protected function getStaticVariableHeader()
    {
        return '<p><p><b><u>' . __('Static Template variables:') . '</u></b>' . ' ' .
            __('their values are written in brand attributes in the backend.') . ' ' .
            __('The values of randomizer feature will also be written in the attributes.');
    }

    /**
     * Return Dynamic Variable header
     *
     * @return string
     */
    protected function getDynamicVariableHeader()
    {
        return '<br><p><p><b><u>' . __('Dynamic Template variables:') . '</u></b>' .
            ' <font color = "#ea7601">' . __('their values will only be seen on the frontend. In the backend you’ll see the variables themselves.') . '</font>' .
            ' ' . __('Here randomizer values will change with every page refresh.');
    }

    /**
     * Return comment for Brand
     *
     * @return string
     */
    protected function getBrandComment()
    {
        return '<b>[brand]</b> - ' . __('output a brand name') . ';<br>' .
            '<b>[meta_title]</b> - ' . __('output a brand meta title') . ';<br>' .
            '<b>[meta_description]</b> - ' . __('output a brand meta description') . ';<br>' .
            '<b>[meta_keywords]</b> - ' . __('output a brand meta keywords') . ';<br>' .
            '<b>[page_title]</b> - ' . __('output a brand page title') . ';<br>';
    }

    /**
     * Return comment for Website Name
     *
     * @return string
     */
    protected function getWebsiteNameComment()
    {
        return '<b>[website_name]</b> - ' . __('output a current website name') . ';';
    }

    /**
     * Return comment for Store Name
     *
     * @return string
     */
    protected function getStoreNameComment()
    {
        return '<b>[store_name]</b> - ' . __('output a current store name') . ';';
    }

    /**
     * Return comment for Store View Name
     *
     * @return string
     */
    protected function getStoreViewNameComment()
    {
        return '<b>[store_view_name]</b> - ' . __('output a current store view name') . ';';
    }


    /**
     * Return comment for filter_all
     *
     * @return string
     */
    protected function getLnAllFiltersComment()
    {
        $string = '<b>[filter_all]</b> - ' . __('inserts all chosen attributes of LN on the brand.');
        $string .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . __('Example:') . " <b>" . '[brand][ – parameters: {filter_all}]' . "</b>";
        $string .= " - " . __('If "color", "occasion", and "shoe size" attributes are chosen, on the frontend you will see:');
        $string .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . __('"Shoes – parameters: Color Red, Occasion Casual, Shoe Size 6.5"');
        $string .= " - " . __('If no attributes are chosen, you will see: "Shoes".');

        return $string;
    }

    /**
     * Return comment for personal filters
     *
     * @return string
     */
    protected function getLnPersonalFiltersComment()
    {
        $string = '<b>[filter_<i>attribute_code</i>]</b> - ' . __('insert attribute value if exists.');
        $string .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . __('Example:') . ' <b>[brand][ in {filter_color}]</b>';
        $string .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . __('Will translate to "Shoes in Color Red" on the frontend.');

        $string .= '<br><b>[filter_<i>attribute_code</i>_label]</b> - ' . __('inserts mentioned product attribute label on the brand LN page.');
        $string .= '<br><b>[filter_<i>attribute_code</i>_value]</b> - ' . __('inserts mentioned product attribute value on the brand LN page.');

        return $string;
    }

    /**
     * Return comment for randomizer
     *
     * @return string
     */
    protected function getRandomizeComment()
    {
        return '<p>'. __('Randomizer feature is available. The construction like [Buy||Order||Purchase] will use a randomly picked word.').'<br>'.__('
        Also randomizers can be used within other template variables, ex: [Name:||Title: {brand}]. Number of randomizers blocks is not limited within the template.').'<br>';
    }
}
