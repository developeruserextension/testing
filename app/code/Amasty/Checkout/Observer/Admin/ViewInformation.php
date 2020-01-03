<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Checkout
 */


namespace Amasty\Checkout\Observer\Admin;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Amasty\Checkout\Block\Adminhtml\Sales\Order\Delivery;
use Amasty\Checkout\Model\Config;

/**
 * Class ViewInformation
 */
class ViewInformation implements ObserverInterface
{
    /**
     * @var Config
     */
    protected $configProvider;

    public function __construct(
        Config $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        if (!$this->configProvider->isEnabled()) {
            return;
        }

        $elementName = $observer->getElementName();

        if ('order_info' == $elementName) {
            $block = $observer->getLayout()->getBlock($elementName);
            if ($block->hasData('amcheckout_delivery')) {
                return;
            }

            $transport = $observer->getTransport();
            $html = $transport->getOutput();

            $deliveryBlock = $observer->getLayout()
                ->createBlock(Delivery::class);

            $html .= $deliveryBlock->toHtml();
            $block->setData('amcheckout_delivery', true);

            $transport->setOutput($html);
        }
    }
}
