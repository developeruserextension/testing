<?php

namespace Grizzlysts\HidePaymentMethod\Model\Config\Source;

class Method implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    public function __construct(
        \Magento\Payment\Helper\Data $paymentHelper
    )
    {
        $this->paymentHelper = $paymentHelper;
    }

    public function toOptionArray()
    {
        $list = $this->paymentHelper->getPaymentMethodList();
        $options = [];
        $options[] = ['value' => '', 'label' => __('Select method')];
        foreach ($list as $code => $name)
        {
            if ($name != null)
            {
                $options[] = ['value' => $code, 'label' => $name . " - " . $code];
            }
        }
        return $options;
    }
}