<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Controller\Adminhtml\Edit;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\OrderEditor\Controller\Adminhtml\AbstractAction;

/**
 * Class Shipping
 */
class Shipping extends AbstractAction
{
    /**
     * @return void
     */
    protected function update()
    {
        $this->updateShippingMethod();
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function updateShippingMethod()
    {
        $params = $this->prepareParams();
        $this->shipping->initParams($params);

        $this->shipping->updateShippingMethod();
    }

    /**
     * @return string
     */
    protected function prepareResponse(): string
    {
        return static::ACTION_RELOAD_PAGE;
    }

    /**
     * Collect params from the request to the array.
     * Contains validation.
     *
     * @return array
     * @throws LocalizedException
     */
    protected function prepareParams(): array
    {
        $params = [];
        $paramsToUpdate = [
            'shipping_method',
            'order_id',
            'price_excl_tax',
            'price_incl_tax',
            'tax_percent',
            'description',
            'discount_amount',
            'tax_rates'
        ];

        foreach ($paramsToUpdate as $paramToUpdate) {
            $val = $this->getRequest()->getParam($paramToUpdate);
            if ($val == null) {
                if ($paramToUpdate === 'tax_rates') {
                    $val = [];
                } else {
                    throw new LocalizedException(__('Empty param %1', $paramToUpdate));
                }
            }
            $params[$paramToUpdate] = $val;
        }

        return $params;
    }
}
