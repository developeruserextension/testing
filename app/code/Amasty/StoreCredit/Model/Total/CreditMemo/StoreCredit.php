<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Model\Total\CreditMemo;

use Amasty\StoreCredit\Api\Data\SalesFieldInterface;
use Amasty\StoreCredit\Model\ConfigProvider;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class StoreCredit extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    public function __construct(
        ConfigProvider $configProvider,
        PriceCurrencyInterface $priceCurrency,
        RequestInterface $request,
        array $data = []
    ) {
        parent::__construct($data);
        $this->configProvider = $configProvider;
        $this->request = $request;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        if (!$creditmemo->getOrder()->getCustomerId()) {
            return $this;
        }

        $order = $creditmemo->getOrder();

        $grandTotal = $creditmemo->getGrandTotal();
        $baseGrandTotal = $creditmemo->getBaseGrandTotal();

        $leftStoreCredit = $order->getAmstorecreditAmount() - $order->getAmstorecreditRefundedAmount();
        $leftBaseStoreCredit = $order->getAmstorecreditBaseAmount() - $order->getAmstorecreditRefundedBaseAmount();

        if ($leftStoreCredit < 0) {
            $leftStoreCredit = $leftBaseStoreCredit = 0;
        }

        $storeCreditAmount = null;
        $storeCreditBaseAmount = null;

        $returnToStoreCredit = $this->request->getParam('return_to_store_credit');
        if ($returnToStoreCredit !== null) {
            $creditmemo->setData(SalesFieldInterface::AMSC_USE, (bool)$returnToStoreCredit);
            if ($returnToStoreCredit) {
                $amount = $this->request->getParam('store_credit_return_amount');
                if ($amount === null) {
                    $storeCreditAmount = $grandTotal > $leftStoreCredit ? $grandTotal : $leftStoreCredit;
                    $storeCreditBaseAmount = $baseGrandTotal > $leftBaseStoreCredit
                        ? $baseGrandTotal
                        : $leftBaseStoreCredit;
                    $grandTotal -= $storeCreditAmount;
                    $baseGrandTotal -= $storeCreditBaseAmount;
                } else {
                    if ($this->request->getParam('amstore_credit_new')) {
                        $amount = (double)$amount;
                        if ($amount < 0) {
                            throw new LocalizedException(__('Store Credit Refund couldn\'t be less than zero.'));
                        }
                        if ($amount < 0.0001) {
                            $creditmemo->setData(SalesFieldInterface::AMSC_USE, false);
                        } else {
                            if ($baseGrandTotal > 0.0001) {
                                $storeCreditAmount = $this->priceCurrency->round(
                                    $amount * $grandTotal / $baseGrandTotal
                                );
                            } else {
                                //
                            }

                            $storeCreditBaseAmount = $amount;

                            if ($storeCreditAmount < $grandTotal) {
                                $moneyForRefund = $order->getGrandTotal() - $order->getTotalRefunded();
                                if ($moneyForRefund < $grandTotal - $storeCreditAmount) {
                                    $storeCreditAmount = $grandTotal - $moneyForRefund;
                                    $storeCreditBaseAmount = $baseGrandTotal
                                        - $order->getBaseGrandTotal() + $order->getBaseTotalRefunded();
                                }
                            }

                            $grandTotal -= $storeCreditAmount;
                            $baseGrandTotal -= $storeCreditBaseAmount;
                            if ($baseGrandTotal < 0.0001) {
                                $grandTotal = $baseGrandTotal = 0;
                            }
                        }
                    } else {
                        $storeCreditAmount = $grandTotal;
                        $storeCreditBaseAmount = $baseGrandTotal;
                        $grandTotal = $baseGrandTotal = 0;
                    }
                }
            }
        } else {
            if ($this->configProvider->isRefundAutomatically()) {
                $creditmemo->setData(SalesFieldInterface::AMSC_USE, true);
                $storeCreditAmount = $grandTotal;
                $storeCreditBaseAmount = $baseGrandTotal;
                $grandTotal = $baseGrandTotal = 0;
            }
        }

        if (!$creditmemo->getData(SalesFieldInterface::AMSC_USE)) {
            if ($grandTotal > $order->getGrandTotal() - $order->getTotalRefunded()) {
                $storeCreditAmount = $grandTotal - $order->getGrandTotal()  + $order->getTotalRefunded();
                $storeCreditBaseAmount = $baseGrandTotal -$order->getBaseGrandTotal() + $order->getBaseTotalRefunded();
            } elseif ($leftStoreCredit < $grandTotal) {
                $storeCreditAmount = $leftStoreCredit;
                $storeCreditBaseAmount = $leftBaseStoreCredit;
            }

            $grandTotal -= $storeCreditAmount;
            $baseGrandTotal -= $storeCreditBaseAmount;
        }

        $creditmemo->setAmstorecreditAmount($storeCreditAmount);
        $creditmemo->setAmstorecreditBaseAmount($storeCreditBaseAmount);
        if ($baseGrandTotal < 0.0001) {
            $baseGrandTotal = $grandTotal = 0;
            $creditmemo->setAllowZeroGrandTotal(true);
        }
        $creditmemo->setGrandTotal($grandTotal);
        $creditmemo->setBaseGrandTotal($baseGrandTotal);

        return $this;
    }
}
