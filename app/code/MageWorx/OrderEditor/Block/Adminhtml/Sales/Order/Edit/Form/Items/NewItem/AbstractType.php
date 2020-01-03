<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Items\NewItem;

use \MageWorx\OrderEditor\Model\Quote\Item;
use \MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Items\Type\AbstractType as AbstractItemType;

/**
 * Class AbstractType
 */
class AbstractType extends AbstractItemType
{
    /**
     * @return string
     */
    public function getPrefixId() : string
    {
        return Item::PREFIX_ID;
    }

    /**
     * @return string
     */
    public function getEditedItemType() : string
    {
        return static::ITEM_TYPE_QUOTE;
    }
}
