<?php

namespace Infoplus\Connect\Model\Sales;

class ShipmentRepositoryExtension
{
   public function afterSave(
      \Magento\Sales\Model\Order\ShipmentRepository $subject,
      \Magento\Sales\Model\Order\Shipment $result
   )
   {
      foreach ($result->getAllItems() as $item)
      {
         $orderItem = $item->getOrderItem();
         $orderItem->setQtyShipped( $item->getQty() );
         $orderItem->save();
      }

      $order = $result->getOrder()->load( $result->getOrder()->getId() );
      $order->save();

      return $result;
   }
}
