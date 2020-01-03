<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Model\Total\Invoice;

use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

class StoreCredit extends AbstractTotal
{
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $order = $invoice->getOrder();
        if ($order->getAmstorecreditAmount() > 0) {
            $invoiceBaseGrandTotal = $invoice->getBaseGrandTotal();
            $invoiceGrandTotal = $invoice->getGrandTotal();

            $leftStoreCredit = $order->getAmstorecreditAmount() - $order->getAmstorecreditInvoicedAmount();
            $leftBaseStoreCredit = $order->getAmstorecreditBaseAmount() - $order->getAmstorecreditInvoicedBaseAmount();

            if ($leftBaseStoreCredit > $invoiceBaseGrandTotal) {
                $invoice->setAmstorecreditBaseAmount($invoiceBaseGrandTotal);
                $invoice->setAmstorecreditAmount($invoiceGrandTotal);
                $invoiceBaseGrandTotal = 0;
                $invoiceGrandTotal = 0;
            } else {
                $invoiceGrandTotal -= $leftStoreCredit;
                $invoice->setAmstorecreditAmount($leftStoreCredit);
                $invoiceBaseGrandTotal -= $leftBaseStoreCredit;
                $invoice->setAmstorecreditBaseAmount($leftBaseStoreCredit);
            }

            if ($invoiceGrandTotal < 0.0001) {
                $invoiceGrandTotal = $invoiceBaseGrandTotal = 0;
            }

            $order->setAmstorecreditInvoicedBaseAmount(
                $order->getAmstorecreditInvoicedBaseAmount() + $invoice->getAmstorecreditBaseAmount()
            );

            $order->setAmstorecreditInvoicedAmount(
                $order->getAmstorecreditInvoicedAmount() + $invoice->getAmstorecreditAmount()
            );

            $invoice->setBaseGrandTotal($invoiceBaseGrandTotal);
            $invoice->setGrandTotal($invoiceGrandTotal);
        }
        return $this;
    }
}
