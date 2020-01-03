<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrdersGrid\Plugin;

use MageWorx\OrdersGrid\Model\Synchronizer;

/**
 * Class Synchronize
 */
class Synchronize extends Synchronizer
{
    /**
     * Synchronize each available object
     *
     * @param $subject
     * @param $proceed
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    public function aroundSave($subject, $proceed, \Magento\Framework\Model\AbstractModel $object)
    {
        $returnValue = $proceed($object);

        foreach (static::CLASS_MAPPER as $className => $method) {
            if ($object instanceof $className) {
                try {
                    $this->{$method}($object);
                } catch (\Exception $exception) {
                    $this->logger->critical($exception->getMessage() . '  ' . $exception->getTraceAsString());
                    continue;
                }
            }
        }

        return $returnValue;
    }
}