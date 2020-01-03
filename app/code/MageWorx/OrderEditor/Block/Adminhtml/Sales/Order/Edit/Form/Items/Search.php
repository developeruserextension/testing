<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Items;

/**
 * Class Search
 */
class Search extends \Magento\Sales\Block\Adminhtml\Order\Create\Search
{
    /**
     * Get buttons html
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getButtonsHtml() : string
    {
        $addButtonHtml = $this->getLayout()
            ->createBlock(\Magento\Backend\Block\Widget\Button::class)
            ->setData(
                [
                    'label' => __('Add Selected Product(s) to Order'),
                    'class' => 'action-add action-secondary',
                    'id'    => 'ordereditor-apply-add-products'
                ]
            )->toHtml();

        $cancelButtonHtml = $this->getLayout()
            ->createBlock(\Magento\Backend\Block\Widget\Button::class)
            ->setData(
                [
                    'label' => __('Cancel'),
                    'class' => 'action-cancel action-secondary',
                    'id'    => 'ordereditor-cancel-add-products'
                ]
            )->toHtml();

        return $cancelButtonHtml . $addButtonHtml;
    }
}
