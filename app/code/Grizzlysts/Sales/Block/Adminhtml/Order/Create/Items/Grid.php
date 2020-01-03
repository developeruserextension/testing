<?php
/**
 * Created by PhpStorm.
 * User: hieu
 * Date: 12/10/19
 * Time: 23:35
 */

namespace Grizzlysts\Sales\Block\Adminhtml\Order\Create\Items;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;

class Grid extends \Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid
{
    /**
     * Get items
     *
     * @return Item[]
     */
    public function getItems()
    {
        $items = $this->getParentBlock()->getItems();
        $oldSuperMode = $this->getQuote()->getIsSuperMode();
        $this->getQuote()->setIsSuperMode(false);
        foreach ($items as $item) {
            // To dispatch inventory event sales_quote_item_qty_set_after, set item qty
            // $item->setQty($item->getQty());

            if (!$item->getMessage()) {
                //Getting product ids for stock item last quantity validation before grid display
                $stockItemToCheck = [];

                $childItems = $item->getChildren();
                if (count($childItems)) {
                    foreach ($childItems as $childItem) {
                        $stockItemToCheck[] = $childItem->getProduct()->getId();
                    }
                } else {
                    $stockItemToCheck[] = $item->getProduct()->getId();
                }

                foreach ($stockItemToCheck as $productId) {
                    $check = $this->stockState->checkQuoteItemQty(
                        $productId,
                        $item->getQty(),
                        $item->getQty(),
                        $item->getQty(),
                        $this->getQuote()->getStore()->getWebsiteId()
                    );
                    $item->setMessage($check->getMessage());
                    $item->setHasError($check->getHasError());
                }
            }

            if ($item->getProduct()->getStatus() == ProductStatus::STATUS_DISABLED) {
                $item->setMessage(__('This product is disabled.'));
                $item->setHasError(true);
            }
        }
        $this->getQuote()->setIsSuperMode($oldSuperMode);
        return $items;
    }
}