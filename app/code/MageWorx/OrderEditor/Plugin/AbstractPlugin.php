<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Plugin;

use Magento\Framework\App\RequestInterface;

/**
 * Class AbstractPlugin
 */
abstract class AbstractPlugin
{
    const ORDER_EDITOR_BLOCKS = [
        'info',
        'account',
        'billing_address',
        'shipping_address',
        'order_items',
        'shipping_method',
        'payment_method'
    ];

    /**
     * Request instance
     *
     * @var RequestInterface
     */
    protected $request;

    /**
     * BeforeCollectTotalsPlugin constructor.
     *
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * @return bool
     */
    protected function isOrderEdit(): bool
    {
        $post = $this->request->getPost();

        if (!empty($post['block_id'])
            && in_array(
                $post['block_id'],
                static::ORDER_EDITOR_BLOCKS
            )
        ) {
            return true;
        }

        if (!empty($post['shipping_method'])) {
            return true;
        }

        return false;
    }
}
