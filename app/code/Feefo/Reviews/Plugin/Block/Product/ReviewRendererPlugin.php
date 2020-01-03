<?php
/**
 * @author Atwix Team
 * @copyright Copyright (c) 2018 Atwix (https://www.atwix.com/)
 * @package Feefo_Reviews
 */
namespace Feefo\Reviews\Plugin\Block\Product;

use Magento\Review\Block\Product\ReviewRenderer;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ReviewRendererPlugin
 */
class ReviewRendererPlugin
{
    /**
     * Rating block name
     */
    const FEEFO_BLOCK_NAME = 'category.products.list.feefoRating';

    /**
     * Get product feefo reviews summary
     *
     * @param ReviewRenderer $subject
     * @param $proceed
     * @param $product
     *
     * @return string
     *
     * @throws LocalizedException
     */
    public function aroundGetReviewsSummaryHtml(ReviewRenderer $subject, $proceed, $product) {
        $layout= $subject->getLayout();
        $block = $layout->getBlock(self::FEEFO_BLOCK_NAME);
        if ($block) {
            $block->setCurrentProduct($product);
            if ($block->isEnabledWidget() && $block->isRightPlacement()) {

                return $block->toHtml();
            }
        }

        return $proceed($product);
    }
}