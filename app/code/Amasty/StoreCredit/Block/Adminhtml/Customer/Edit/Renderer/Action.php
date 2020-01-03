<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Block\Adminhtml\Customer\Edit\Renderer;

use Amasty\StoreCredit\Api\Data\HistoryInterface;
use Amasty\StoreCredit\Model\History\MessageProcessor;
use Magento\Backend\Block\Context;

class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Input
{
    public function render(\Magento\Framework\DataObject $row)
    {
        return MessageProcessor::processSmall(
            $row->getData(HistoryInterface::ACTION),
            [
                array_merge(
                    [
                        $row->getData(HistoryInterface::DIFFERENCE),
                        $row->getData(HistoryInterface::STORE_CREDIT_BALANCE)
                    ],
                    json_decode($row->getData(HistoryInterface::ACTION_DATA), true)
                )
            ]
        );
    }
}
