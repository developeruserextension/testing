<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Gateway;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Model\Method\Adapter as PaymentMethodAdapter;

/**
 * Class PaymentFacade
 */
class PaymentFacade extends PaymentMethodAdapter
{
    /**
     * Get custom method title from the additional_information block
     *
     * @return string
     */
    public function getTitle(): string
    {
        $title = parent::getTitle();

        try {
            $info = $this->getInfoInstance();
            if (!$info) {
                return $title;
            }
        } catch (LocalizedException $exception) {
            return $title;
        }

        $additionalInformation = $info->getAdditionalInformation();
        if (!$additionalInformation || empty($additionalInformation['method_title'])) {
            return $title;
        }

        return $additionalInformation['method_title'];
    }
}
